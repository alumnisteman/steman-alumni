<?php
namespace App\Http\Controllers;

use App\Models\News;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class NewsController extends Controller
{
    // Public Views
    public function index(Request $request)
    {
        $query = News::where('status', 'published');
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
        }
        $news = $query->latest()->paginate(9)->withQueryString();
        return view('news.index', compact('news'));
    }

    public function show($slug)
    {
        $item = News::where('slug', $slug)->where('status', 'published')->firstOrFail();
        return view('news.show', compact('item'));
    }

    // Admin Views
    public function adminIndex()
    {
        // Select only columns needed for the list; skip 'content' (large rich text) to avoid 502 OOM crash
        $news = News::latest()->select('id', 'title', 'slug', 'category', 'thumbnail', 'status', 'created_at')->paginate(20);
        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'required|max:255',
            'content'   => 'required',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'category'  => 'required'
        ]);

        $path = null;
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $imageName = Str::random(40) . '.webp';
            
            if (class_exists(\Intervention\Image\ImageManager::class)) {
                $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                $image = $manager->read($file->getRealPath());
                $encoded = $image->toWebp(70); // 70% quality compresses it < 200kb usually
                Storage::disk('public')->put('news/' . $imageName, (string) $encoded);
            } else {
                $imageName = $file->hashName();
                $file->storeAs('news', $imageName, 'public');
            }
            $path = '/storage/news/' . $imageName;
        }

        $news = News::create([
            'user_id'      => auth()->id(),
            'title'        => $request->title,
            'content'      => $request->content,
            'category'     => $request->category,
            'thumbnail'    => $path,
            'status'       => $request->status ?? 'draft'
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Create News',
            'description' => 'Published news: ' . $news->title,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);
        Cache::forget('welcome_data');

        return redirect('/admin/news')->with('success', 'Berita berhasil diterbitkan.');
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $request->validate([
            'title'    => 'required|max:255',
            'content'  => 'required',
            'category' => 'required'
        ]);

        $path = $news->thumbnail;
        if ($request->hasFile('thumbnail')) {
            if ($path) {
                $relativePath = ltrim(str_replace('/storage/', '', $path), '/');
                Storage::disk('public')->delete($relativePath);
            }
            
            $file = $request->file('thumbnail');
            $imageName = Str::random(40) . '.webp';
            
            if (class_exists(\Intervention\Image\ImageManager::class)) {
                $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                $image = $manager->read($file->getRealPath());
                $encoded = $image->toWebp(70);
                Storage::disk('public')->put('news/' . $imageName, (string) $encoded);
            } else {
                $imageName = $file->hashName();
                $file->storeAs('news', $imageName, 'public');
            }
            $path = '/storage/news/' . $imageName;
        }

        $news->update([
            'title'        => $request->title,
            'content'      => $request->content,
            'category'     => $request->category,
            'thumbnail'    => $path,
            'status'       => $request->status ?? 'draft'
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Update News',
            'description' => 'Updated news: ' . $news->title,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);
        Cache::forget('welcome_data');

        return redirect('/admin/news')->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(News $news)
    {
        if ($news->thumbnail) {
            // Path is stored as '/storage/news/file.jpg', strip '/storage/' prefix for disk delete
            $relativePath = ltrim(str_replace('/storage/', '', $news->thumbnail), '/');
            Storage::disk('public')->delete($relativePath);
        }
        $news_title = $news->title;
        $news->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Delete News',
            'description' => 'Deleted news: ' . $news_title,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);
        Cache::forget('welcome_data');

        return back()->with('success', 'Berita berhasil dihapus.');
    }
}
