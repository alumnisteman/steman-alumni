<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Models\User;
use App\Models\StoryView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class StoryController extends Controller
{
    /**
     * Redirect to feed
     */
    public function index()
    {
        return redirect()->route('feed.index');
    }

    /**
     * Store a new story (Valid for 24 hours)
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'image' => 'nullable|image|max:10240',
                'caption' => 'nullable|string|max:100',
                'content' => 'nullable|string|max:60', // Allow notes via this method
                'spotify_url' => 'nullable|url',
            ]);

            if (!$request->hasFile('image') && !$request->spotify_url && !$request->content) {
                return back()->with('error', 'Pilih gambar, masukkan link Spotify, atau tulis catatan.');
            }

            $imageUrl = null;
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('stories', 'public');
                $imageUrl = Storage::url($path);
            }

            $mediaUrl = $request->spotify_url;
            if ($mediaUrl) {
                // 1. Spotify Conversion
                if (str_contains($mediaUrl, 'open.spotify.com/')) {
                    if (!str_contains($mediaUrl, 'open.spotify.com/embed/')) {
                        $mediaUrl = str_replace('open.spotify.com/', 'open.spotify.com/embed/', $mediaUrl);
                    }
                } 
                // 2. YouTube Conversion (Long Link)
                elseif (str_contains($mediaUrl, 'youtube.com/watch?v=')) {
                    $videoId = explode('v=', $mediaUrl)[1];
                    $videoId = explode('&', $videoId)[0];
                    $mediaUrl = "https://www.youtube.com/embed/{$videoId}?autoplay=1&rel=0";
                }
                // 3. YouTube Short Link (youtu.be)
                elseif (str_contains($mediaUrl, 'youtu.be/')) {
                    $videoId = explode('youtu.be/', $mediaUrl)[1];
                    $videoId = explode('?', $videoId)[0];
                    $mediaUrl = "https://www.youtube.com/embed/{$videoId}?autoplay=1&rel=0";
                }
                // 4. YouTube Music
                elseif (str_contains($mediaUrl, 'music.youtube.com/watch?v=')) {
                    $videoId = explode('v=', $mediaUrl)[1];
                    $videoId = explode('&', $videoId)[0];
                    $mediaUrl = "https://www.youtube.com/embed/{$videoId}?autoplay=1";
                }
            }
            $spotifyUrl = $mediaUrl;

            // Default image for Spotify-only stories if no image is uploaded
            if (!$imageUrl && $spotifyUrl) {
                $imageUrl = 'https://images.unsplash.com/photo-1614680376593-902f74cf0d41?q=80&w=1000&auto=format&fit=crop'; // Artistic music background
            }

            // Determine type
            $type = 'image';
            if ($request->content && !$request->hasFile('image') && !$request->spotify_url) {
                $type = 'note';
            } elseif ($spotifyUrl) {
                $type = 'spotify';
            }

            Story::create([
                'user_id' => auth()->id(),
                'type' => $type,
                'content' => $request->content,
                'image_url' => $imageUrl,
                'spotify_url' => $spotifyUrl,
                'caption' => $request->caption,
                'expires_at' => now()->addHours(24),
            ]);

            // Notify followers (Gen-Z Alert System)
            try {
                $user = auth()->user();
                $followers = $user->followers;
                if ($followers->count() > 0) {
                    \Illuminate\Support\Facades\Notification::send($followers, new \App\Notifications\NewStoryNotification($user, $type));
                }
            } catch (\Throwable $notifError) {
                \Illuminate\Support\Facades\Log::warning('Failed to send story notifications: ' . $notifError->getMessage());
            }

            return redirect()->back()->with('success', 'Story berhasil diposting! Akan hilang dalam 24 jam.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Story Store Error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal memposting story: ' . $e->getMessage());
        }
    }

    public function storeNote(Request $request)
    {
        try {
            $request->validate([
                'content' => 'required|string|max:60',
                'caption' => 'nullable|string|max:20', // Mood name
            ]);

            $story = Story::create([
                'user_id' => auth()->id(),
                'type' => 'note',
                'content' => $request->content,
                'caption' => $request->caption ?? 'default',
                'expires_at' => now()->addHours(24),
            ]);

            // Notify followers (Mood Update Alert)
            try {
                $user = auth()->user();
                $followers = $user->followers;
                if ($followers->count() > 0) {
                    \Illuminate\Support\Facades\Notification::send($followers, new \App\Notifications\NewStoryNotification($user, 'note'));
                }
            } catch (\Throwable $notifError) {
                \Illuminate\Support\Facades\Log::warning('Failed to send note notifications: ' . $notifError->getMessage());
            }

            return back()->with('success', 'Catatan dibagikan!');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Story Note Store Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal membagikan catatan.');
        }
    }

    /**
     * Handle shared story links
     */
    public function showShared(Story $story)
    {
        if ($story->expires_at < now()) {
            return redirect('/feed')->with('error', 'Story ini sudah kadaluarsa atau dihapus.');
        }

        // We can just redirect to the feed and pass a parameter to auto-open it via JS if we want,
        // or just let them see the feed. For a seamless Gen-Z experience, let's pass a query param.
        return redirect()->route('feed.index', ['open_story' => $story->user_id]);
    }

    /**
     * View a specific story (API)
     */
    public function show(Story $story)
    {
        try {
            if ($story->expires_at < now()) {
                return response()->json(['error' => 'Story sudah kadaluarsa.'], 410);
            }

            $story->increment('views_count');

            // Null-safe user data
            $userName = $story->user ? $story->user->name : 'Alumni';
            $userAvatar = $story->user ? $story->user->profile_picture_url : 'https://ui-avatars.com/api/?name=Alumni';

            return response()->json([
                'id' => $story->id,
                'user' => [
                    'name' => $userName,
                    'avatar' => $userAvatar,
                ],
                'type' => $story->type,
                'image_url' => $story->image_url,
                'spotify_url' => $story->spotify_url,
                'caption' => $story->caption,
                'created_at_human' => $story->created_at->diffForHumans(),
                'created_at' => $story->created_at,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Story Show API Error: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan sistem.'], 500);
        }
    }

    /**
     * Get active stories for the feed (Grouped by User)
     */
    public function getActiveStories()
    {
        try {
            $stories = Story::active()
                ->with('user')
                ->whereHas('user') // Ensure user exists
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($story) {
                    $story->created_at_human = $story->created_at->diffForHumans();
                    
                    // Ensure absolute URL for images
                    if ($story->image_url && !str_starts_with($story->image_url, 'http')) {
                        $story->image_url = url($story->image_url);
                    }

                    // Fallback avatar/name for security
                    if ($story->user) {
                        $story->user->name = $story->user->name ?? 'Alumni';
                        $story->user->profile_picture_url = $story->user->profile_picture_url ?? 'https://ui-avatars.com/api/?name=Alumni';
                    }
                    
                    return $story;
                })
                ->groupBy('user_id');

            return response()->json($stories);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('GetActiveStories API Error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    /**
     * Record a view for a story
     */
    public function view(Request $request)
    {
        $request->validate([
            'story_id' => 'required|exists:stories,id'
        ]);

        StoryView::firstOrCreate([
            'story_id' => $request->story_id,
            'viewer_id' => Auth::id(),
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Get list of viewers for a story (Owner only)
     */
    public function viewers($id)
    {
        $story = Story::findOrFail($id);

        if ($story->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $viewers = StoryView::with('viewer')
            ->where('story_id', $id)
            ->latest()
            ->get();

        return response()->json($viewers);
    }
}
