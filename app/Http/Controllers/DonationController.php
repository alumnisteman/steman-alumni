<?php

namespace App\Http\Controllers;

use App\Models\DonationCampaign;
use App\Models\Donation;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Jobs\LogActivity;
use App\Models\AuditLog;
use Illuminate\Support\Str;

class DonationController extends Controller
{
    // Alumni: List campaigns
    public function index()
    {
        $foundationCampaigns = DonationCampaign::foundation()->where('status', 'active')->latest()->get();
        $eventCampaigns = DonationCampaign::event()->where('status', 'active')->latest()->get();
        
        $totalFoundation = Donation::where('status', 'verified')
            ->whereHas('campaign', fn($q) => $q->where('type', 'foundation'))
            ->sum('amount');
            
        $totalEvent = Donation::where('status', 'verified')
            ->whereHas('campaign', fn($q) => $q->where('type', 'event'))
            ->sum('amount');
            
        $totalDonation = $totalFoundation + $totalEvent;
        
        // Real-time feed (last 5 verified donations)
        $recentDonations = Donation::where('status', 'verified')
            ->with(['user', 'campaign'])
            ->latest()
            ->take(5)
            ->get();

        return view('donations.index', compact(
            'foundationCampaigns', 
            'eventCampaigns', 
            'totalFoundation', 
            'totalEvent', 
            'totalDonation', 
            'recentDonations'
        ));
    }

    /**
     * Mobile-First Super App UX for Alumni Fund
     */
    public function mobileFund()
    {
        $yayasan = DonationCampaign::foundation()->where('status', 'active')->first();
        $reuni = DonationCampaign::event()->where('status', 'active')->first();
        
        // Fallback for demo if none exists
        if (!$yayasan) $yayasan = (object)['id' => 1, 'collected_amount' => 0, 'progress' => 0, 'goal_amount' => 100];
        if (!$reuni) $reuni = (object)['id' => 2, 'collected_amount' => 0, 'progress' => 0, 'goal_amount' => 100];

        $stories = Story::active()->with('user')->latest()->take(10)->get()->map(function($s) {
            return (object)[
                'cover' => $s->image_url ?? ($s->user ? $s->user->profile_picture_url : 'https://ui-avatars.com/api/?name=Alumni'),
                'title' => $s->user ? $s->user->name : 'Alumni',
                'text' => $s->caption ?? $s->content ?? 'Mendukung Alumni Fund'
            ];
        });

        $donations = Donation::where('status', 'verified')->with('user')->latest()->take(10)->get();
        $funds = DonationCampaign::where('status', 'active')->get();

        return view('fund.mobile', compact('yayasan', 'reuni', 'stories', 'donations', 'funds'));
    }

    // Public Audit Page
    public function audit()
    {
        $logs = AuditLog::with('user')->latest()->paginate(20);
        $totalFunds = Donation::where('status', 'verified')->sum('amount');
        return view('donations.audit', compact('logs', 'totalFunds'));
    }

    // Campaign Detail (with distribution reports)
    public function show(DonationCampaign $campaign)
    {
        $donations = $campaign->donations()->where('status', 'verified')->with('user')->latest()->get();
        return view('donations.show', compact('campaign', 'donations'));
    }

    // Alumni: Donation Form
    public function donate(DonationCampaign $campaign)
    {
        return view('donations.form', compact('campaign'));
    }

