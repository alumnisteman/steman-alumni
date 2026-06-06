<?php
namespace App\Http\Controllers;

use App\Models\News;
use App\Models\ActivityLog;
use App\Support\WelcomeCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Jobs\LogActivity;

class NewsController extends Controller
{
    use \App\Traits\OptimizesImages;

    // Public Views
    public function index(Request $request, \App\Services\NewsAggregator $agg, \App\Services\TrendingService $trendService)
    {
        $query = News::published();
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
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
            $query->published();
        }

        $item = $query->firstOrFail();

        // Get related news (same category, excluding current item)
        $related = News::published()
            ->where('category', $item->category)
            ->where('id', '!=', $item->id)
            ->latest()
            ->limit(4)
            ->get();

        return view('news.show', compact('item', 'related'));
    }

    // Admin Views
    public function adminIndex()
    {
        // Select only columns needed for the list; skip 'content' (large rich text) to avoid 502 OOM crash
        $news = News::latest()
            ->select('id', 'title', 'slug', 'category', 'thumbnail', 'status', 'is_published', 'created_at')
            ->paginate(20);

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
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'category'  => 'required'
        ]);

        $path = null;
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            
            try {
                $path = $this->optimizeAndStoreImage($file, 'news', 'public', 70, 1200);
            } catch (\Exception $e) {
                \Log::warning('News thumbnail optimization failed, using original: ' . $e->getMessage());
                $imageName = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('news', $imageName, 'public');
            }
            if (!str_starts_with($path, '/storage/')) {
                $path = '/storage/' . ltrim($path, '/');
            }
        }

        $news = News::create(array_merge([
            'user_id'   => auth()->id(),
            'title'     => $request->title,
            'content'   => $request->content,
            'category'  => $request->category,
            'thumbnail' => $path,
        ], News::publishAttributes($request->has('status'))));

        LogActivity::dispatch(
            Auth::id(),
            'Create News',
            'Published news: ' . $news->title,
            $request->ip(),
            $request->header('User-Agent')
        );
        WelcomeCache::forget();

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil diterbitkan.');
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $request->validate([
            'title'     => 'required|max:255',
            'content'   => 'required',
            'category'  => 'required',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
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
                \Log::warning('News thumbnail optimization failed, using original: ' . $e->getMessage());
                $imageName = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $optimizedPath = $file->storeAs('news', $imageName, 'public');
            }
            
            $path = str_starts_with($optimizedPath, '/storage/') ? $optimizedPath : '/storage/' . ltrim($optimizedPath, '/');
        }

        $news->update(array_merge([
            'title'     => $request->title,
            'content'   => $request->content,
            'category'  => $request->category,
            'thumbnail' => $path,
        ], News::publishAttributes($request->has('status'))));

        LogActivity::dispatch(
            Auth::id(),
            'Update News',
            'Updated news: ' . $news->title,
            $request->ip(),
            $request->header('User-Agent')
        );
        WelcomeCache::forget();

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(News $news)
    {
        if ($news->thumbnail) {
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
        WelcomeCache::forget();

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil dihapus.');
    }

    public function togglePublish(News $news)
    {
        $published = !$news->isPublished();
        $news->update(News::publishAttributes($published));
        
        LogActivity::dispatch(
            Auth::id(),
            ($published ? 'Publish' : 'Unpublish') . ' News',
            ($published ? 'Published' : 'Unpublished') . ' news: ' . $news->title,
            request()->ip(),
            request()->header('User-Agent')
        );
        WelcomeCache::forget();
        
        $message = $published ? 'Berita berhasil diterbitkan!' : 'Berita berhasil dikembalikan ke draft.';
        return back()->with('success', $message);
    }
}
