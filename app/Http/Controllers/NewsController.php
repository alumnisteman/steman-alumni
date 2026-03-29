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
        $query = News::where('is_published', true);
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
        }
        $news = $query->latest()->paginate(9)->withQueryString();
        return view('news.index', compact('news'));
    }

    public function show($slug)
    {
        $item = News::where('slug', $slug)->where('is_published', true)->firstOrFail();
        return view('news.show', compact('item'));
    }

    // Admin Views
    public function adminIndex()
    {
        // Select only columns needed for the list; skip 'content' (large rich text) to avoid 502 OOM crash
        $news = News::latest()->select('id', 'title', 'slug', 'category', 'thumbnail', 'is_published', 'created_at')->paginate(20);
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
            $storedPath = $request->file('thumbnail')->store('news', 'public');
            $path = '/storage/' . $storedPath;
        }

        $news = News::create([
            'user_id'      => auth()->id(),
            'title'        => $request->title,
            'content'      => $request->content,
            'category'     => $request->category,
            'thumbnail'    => $path,
            'is_published' => $request->has('is_published')
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Create News',
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
                // Path is stored as '/storage/news/file.jpg', strip '/storage/' prefix for disk delete
                $relativePath = ltrim(str_replace('/storage/', '', $path), '/');
                Storage::disk('public')->delete($relativePath);
            }
            $storedPath = $request->file('thumbnail')->store('news', 'public');
            $path = '/storage/' . $storedPath;
        }

        $news->update([
            'title'        => $request->title,
            'content'      => $request->content,
            'category'     => $request->category,
            'thumbnail'    => $path,
            'is_published' => $request->has('is_published')
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Update News',
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
            'activity' => 'Delete News',
            'description' => 'Deleted news: ' . $news_title,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);
        Cache::forget('welcome_data');

        return back()->with('success', 'Berita berhasil dihapus.');
    }
}
