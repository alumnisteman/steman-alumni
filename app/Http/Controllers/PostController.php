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
use App\Jobs\LogActivity;
use App\Jobs\ModerateContentWithAI;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['user', 'likes', 'comments.user', 'taggedUsers'])->latest();

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Filter by angkatan if requested
        if ($request->has('angkatan') && $request->angkatan != '') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('graduation_year', $request->angkatan);
            });
        }

        $posts = $query->paginate(10);
        $userPostsCount = Post::where('user_id', Auth::id())->count();
        $angkatanList = User::distinct()->whereNotNull('graduation_year')->orderBy('graduation_year', 'desc')->pluck('graduation_year');

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

        if (!\App\Services\ContentModerationService::isClean($request->content)) {
            return back()->withInput()->withErrors(['content' => '⚠️ PERINGATAN ADMIN: Postingan Anda telah diblokir karena mengandung kata-kata terlarang/SARA.']);
        }

        $post = Post::create([
            'user_id' => Auth::id(),
            'content' => strip_tags($request->content),
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

        LogActivity::dispatch(
            Auth::id(),
            'Create Alumni Post',
            'Alumni posted a new ' . $request->type . ' on the nostalgia feed.',
            $request->ip(),
            $request->header('User-Agent')
        );

        // AI Moderation (Background)
        ModerateContentWithAI::dispatch($post);

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

        LogActivity::dispatch(
            Auth::id(),
            'Delete Alumni Post',
            'Alumni deleted a post from the nostalgia feed.',
            request()->ip(),
            request()->header('User-Agent')
        );

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

        if (!\App\Services\ContentModerationService::isClean($request->content)) {
            return back()->withInput()->withErrors(['content' => '⚠️ TEGURAN ADMIN: Komentar Anda melanggar aturan penggunaan kata-kata di portal ini.']);
        }

        $comment = $post->comments()->create([
            'user_id' => Auth::id(),
            'content' => strip_tags($request->content),
        ]);

        $post->increment('comments_count');

        // AI Moderation
        \App\Jobs\ModerateContentWithAI::dispatch($comment);

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
            ->get(['id', 'name', 'graduation_year']);

        return response()->json($alumni);
    }
}
