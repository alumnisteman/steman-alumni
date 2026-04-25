<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use App\Models\Feed;
use App\Models\Follow;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Jobs\PushPostToFeeds;
use App\Jobs\GenerateFeedJob;

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
            'is_anonymous' => $data['is_anonymous'] ?? false,
        ]);

        // We no longer push directly to Feed table as we use precomputed Redis feeds.
        // Instead, we just invalidate the user's feed cache and trigger a regeneration.
        try {
            Redis::del("feed:user:{$user->id}");
            GenerateFeedJob::dispatch($user->id);

            // Tell followers to regenerate their feeds
            $followerIds = $user->followers()->pluck('follower_id');
            foreach ($followerIds as $followerId) {
                Redis::del("feed:user:{$followerId}");
                GenerateFeedJob::dispatch($followerId);
            }
        } catch (\Exception $e) {
            Log::warning("Redis unavailable during post creation: " . $e->getMessage());
        }

        return $post;

        return $post;
    }

    /**
     * Legacy Fan-out (Kept for compatibility, but basically unused now)
     */
    public function distributePost(Post $post)
    {
        // Handled by GenerateFeedJob now
        Log::info("Post {$post->id} distributed via GenerateFeedJob.");
    }

    /**
     * Get feed for a user using Precomputed Redis Lists
     */
    public function getFeed(User $user, int $page = 1, int $perPage = 10)
    {
        $redisKey = "feed:user:{$user->id}";
        $start = ($page - 1) * $perPage;
        $end = $start + $perPage - 1;
        
        // Cek Redis list
        $postIds = [];
        try {
            $postIds = Redis::lrange($redisKey, $start, $end);
        } catch (\Exception $e) {
            Log::warning("Redis unavailable for feed retrieval: " . $e->getMessage());
            // Force empty postIds to trigger DB fallback below
            $postIds = [];
        }
        
        if (empty($postIds)) {
            if ($page === 1) {
                // Generate immediately for first time/expired
                try {
                    GenerateFeedJob::dispatch($user->id);
                } catch (\Exception $e) {}
                
                // Fallback while generating
                return Post::where('visibility', 'public')
                    ->where('user_id', '!=', $user->id)
                    ->with(['user', 'likes', 'comments'])
                    ->latest()
                    ->limit($perPage)
                    ->get();
            }
            return collect();
        }

        // Ambil post dari database berdasar urutan ID di Redis
        $posts = Post::whereIn('id', $postIds)
            ->with(['user', 'likes', 'comments'])
            ->get();

        // Restore urutan sesuai array ID dari Redis
        return $posts->sortBy(function($post) use ($postIds) {
            return array_search($post->id, $postIds);
        })->values();
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
            
            // Trigger feed regeneration
            GenerateFeedJob::dispatch($follower->id);
            
            return ['status' => 'unfollowed'];
        }

        Follow::create([
            'follower_id' => $follower->id,
            'following_id' => $target->id,
        ]);
        
        // Trigger feed regeneration
        GenerateFeedJob::dispatch($follower->id);

        return ['status' => 'followed'];
    }
}
