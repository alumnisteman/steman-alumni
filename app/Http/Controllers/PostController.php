<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostLike;
use App\Models\PostComment;
use App\Models\PostTag;
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
        $angkatanList = User::distinct()->whereNotNull('tahun_lulus')->orderBy('tahun_lulus', 'desc')->pluck('tahun_lulus');

        return view('alumni.nostalgia', compact('posts', 'angkatanList'));
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
                PostTag::create([
                    'post_id' => $post->id,
                    'user_id' => $taggedUserId,
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
        // Only author or admin/editor can delete
        if (Auth::id() !== $post->user_id && !in_array(Auth::user()->role, ['admin', 'editor'])) {
            abort(403);
        }

        if ($post->image_url) {
            $path = str_replace('/storage/', '', $post->image_url);
            Storage::disk('public')->delete($path);
        }

        $post->delete();

        return back()->with('success', 'Postingan telah dihapus.');
    }

    public function toggleLike(Post $post)
    {
        $like = PostLike::where('post_id', $post->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($like) {
            $like->delete();
            $post->decrement('likes_count');
            $status = 'unliked';
        } else {
            PostLike::create([
                'post_id' => $post->id,
                'user_id' => Auth::id(),
            ]);
            $post->increment('likes_count');
            $status = 'liked';
            
            // Award points for receiving a like (optional, but good for engagement)
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

        PostComment::create([
            'post_id' => $post->id,
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
