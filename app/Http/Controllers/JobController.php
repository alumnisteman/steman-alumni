<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Jobs\LogActivity;
use App\Mail\JobApplicationMail;

use App\Services\JobAggregatorService;

class JobController extends Controller
{
    // AI: Import from URL
    public function importAI(Request $request, JobAggregatorService $aggregator)
    {
        $request->validate(['url' => 'required|url']);
        
        $data = $aggregator->processExternalJob($request->url);
        
        if (isset($data['error'])) {
            return response()->json(['success' => false, 'message' => $data['error']], 422);
        }

        return response()->json(['success' => true, 'data' => $data]);
    }

    // Admin: List
    public function adminIndex()
    {
        $jobs = JobVacancy::latest()->paginate(10);
        return view('admin.jobs.index', compact('jobs'));
    }

    // Admin: Create
    public function create()
    {
        return view('admin.jobs.form');
    }

    // Admin: Store
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'company'       => 'required|string|max:255',
            'location'      => 'nullable|string|max:255',
            'description'   => 'nullable|string',
            'content'       => 'nullable|string',
            'external_link' => 'nullable|url',
            'type'          => 'required|string',
            'status'        => 'required|in:active,closed,draft',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('jobs', 'public');
            $data['image'] = '/storage/' . $path;
        }

        $job = JobVacancy::create($data);

        LogActivity::dispatch(
            Auth::id(),
            'Create Job Vacancy',
            'Added job: ' . $job->title . ' at ' . $job->company,
            $request->ip(),
            $request->header('User-Agent')
        );
        Cache::forget('welcome_data');

        return redirect()->route('admin.jobs.index')->with('success', 'Lowongan kerja berhasil ditambahkan.');
    }

    // Admin: Edit
    public function edit(JobVacancy $vacancy)
    {
        $job = $vacancy;
        return view('admin.jobs.form', compact('job'));
    }

    // Admin: Update
    public function update(Request $request, JobVacancy $vacancy)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'company'       => 'required|string|max:255',
            'location'      => 'nullable|string|max:255',
            'description'   => 'nullable|string',
            'content'       => 'nullable|string',
            'external_link' => 'nullable|url',
            'type'          => 'required|string',
            'status'        => 'required|in:active,closed,draft',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($request->hasFile('image')) {
            if ($vacancy->image) {
                $oldPath = str_replace('/storage/', '', $vacancy->image);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('jobs', 'public');
            $data['image'] = '/storage/' . $path;
        }

        $vacancy->update($data);

        LogActivity::dispatch(
            Auth::id(),
            'Update Job Vacancy',
            'Updated job: ' . $vacancy->title . ' at ' . $vacancy->company,
            $request->ip(),
            $request->header('User-Agent')
        );
        Cache::forget('welcome_data');

        return redirect()->route('admin.jobs.index')->with('success', 'Lowongan kerja berhasil diperbarui.');
    }

    // Admin: Delete
    public function destroy(JobVacancy $vacancy)
    {
        if ($vacancy->image) {
            $oldPath = str_replace('/storage/', '', $vacancy->image);
            Storage::disk('public')->delete($oldPath);
        }
        $title = $vacancy->title;
        $vacancy->delete();

        LogActivity::dispatch(
            Auth::id(),
            'Delete Job Vacancy',
            'Deleted job: ' . $title,
            request()->ip(),
            request()->header('User-Agent')
        );
        Cache::forget('welcome_data');

        return back()->with('success', 'Lowongan kerja berhasil dihapus.');
    }

    // Public: List
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = JobVacancy::where('status', 'active');

        // High Performance Search via Meilisearch
        if ($request->filled('search')) {
            $jobs = JobVacancy::search($request->search)
                ->where('status', 'active')
                ->paginate(12)
                ->withQueryString();
        } else {
            $query = JobVacancy::where('status', 'active');

            // Semantic Recommendation Logic
            if ($user && $request->get('tab') === 'recommended') {
                $interests = $user->major; // Can be expanded to user interests
                $query->where(function($q) use ($interests) {
                    $q->where('description', 'like', '%' . $interests . '%')
                      ->orWhere('title', 'like', '%' . $interests . '%');
                });
            }

            $jobs = $query->latest()->paginate(12)->withQueryString();
        }
        
        // Count matches for badge
        $matchCount = 0;
        if ($user && $user->major) {
            $matchCount = JobVacancy::where('status', 'active')
                ->where(function($q) use ($user) {
                    $q->where('description', 'like', '%' . $user->major . '%')
                      ->orWhere('title', 'like', '%' . $user->major . '%');
                })->count();
        }

        return view('jobs.index', compact('jobs', 'matchCount'));
    }

    // Public: Show
    public function show($slug)
    {
        $job = JobVacancy::where('slug', $slug)->firstOrFail();
        return view('jobs.show', compact('job'));
    }

    // Public: Apply (One-Click Apply)
    public function apply(Request $request, $slug)
    {
        $job = JobVacancy::where('slug', $slug)->firstOrFail();
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login untuk melamar pekerjaan.');
        }

        $request->validate([
            'cover_letter' => 'nullable|string|max:1000'
        ]);

        // Send Email to Admin/HR
        $adminEmail = setting('contact_email', 'admin@alumni-steman.my.id');
        
        try {
            Mail::to($adminEmail)->send(new JobApplicationMail($user, $job, $request->cover_letter));
            
            LogActivity::dispatch(
                $user->id,
                'Apply Job',
                'Applied for job: ' . $job->title . ' at ' . $job->company,
                $request->ip(),
                $request->header('User-Agent')
            );
            
            return back()->with('success', 'Lamaran berhasil dikirim! Resume otomatis Anda telah diteruskan ke pihak penyedia lowongan.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Job Apply Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengirim lamaran. Silakan coba beberapa saat lagi.');
        }
    }
}
