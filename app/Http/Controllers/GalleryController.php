<?php
namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\ActivityLog;
use App\Jobs\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class GalleryController extends Controller
{
    use \App\Traits\OptimizesImages;

    private function cleanTiktokUrl($url) {
        if (strpos($url, 'vt.tiktok.com') !== false || strpos($url, 'vm.tiktok.com') !== false) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Prevent 504 Gateway Timeout
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            curl_exec($ch);
            $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            curl_close($ch);
        }
        return explode('?', $url)[0];
    }

    public function index(Request $request)
    {
        $type = $request->get('type', 'photo');
        if ($type === 'video') {
            $media = Gallery::whereIn('type', ['video', 'tiktok'])->where('status', 'published')->latest()->paginate(12);
        } else {
            $media = Gallery::where('type', $type)->where('status', 'published')->latest()->paginate(12);
        }
        return view('gallery.index', compact('media', 'type'));
    }

    public function adminIndex()
    {
        // Paginate to avoid 502 OOM: avoid loading all gallery rows with descriptions at once
        $media = Gallery::latest()->paginate(30);
        return view('admin.gallery', compact('media'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'type'        => 'required|in:photo,video,tiktok',
            'file'        => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,mp4,webm,ogg|max:102400',
            'youtube_url' => 'nullable|url',
            'tiktok_url'  => 'nullable|url',
            'description' => 'nullable|string',
            'status'      => 'required|in:draft,published',
        ]);

        $fileUrl    = null;
        $youtubeUrl = null;

        $tiktokUrl  = null;

        if ($request->type == 'video' && $request->youtube_url && strpos($request->youtube_url, 'tiktok') !== false) {
            return back()->with('error', 'Link TikTok harus dimasukkan pada Tipe Media TikTok, bukan Video/YouTube.');
        }

        if ($request->type == 'photo') {
            if (!$request->hasFile('file')) return back()->with('error', 'Foto wajib diunggah.');
            $file = $request->file('file');
            
            try {
                $path = $this->optimizeAndStoreImage($file, 'gallery', 'public', 80, 1200);
            } catch (\Exception $e) {
                // Fallback
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('gallery', $fileName, 'public');
            }

            $fileUrl = '/storage/' . $path;
        } elseif ($request->type == 'tiktok') {
            if (!$request->tiktok_url) return back()->with('error', 'Wajib menyertakan link TikTok.');
            $tiktokUrl = $this->cleanTiktokUrl($request->tiktok_url);
        } else {
            if ($request->youtube_url) {
                $url = $request->youtube_url;
                if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?|live|shorts)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
                    $youtubeUrl = 'https://www.youtube.com/embed/' . $match[1];
                } else {
                    $youtubeUrl = $url;
                }
            } else {
                return back()->with('error', 'Wajib menyertakan link YouTube untuk video.');
            }
        }

        $gallery = new Gallery();
        $gallery->user_id     = auth()->id();
        $gallery->title       = $request->title;
        $gallery->type        = $request->type;
        $gallery->file_path   = $fileUrl;
        $gallery->youtube_url = $youtubeUrl;
        $gallery->tiktok_url  = ($request->type === 'tiktok') ? $tiktokUrl : null;
        $gallery->description = $request->description;
        $gallery->status      = $request->status ?? 'published';
        $gallery->save();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Create Gallery Item',
            'description' => 'Added gallery item: ' . $gallery->title . ' (Type: ' . $gallery->type . ')',
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);
        Cache::forget('welcome_data');

        return back()->with('success', 'Media berhasil ditambahkan.');
    }

    public function destroy(Gallery $gallery)
    {
        if ($gallery->file_path) {
            // Path stored as '/storage/gallery/file.jpg', strip '/storage/' prefix for disk deletion
            $relativePath = ltrim(str_replace('/storage/', '', $gallery->file_path), '/');
            Storage::disk('public')->delete($relativePath);
        }
        $title = $gallery->title;
        $gallery->delete();

        LogActivity::dispatch(
            Auth::id(),
            'Delete Gallery Item',
            'Deleted media: ' . $title,
            request()->ip(),
            request()->header('User-Agent')
        );

        return back()->with('success', 'Konten berhasil dihapus.');
    }

    public function edit(Gallery $gallery)
    {
        return response()->json($gallery);
    }

    public function update(Request $request, Gallery $gallery)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'tiktok_url'  => 'nullable|url',
            'youtube_url' => 'nullable|url',
            'file'        => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,mp4,webm,ogg|max:102400',
            'status'      => 'required|in:draft,published',
        ]);

        $data = [
            'title'       => $request->title,
            'description' => $request->description,
            'type'        => $request->type,
            'status'      => $request->status,
        ];

        if ($request->type === 'tiktok' && $request->tiktok_url) {
            $data['tiktok_url'] = $this->cleanTiktokUrl($request->tiktok_url);
        }

        if ($request->type === 'video') {
            if ($request->youtube_url) {
                $url = $request->youtube_url;
                if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?|live|shorts)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
                    $url = 'https://www.youtube.com/embed/' . $match[1];
                }
                $data['youtube_url'] = $url;
            }
        }

        if ($request->hasFile('file') && $request->type === 'photo') {
            // Delete old file if it exists
            if ($gallery->file_path) {
                $relativePath = ltrim(str_replace('/storage/', '', $gallery->file_path), '/');
                Storage::disk('public')->delete($relativePath);
            }

            $file = $request->file('file');

            try {
                $path = $this->optimizeAndStoreImage($file, 'gallery', 'public', 80, 1200);
            } catch (\Exception $e) {
                // Fallback
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('gallery', $fileName, 'public');
            }

            $data['file_path'] = '/storage/' . $path;
        }

        $gallery->update($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Update Gallery Item',
            'description' => 'Updated gallery item: ' . $gallery->title . ' (ID: ' . $gallery->id . ')',
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);
        Cache::forget('welcome_data');

        return back()->with('success', 'Media berhasil diperbarui.');
    }
}
