<?php
namespace App\Http\Controllers;

use App\Models\News;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Jobs\LogActivity;

class NewsController extends Controller
{
    use \App\Traits\OptimizesImages;

    // Public Views
    public function index(Request $request, \App\Services\NewsAggregator $agg, \App\Services\TrendingService $trendService)
    {
        $query = News::where('status', 'published');
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
        }
        $news = $query->latest()->paginate(9)->withQueryString();
        
        $aggregatedNews = collect($agg->get());
        $trending = collect($trendService->getTrendingKeywords());

        return view('news.index', compact('news', 'aggregatedNews', 'trending'));
    }

    public function show($slug)
    {
        $query = News::where('slug', $slug);
        
        // If not published, only allow admin/editor to see it (Preview mode)
        if (!auth()->check() || (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('editor'))) {
            $query->where('status', 'published');
        }

        $item = $query->firstOrFail();
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
            
            try {
                $path = $this->optimizeAndStoreImage($file, 'news', 'public', 70, 1200);
            } catch (\Exception $e) {
                $imageName = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('news', $imageName, 'public');
            }
            // Ensure the path starts with /storage/ to match existing DB conventions
            if (!str_starts_with($path, '/storage/')) {
                $path = '/storage/' . $path;
            }
        }

        $news = News::create([
            'user_id'      => auth()->id(),
            'title'        => $request->title,
            'content'      => $request->content,
            'category'     => $request->category,
            'thumbnail'    => $path,
            'status'       => $request->status ?? 'draft'
        ]);

        LogActivity::dispatch(
            Auth::id(),
            'Create News',
            'Published news: ' . $news->title,
            $request->ip(),
            $request->header('User-Agent')
        );
        Cache::forget('welcome_data');

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil diterbitkan.');
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
            
            try {
                $optimizedPath = $this->optimizeAndStoreImage($file, 'news', 'public', 70, 1200);
            } catch (\Exception $e) {
                $imageName = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $optimizedPath = $file->storeAs('news', $imageName, 'public');
            }
            
            $path = str_starts_with($optimizedPath, '/storage/') ? $optimizedPath : '/storage/' . $optimizedPath;
        }

        $news->update([
            'title'        => $request->title,
            'content'      => $request->content,
            'category'     => $request->category,
            'thumbnail'    => $path,
            'status'       => $request->status ?? 'draft'
        ]);

        LogActivity::dispatch(
            Auth::id(),
            'Update News',
            'Updated news: ' . $news->title,
            $request->ip(),
            $request->header('User-Agent')
        );
        Cache::forget('welcome_data');

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil diperbarui.');
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

        LogActivity::dispatch(
            Auth::id(),
            'Delete News',
            'Deleted news: ' . $news_title,
            request()->ip(),
            request()->header('User-Agent')
        );
        Cache::forget('welcome_data');

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil dihapus.');
    }

    public function togglePublish(News $news)
    {
        $newStatus = ($news->status === 'published') ? 'draft' : 'published';
        $news->update(['status' => $newStatus]);
        
        LogActivity::dispatch(
            Auth::id(),
            ($newStatus === 'published' ? 'Publish' : 'Unpublish') . ' News',
            ($newStatus === 'published' ? 'Published' : 'Unpublished') . ' news: ' . $news->title,
            request()->ip(),
            request()->header('User-Agent')
        );
        Cache::forget('welcome_data');
        
        $message = ($newStatus === 'published' ? 'Berita berhasil diterbitkan!' : 'Berita berhasil dikembalikan ke draft.');
        return back()->with('success', $message);
    }
}
