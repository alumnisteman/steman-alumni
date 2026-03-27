<?php
namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class GalleryController extends Controller
{
    private function cleanTiktokUrl($url) {
        if (strpos($url, 'vt.tiktok.com') !== false || strpos($url, 'vm.tiktok.com') !== false) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
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
            $media = Gallery::whereIn('type', ['video', 'tiktok'])->latest()->paginate(12);
        } else {
            $media = Gallery::where('type', $type)->latest()->paginate(12);
        }
        return view('gallery.index', compact('media', 'type'));
    }

    public function adminIndex()
    {
        $media = Gallery::latest()->get();
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
        ]);

        $fileUrl    = null;
        $youtubeUrl = null;

        $tiktokUrl  = null;

        if ($request->type == 'video' && $request->youtube_url && strpos($request->youtube_url, 'tiktok') !== false) {
            return back()->with('error', 'Link TikTok harus dimasukkan pada Tipe Media TikTok, bukan Video/YouTube.');
        }

        if ($request->type == 'photo') {
            if (!$request->hasFile('file')) return back()->with('error', 'Foto wajib diunggah.');
            $path    = $request->file('file')->store('gallery', 'public');
            $fileUrl = Storage::disk('public')->url($path);
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

        $gallery = Gallery::create([
            'user_id'     => auth()->id(),
            'title'       => $request->title,
            'type'        => $request->type,
            'file_path'   => $fileUrl,
            'youtube_url' => $youtubeUrl,
            'tiktok_url'  => ($request->type === 'tiktok') ? $tiktokUrl : null,
            'description' => $request->description,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Create Gallery Item',
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
            $relativePath = str_replace(Storage::disk('public')->url(''), '', $gallery->file_path);
            Storage::disk('public')->delete(ltrim($relativePath, '/'));
        }
        $title = $gallery->title;
        $gallery->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Delete Gallery Item',
            'description' => 'Deleted gallery item: ' . $title,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);
        Cache::forget('welcome_data');

        return back()->with('success', 'Media berhasil dihapus.');
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
        ]);

        $data = [
            'title'       => $request->title,
            'description' => $request->description,
            'type'        => $request->type,
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
                $relativePath = str_replace(Storage::disk('public')->url(''), '', $gallery->file_path);
                Storage::disk('public')->delete(ltrim($relativePath, '/'));
            }
            $path              = $request->file('file')->store('gallery', 'public');
            $data['file_path'] = Storage::disk('public')->url($path);
        }

        $gallery->update($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Update Gallery Item',
            'description' => 'Updated gallery item: ' . $gallery->title . ' (ID: ' . $gallery->id . ')',
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);
        Cache::forget('welcome_data');

        return back()->with('success', 'Media berhasil diperbarui.');
    }
}
