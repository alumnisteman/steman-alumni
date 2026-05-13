<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Podcast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PodcastController extends Controller
{
    public function index()
    {
        $podcasts = Podcast::latest()->paginate(10);
        return view('admin.podcasts.index', compact('podcasts'));
    }

    public function create()
    {
        return view('admin.podcasts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'guest_name' => 'required|string|max:255',
            'category' => 'required|in:career,overseas,startup',
            'description' => 'required|string',
            'audio_url' => 'required|string',
            'thumbnail_url' => 'required|string',
            'duration' => 'required|string|max:20',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . rand(1000, 9999);
        $validated['is_published'] = $request->has('is_published');
        $validated['user_id'] = Auth::id();
        
        Podcast::create($validated);
        
        Cache::forget('welcome_data_static');

        return redirect()->route('admin.podcasts.index')->with('success', 'Podcast created successfully.');
    }

    public function edit(Podcast $podcast)
    {
        return view('admin.podcasts.edit', compact('podcast'));
    }

    public function update(Request $request, Podcast $podcast)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'guest_name' => 'required|string|max:255',
            'category' => 'required|in:career,overseas,startup',
            'description' => 'required|string',
            'audio_url' => 'required|string',
            'thumbnail_url' => 'required|string',
            'duration' => 'required|string|max:20',
        ]);

        if ($podcast->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . rand(1000, 9999);
        }
        $validated['is_published'] = $request->has('is_published');

        $podcast->update($validated);
        
        Cache::forget('welcome_data_static');

        return redirect()->route('admin.podcasts.index')->with('success', 'Podcast updated successfully.');
    }

    public function destroy(Podcast $podcast)
    {
        $podcast->delete();
        Cache::forget('welcome_data_static');
        return redirect()->route('admin.podcasts.index')->with('success', 'Podcast deleted successfully.');
    }
}
