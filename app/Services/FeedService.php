<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use App\Models\Feed;
use App\Models\Follow;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Jobs\PushPostToFeeds;

class FeedService
{
    /**
     * Create a new post and trigger distribution
     */
    public function createPost(User $user, array $data)
    {
        $post = Post::create([
            'user_id' => $user->id,
            'content' => $data['content'],
            'image_url' => $data['image_url'] ?? null,
            'type' => $data['type'] ?? 'memory',
            'visibility' => $data['visibility'] ?? 'public',
        ]);

        // 1. Add to owner's own feed immediately
        Feed::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'score' => $this->calculateScore($post),
        ]);

        // 2. Clear owner's Redis cache
        Cache::forget("feed:user:{$user->id}");

        // 3. Dispatch Job for Fan-out to followers
        PushPostToFeeds::dispatch($post);

        return $post;
    }

    /**
     * The actual Fan-out on Write logic (to be run in background queue)
     */
    public function distributePost(Post $post)
    {
        $author = $post->user;
        
        // Get all follower IDs
        $followerIds = $author->followers()->pluck('follower_id');

        foreach ($followerIds as $followerId) {
            Feed::updateOrCreate(
                ['user_id' => $followerId, 'post_id' => $post->id],
                ['score' => $this->calculateScore($post)]
            );
            
            // Invalidate Redis cache for this follower
            Cache::forget("feed:user:{$followerId}");
        }
        
        Log::info("Post {$post->id} distributed to " . count($followerIds) . " followers.");
    }

    /**
     * Get feed for a user with Redis Caching
     */
    public function getFeed(User $user, int $perPage = 20)
    {
        $cacheKey = "feed:user:{$user->id}";

        $posts = Cache::remember($cacheKey, 1800, function () use ($user, $perPage) {
            $feedPosts = Feed::where('user_id', $user->id)
                ->with(['post.user', 'post.likes', 'post.comments'])
                ->orderBy('score', 'desc')
                ->limit($perPage)
                ->get()
                ->pluck('post');

            // SMART FEED: If feed is empty or short, add relevant posts from same major/batch/city
            if ($feedPosts->count() < 10) {
                $smartPosts = Post::where('user_id', '!=', $user->id)
                    ->where('visibility', 'public')
                    ->where(function ($q) use ($user) {
                        $q->whereHas('user', function ($uq) use ($user) {
                            $uq->where('major', $user->major)
                               ->orWhere('graduation_year', $user->graduation_year)
                               ->orWhere('city_name', $user->city_name);
                        });
                    })
                    ->whereNotIn('id', $feedPosts->pluck('id'))
                    ->with(['user', 'likes', 'comments'])
                    ->latest()
                    ->limit(10)
                    ->get();
                
                $feedPosts = $feedPosts->concat($smartPosts)->unique('id')->values();
            }

            return $feedPosts;
        });

        return $posts;
    }

    /**
     * Ranking Algorithm (Gen Z Style)
     */
    public function calculateScore(Post $post)
    {
        $likesWeight = 3;
        $commentsWeight = 5;
        
        // Base score from engagement
        $engagementScore = ($post->likes_count * $likesWeight) + ($post->comments_count * $commentsWeight);
        
        // Recency boost (Unix timestamp / 10000 to keep it manageable)
        $recencyScore = $post->created_at->timestamp / 10000;

        return $engagementScore + $recencyScore;
    }

    /**
     * Toggle Follow/Unfollow
     */
    public function toggleFollow(User $follower, User $target)
    {
        if ($follower->id === $target->id) return false;

        $existing = Follow::where('follower_id', $follower->id)
            ->where('following_id', $target->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return ['status' => 'unfollowed'];
        }

        Follow::create([
            'follower_id' => $follower->id,
            'following_id' => $target->id,
        ]);

        return ['status' => 'followed'];
    }
}
