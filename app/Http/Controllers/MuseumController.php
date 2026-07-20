<?php

namespace App\Http\Controllers;

use App\Models\MuseumItem;
use App\Models\MuseumItemLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class MuseumController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->input('category');
        $era      = $request->input('era');

        $items = MuseumItem::approved()
            ->when($category, fn ($q) => $q->where('category', $category))
            ->when($era,      fn ($q) => $q->where('era_year', $era))
            ->withCount('userLikes')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = MuseumItem::$categoryLabels;

        $eras = MuseumItem::approved()
            ->whereNotNull('era_year')
            ->distinct()
            ->orderBy('era_year')
            ->pluck('era_year');

        $stats = Cache::remember('museum_stats', 600, function() {
            // Fetch verified LPJ campaigns
            $lpjCampaigns = \App\Models\DonationCampaign::whereNotNull('lpj_pdf_path')
                ->where('report_status', 'verified')
                ->get();

            return [
                'total'       => MuseumItem::approved()->count(),
                'categories'  => MuseumItem::approved()->selectRaw('category, count(*) as cnt')->groupBy('category')->pluck('cnt', 'category'),
                'total_likes' => MuseumItemLike::count(),
                
                // LPJ Integrated Data
                'lpj_count'   => $lpjCampaigns->count() + 1,
                'lpj_expense' => $lpjCampaigns->sum('total_expense') + 230341930,
                'lpj_funds_raised' => \App\Models\Donation::where('status', 'verified')->sum('amount'),
                'lpj_list'    => array_merge([
                    [
                        'title' => 'Reuni Akbar Ke-4 Tahun 2026',
                        'slug' => 'reuni-akbar-ke-4-2026',
                        'total_expense' => 230341930,
                        'verified_at' => '2026',
                        'pdf_url' => route('pdf.view', ['f' => 'campaign-docs/LPJ_Reuni2026.pdf']),
                    ]
                ], $lpjCampaigns->map(function ($c) {
                    return [
                        'title' => $c->title,
                        'slug' => $c->slug,
                        'total_expense' => $c->total_expense,
                        'verified_at' => $c->report_verified_at ? $c->report_verified_at->format('Y') : null,
                        'pdf_url' => $c->lpj_pdf_path ? \Illuminate\Support\Facades\Storage::url($c->lpj_pdf_path) : null,
                    ];
                })->toArray()),
            ];
        });

        $principals = \App\Models\Principal::orderBy('sort_order')->orderBy('period')->get();

        return view('museum.index', compact('items', 'categories', 'eras', 'stats', 'category', 'era', 'principals'));
    }

    public function show(MuseumItem $museumItem)
    {
        abort_if($museumItem->status !== 'approved', 404);
        $museumItem->increment('views');

        $isLiked = auth()->check() ? $museumItem->isLikedBy(auth()->user()) : false;
        $related = MuseumItem::approved()
            ->where('category', $museumItem->category)
            ->where('id', '!=', $museumItem->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('museum.show', compact('museumItem', 'isLiked', 'related'));
    }

    public function create()
    {
        $categories = MuseumItem::$categoryLabels;
        return view('museum.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:200',
            'description' => 'nullable|string|max:2000',
            'category'    => 'required|in:' . implode(',', array_keys(MuseumItem::$categoryLabels)),
            'image'       => 'nullable|image|max:3072', // max 3 MB
            'video_url'   => 'nullable|url',
            'era_year'    => 'nullable|integer|min:1950|max:' . date('Y'),
            'donated_by'  => 'nullable|string|max:100',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imgRes = imagecreatefromstring(file_get_contents($image->getRealPath()));

            ob_start();
            if (function_exists('imagewebp')) {
                imagewebp($imgRes, null, 80);
                $ext = 'webp';
            } else {
                imagejpeg($imgRes, null, 80);
                $ext = 'jpg';
            }
            $imgData = ob_get_clean();
            imagedestroy($imgRes);

            $filename = 'museum/' . uniqid() . '.' . $ext;
            Storage::disk('public')->put($filename, $imgData);
            $imagePath = $filename;
        }

        MuseumItem::create([
            'title'       => $request->title,
            'description' => $request->description,
            'category'    => $request->category,
            'image_url'   => $imagePath ? Storage::url($imagePath) : null,
            'video_url'   => $request->video_url,
            'era_year'    => $request->era_year,
            'donated_by'  => $request->donated_by,
            'uploaded_by' => auth()->id(),
            'status'      => 'pending', // admin must approve
        ]);

        Cache::forget('museum_stats');

        return back()->with('success', 'Arsip berhasil dikirim! Menunggu persetujuan admin. Terima kasih telah berkontribusi 🏛️');
    }

    public function toggleLike(MuseumItem $museumItem)
    {
        $user = auth()->user();
        $existing = MuseumItemLike::where('museum_item_id', $museumItem->id)
                                   ->where('user_id', $user->id)->first();

        if ($existing) {
            $existing->delete();
            $museumItem->decrement('likes');
            $liked = false;
        } else {
            MuseumItemLike::create(['museum_item_id' => $museumItem->id, 'user_id' => $user->id]);
            $museumItem->increment('likes');
            $liked = true;
        }

        Cache::forget('museum_stats');

        return response()->json(['liked' => $liked, 'total' => $museumItem->likes]);
    }

    // Admin: list pending items for approval
    public function adminIndex()
    {
        $items = MuseumItem::with('uploader')
            ->orderByRaw("FIELD(status,'pending','approved','rejected')")
            ->latest()
            ->paginate(20);
        $categories = MuseumItem::$categoryLabels;
        return view('admin.museum.index', compact('items', 'categories'));
    }

    public function update(Request $request, MuseumItem $museumItem)
    {
        abort_if(auth()->id() !== $museumItem->uploaded_by && !in_array(auth()->user()->role, ['admin', 'editor']), 403, 'Unauthorized');

        $request->validate([
            'title'       => 'required|string|max:200',
            'description' => 'nullable|string|max:2000',
            'category'    => 'required|in:' . implode(',', array_keys(MuseumItem::$categoryLabels)),
            'era_year'    => 'nullable|integer|min:1965|max:' . date('Y'),
            'image'       => 'nullable|image|max:2048',
            'video_url'   => 'nullable|url|max:255',
            'donated_by'  => 'nullable|string|max:100',
        ]);

        $data = $request->only(['title', 'description', 'category', 'era_year', 'video_url', 'donated_by']);

        if ($request->hasFile('image')) {
            if ($museumItem->image_url && !str_starts_with($museumItem->image_url, 'http')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $museumItem->image_url));
            }

            $image = $request->file('image');
            $imgRes = imagecreatefromstring(file_get_contents($image->getRealPath()));
            ob_start();
            if (function_exists('imagewebp')) {
                imagewebp($imgRes, null, 80);
                $ext = 'webp';
            } else {
                imagejpeg($imgRes, null, 80);
                $ext = 'jpg';
            }
            $imgData = ob_get_clean();
            imagedestroy($imgRes);

            $filename = 'museum/' . uniqid() . '.' . $ext;
            Storage::disk('public')->put($filename, $imgData);
            $data['image_url'] = '/storage/' . $filename;
        }

        $museumItem->update($data);
        Cache::forget('museum_stats');

        return back()->with('success', 'Arsip museum berhasil diperbarui!');
    }

    public function approve(MuseumItem $museumItem)
    {
        $museumItem->update(['status' => 'approved']);
        Cache::forget('museum_stats');
        return back()->with('success', 'Arsip disetujui dan kini tampil di museum! 🏛️');
    }

    public function reject(MuseumItem $museumItem)
    {
        $museumItem->update(['status' => 'rejected']);
        return back()->with('success', 'Arsip ditolak.');
    }

    public function destroy(MuseumItem $museumItem)
    {
        if ($museumItem->image_url && !str_starts_with($museumItem->image_url, 'http')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $museumItem->image_url));
        }
        $museumItem->delete();
        Cache::forget('museum_stats');
        return back()->with('success', 'Arsip dihapus.');
    }

    public function storePrincipal(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'period' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'status' => 'required|in:active,former',
            'sort_order' => 'required|integer',
        ]);

        $photoUrl = null;
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('principals', 'public');
            $photoUrl = Storage::url($path);
        }

        \App\Models\Principal::create([
            'name' => $request->name,
            'period' => $request->period,
            'photo_path' => $photoUrl,
            'status' => $request->status,
            'sort_order' => $request->sort_order,
        ]);

        return back()->with('success', 'Foto kepala sekolah berhasil ditambahkan!');
    }

    public function updatePrincipal(Request $request, \App\Models\Principal $principal)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'period' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'status' => 'required|in:active,former',
            'sort_order' => 'required|integer',
        ]);

        $data = [
            'name' => $request->name,
            'period' => $request->period,
            'status' => $request->status,
            'sort_order' => $request->sort_order,
        ];

        if ($request->hasFile('photo')) {
            if ($principal->photo_path) {
                $oldPath = str_replace('/storage/', '', $principal->photo_path);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('photo')->store('principals', 'public');
            $data['photo_path'] = Storage::url($path);
        }

        $principal->update($data);

        return back()->with('success', 'Foto kepala sekolah berhasil diperbarui!');
    }

    public function destroyPrincipal(\App\Models\Principal $principal)
    {
        if ($principal->photo_path) {
            $oldPath = str_replace('/storage/', '', $principal->photo_path);
            Storage::disk('public')->delete($oldPath);
        }
        $principal->delete();

        return back()->with('success', 'Foto kepala sekolah berhasil dihapus!');
    }
}
