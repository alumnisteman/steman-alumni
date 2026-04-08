<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Jobs\LogActivity;

class AIController extends Controller
{
    public function dashboard()
    {
        $aiNewsCount = News::where('category', 'AI Generated')->count();
        $aiDrafts = News::where('category', 'AI Generated')->where('status', 'draft')->latest()->get();
        
        // Mock data for usage stats (or implement real tracking in AIService)
        $stats = [
            'total_requests' => 45, // In development
            'spam_prevented' => 12,
            'news_drafts' => $aiNewsCount,
        ];

        return view('admin.ai.dashboard', compact('aiDrafts', 'stats'));
    }

    public function generateNow()
    {
        // Ensure API Key is configured via Setting (DB) or Config (.env fallback)
        if (empty(setting('gemini_api_key')) && empty(config('services.gemini.api_key'))) {
            return back()->with('error', 'Gagal: GEMINI_API_KEY belum dikonfigurasi pada CMS Settings atau environment file (.env).');
        }

        Artisan::call('ai:generate-news');
        $output = Artisan::output();

        if (str_contains(strtolower($output), 'failed') || str_contains(strtolower($output), 'error')) {
            return back()->with('error', 'AI gagal memberikan respon. Pastikan kuota API masih tersedia dan koneksi lancar.');
        }

        return back()->with('success', 'Berita AI berhasil di-generate sebagai draft!');
    }

    public function publish(News $news)
    {
        $news->update(['status' => 'published']);

        LogActivity::dispatch(
            Auth::id(),
            'Publish AI News',
            'Published AI news: ' . $news->title,
            request()->ip(),
            request()->header('User-Agent')
        );
        Cache::forget('welcome_data');

        return back()->with('success', 'Berita kebanggaan alumni berhasil diterbitkan!');
    }
}
