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

        $stats = Cache::remember('museum_stats', 600, fn () => [
            'total'       => MuseumItem::approved()->count(),
            'categories'  => MuseumItem::approved()->selectRaw('category, count(*) as cnt')->groupBy('category')->pluck('cnt', 'category'),
            'total_likes' => MuseumItemLike::count(),
        ]);

        return view('museum.index', compact('items', 'categories', 'eras', 'stats', 'category', 'era'));
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
            'image'       => 'nullable|image|max:3072',
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
            'status'      => 'pending',
        ]);

        Cache::forget('museum_stats');

        return back()->with('success', 'Arsip berhasil dikirim! Menunggu persetujuan admin. Terima kasih telah berkontribusi 🏛️');
    }

    public function edit(MuseumItem $museumItem)
    {
        $this->authorizeMuseumAction($museumItem);
        $categories = MuseumItem::$categoryLabels;
        return view('museum.edit', compact('museumItem', 'categories'));
    }

    public function update(Request $request, MuseumItem $museumItem)
    {
        $this->authorizeMuseumAction($museumItem);

        $request->validate([
            'title'       => 'required|string|max:200',
            'description' => 'nullable|string|max:2000',
            'category'    => 'required|in:' . implode(',', array_keys(MuseumItem::$categoryLabels)),
            'image'       => 'nullable|image|max:3072',
            'video_url'   => 'nullable|url',
            'era_year'    => 'nullable|integer|min:1950|max:' . date('Y'),
            'donated_by'  => 'nullable|string|max:100',
        ]);

        $imagePath = $museumItem->image_url;
        if ($request->hasFile('image')) {
            if ($imagePath && !str_starts_with($imagePath, 'http')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $imagePath));
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
            $imagePath = Storage::url($filename);
        }

        $museumItem->update([
            'title'       => $request->title,
            'description' => $request->description,
            'category'    => $request->category,
            'image_url'   => $imagePath,
            'video_url'   => $request->video_url,
            'era_year'    => $request->era_year,
            'donated_by'  => $request->donated_by,
            'status'      => 'pending',
        ]);

        Cache::forget('museum_stats');

        return redirect()->route('museum.show', $museumItem)
            ->with('success', 'Arsip diperbarui! Menunggu persetujuan ulang admin.');
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

    public function adminIndex()
    {
        $items = MuseumItem::with('uploader')
            ->orderByRaw("FIELD(status,'pending','approved','rejected')")
            ->latest()
            ->paginate(20);
        $categories = MuseumItem::$categoryLabels;
        return view('admin.museum.index', compact('items', 'categories'));
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
        $this->authorizeMuseumAction($museumItem);

        if ($museumItem->image_url && !str_starts_with($museumItem->image_url, 'http')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $museumItem->image_url));
        }
        $museumItem->delete();
        Cache::forget('museum_stats');
        return redirect()->route('museum.index')->with('success', 'Arsip dihapus.');
    }

    private function authorizeMuseumAction(MuseumItem $museumItem): void
    {
        $user = auth()->user();
        if (!$user) {
            abort(403, 'Login diperlukan.');
        }
        $isOwner = (int) $museumItem->uploaded_by === (int) $user->id;
        $isAdmin = in_array($user->role, ['admin', 'editor']);
        if (!$isOwner && !$isAdmin) {
            abort(403, 'Anda tidak berhak mengubah arsip ini.');
        }
    }
}
