<?php

namespace App\Http\Controllers;

use App\Models\Podcast;
use Illuminate\Http\Request;

class PodcastController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('category');
        
        $query = Podcast::where('is_published', true);
        
        if ($category) {
            $query->where('category', $category);
        }
        
        $podcasts = $query->latest()->paginate(12);
        
        return view('podcasts.index', compact('podcasts'));
    }

    public function show($slug)
    {
        $podcast = Podcast::where('slug', $slug)->where('is_published', true)->firstOrFail();
        return view('podcasts.show', compact('podcast'));
    }
}