    // Alumni: Store Donation
    public function store(Request $request, DonationCampaign $campaign)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'proof' => 'required|image|max:2048',
            'is_anonymous' => 'nullable|boolean'
        ]);

        $proofPath = null;
        if ($request->hasFile('proof')) {
            $path = $request->file('proof')->store('donations', 'public');

            // Create Donation with HASH (Blockchain-style integrity)
            $donation = Donation::create([
                'user_id' => Auth::id(),
                'donation_campaign_id' => $campaign->id,
                'amount' => $request->amount,
                'status' => 'pending',
                'proof_path' => $path,
                'is_anonymous' => $request->has('is_anonymous'),
            ]);

            $hash = hash('sha256', $donation->id . $donation->amount . $donation->created_at . Str::random(10));
            $donation->update(['hash' => $hash]);

            // Create Immutable Audit Log
            AuditLog::create([
                'action' => 'donation_created',
                'user_id' => Auth::id(),
                'meta' => $donation->toArray(),
                'hash' => $hash
            ]);
        }

        LogActivity::dispatch(
            Auth::id(),
            'Submit Donation',
            'Submitted donation for: ' . $campaign->title,
            $request->ip(),
            $request->header('User-Agent')
        );

        return redirect()->route('donations.index')->with('success', 'Terima kasih! Donasi Anda telah kami terima dan sedang menunggu verifikasi Admin.');
    }

    // Admin: List all donations
    public function adminIndex(Request $request)
    {
        $query = Donation::with(['user', 'campaign'])->latest();
        
        // Filter by fund type
        if ($request->filled('type')) {
            $query->whereHas('campaign', fn($q) => $q->where('type', $request->type));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $donations = $query->paginate(20);
        
        $stats = [
            'pending'    => Donation::where('status', 'pending')->count(),
            'foundation' => Donation::where('status', 'verified')->whereHas('campaign', fn($q) => $q->where('type', 'foundation'))->sum('amount'),
            'event'      => Donation::where('status', 'verified')->whereHas('campaign', fn($q) => $q->where('type', 'event'))->sum('amount'),
        ];
        
        return view('admin.donations.index', compact('donations', 'stats'));
    }

    // Admin: Verify Donation
    public function verify(Request $request, Donation $donation)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
            'admin_notes' => 'nullable|string'
        ]);

        $oldStatus = $donation->status;
        $donation->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes
        ]);

        // If verified, add to campaign current_amount
        if ($request->status == 'verified' && $oldStatus != 'verified') {
            $donation->campaign->increment('current_amount', $donation->amount);
        }
        // If un-verified (changed from verified), deduct
        if ($oldStatus == 'verified' && $request->status == 'rejected') {
            $donation->campaign->decrement('current_amount', $donation->amount);
        }

        // Create Immutable Audit Log for Verification
        AuditLog::create([
            'action' => 'donation_' . $request->status,
            'user_id' => Auth::id(),
            'meta' => [
                'donation_id' => $donation->id,
                'fund_type'   => $donation->campaign->type ?? null,
                'status' => $request->status,
                'admin_notes' => $request->admin_notes,
                'verified_by' => Auth::user()->name
            ],
            'hash' => $donation->hash
        ]);

        LogActivity::dispatch(
            Auth::id(),
            'Verify Donation',
            'Verified donation #' . $donation->id . ' to ' . $request->status,
            $request->ip(),
            $request->header('User-Agent')
        );

        return back()->with('success', 'Status donasi berhasil diperbarui.');
    }

    // ─────────────────────────────────────────────────────────────────────
    // Admin: Campaign (Fund) Management
    // ─────────────────────────────────────────────────────────────────────

    public function campaignIndex()
    {
        $foundationCampaigns = DonationCampaign::foundation()->withCount('donations')->latest()->get();
        $eventCampaigns = DonationCampaign::event()->withCount('donations')->latest()->get();
        return view('admin.campaigns.index', compact('foundationCampaigns', 'eventCampaigns'));
    }

    public function campaignCreate()
    {
        return view('admin.campaigns.create');
    }

    public function campaignStore(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'type'         => 'required|in:foundation,event',
            'description'  => 'required|string',
            'bank_info'    => 'nullable|string',
            'goal_amount'  => 'required|numeric|min:0',
            'end_date'     => 'nullable|date',
            'status'       => 'required|in:active,completed,cancelled',
            'is_featured'  => 'nullable|boolean',
            'image'        => 'nullable|image|max:2048',
        ]);

        $data['slug'] = Str::slug($request->title) . '-' . time();
        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('campaigns', 'public');
        }

        DonationCampaign::create($data);

        return redirect()->route('admin.campaigns.index')->with('success', 'Fund berhasil dibuat.');
    }

    public function campaignEdit(DonationCampaign $campaign)
    {
        return view('admin.campaigns.edit', compact('campaign'));
    }

    public function campaignUpdate(Request $request, DonationCampaign $campaign)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'type'         => 'required|in:foundation,event',
            'description'  => 'required|string',
            'bank_info'    => 'nullable|string',
            'goal_amount'  => 'required|numeric|min:0',
            'end_date'     => 'nullable|date',
            'status'       => 'required|in:active,completed,cancelled',
            'is_featured'  => 'nullable|boolean',
            'image'        => 'nullable|image|max:2048',
        ]);

        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            if ($campaign->image) Storage::disk('public')->delete($campaign->image);
            $data['image'] = $request->file('image')->store('campaigns', 'public');
        }

        $campaign->update($data);

        return redirect()->route('admin.campaigns.index')->with('success', 'Fund berhasil diperbarui.');
    }

    public function campaignDestroy(DonationCampaign $campaign)
    {
        if ($campaign->image) Storage::disk('public')->delete($campaign->image);
        $campaign->delete();

        return redirect()->route('admin.campaigns.index')->with('success', 'Fund berhasil dihapus.');
    }
}
