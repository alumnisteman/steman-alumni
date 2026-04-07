<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Like;
use App\Models\Comment;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['user', 'likes', 'comments.user', 'taggedUsers'])->latest();

        // Filter by angkatan if requested
        if ($request->has('angkatan') && $request->angkatan != '') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('tahun_lulus', $request->angkatan);
            });
        }

        $posts = $query->paginate(10);
        $userPostsCount = Post::where('user_id', Auth::id())->count();
        $angkatanList = User::distinct()->whereNotNull('tahun_lulus')->orderBy('tahun_lulus', 'desc')->pluck('tahun_lulus');

        return view('alumni.nostalgia', compact('posts', 'userPostsCount', 'angkatanList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'image' => 'nullable|image|max:5120', // Max 5MB
            'type' => 'required|in:memory,story,event',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:users,id',
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts', 'public');
            $imageUrl = Storage::url($path);
        }

        $post = Post::create([
            'user_id' => Auth::id(),
            'content' => $request->content,
            'image_url' => $imageUrl,
            'type' => $request->type,
        ]);

        // Handle Tags
        if ($request->has('tags')) {
            foreach ($request->tags as $taggedUserId) {
                $post->tags()->create([
                    'tagged_user_id' => $taggedUserId,
                ]);

                // Send notification to tagged user
                $taggedUser = User::find($taggedUserId);
                if ($taggedUser) {
                    $taggedUser->notify(new \App\Notifications\TaggedInPostNotification($post, Auth::user()));
                }
            }
        }

        // Award Points
        Auth::user()->awardPoints(20);

        return back()->with('success', 'Postingan nostalgia berhasil dibagikan! +20 poin untuk Anda.');
    }

    public function destroy(Post $post)
    {
        if ($post->user_id != Auth::id() && !in_array(Auth::user()->role, ['admin', 'editor'])) {
            abort(403);
        }

        if ($post->image_url) {
            // Ensure we extract the relative path correctly from a full URL or relative URL
            // $post->image_url = https://alumni-steman.my.id/storage/posts/xxx.png
            $parsedUrl = parse_url($post->image_url, PHP_URL_PATH); // /storage/posts/xxx.png
            if ($parsedUrl) {
                $imagePath = preg_replace('#^/storage/#', '', $parsedUrl); // posts/xxx.png
                Storage::disk('public')->delete($imagePath);
            }
        }

        $post->delete();

        return back()->with('success', 'Kenangan berhasil dihapus');
    }

    public function toggleLike(Post $post)
    {
        $like = $post->likes()->where('user_id', Auth::id())->first();

        if ($like) {
            $like->delete();
            $post->decrement('likes_count');
            $status = 'unliked';
        } else {
            $post->likes()->create([
                'user_id' => Auth::id(),
            ]);
            $post->increment('likes_count');
            $status = 'liked';
            
            // Award points for receiving a like
            $post->user->awardPoints(1);
        }

        return response()->json([
            'status' => $status,
            'likes_count' => $post->likes_count,
        ]);
    }

    public function storeComment(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $post->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        $post->increment('comments_count');

        // Award Points
        Auth::user()->awardPoints(5);

        return back()->with('success', 'Komentar ditambahkan! +5 poin untuk Anda.');
    }

    public function searchAlumni(Request $request)
    {
        $search = $request->get('q');
        $alumni = User::where('name', 'LIKE', "%{$search}%")
            ->where('role', 'alumni')
            ->where('id', '!=', Auth::id())
            ->limit(10)
            ->get(['id', 'name', 'tahun_lulus']);

        return response()->json($alumni);
    }
}
