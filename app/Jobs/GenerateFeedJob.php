<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Post;
use App\Services\RankingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class GenerateFeedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(RankingService $rankingService): void
    {
        $user = User::find($this->userId);
        if (!$user) return;

        // 1. Get Candidate Posts
        // From following
        $followingIds = $user->following()->pluck('users.id');
        
        // Candidates from following (last 7 days to keep it fresh)
        $followingPosts = Post::whereIn('user_id', $followingIds)
            ->where('visibility', '!=', 'private') // assuming there's visibility constraint
            ->where('created_at', '>=', now()->subDays(7))
            ->get();

        // Trending posts (last 3 days, high engagement)
        $trendingPosts = Post::whereNotIn('user_id', clone $followingIds->push($user->id))
            ->where('visibility', 'public')
            ->where('created_at', '>=', now()->subDays(3))
            ->orderByRaw('(likes_count * 2) + (comments_count * 3) DESC')
            ->limit(20)
            ->get();

        // Explore (recent public posts from same major/city)
        $explorePosts = Post::whereNotIn('user_id', clone $followingIds->push($user->id))
            ->where('visibility', 'public')
            ->whereHas('user', function($q) use ($user) {
                $q->where('major', $user->major)
                  ->orWhere('city_name', $user->city_name);
            })
            ->latest()
            ->limit(10)
            ->get();

        // Combine all candidates and ensure uniqueness
        $candidates = $followingPosts->concat($trendingPosts)->concat($explorePosts)->unique('id');

        // 2. Rank Candidates
        $scoredPosts = $candidates->map(function ($post) use ($user, $rankingService) {
            return [
                'id' => $post->id,
                'score' => $rankingService->score($post, $user)
            ];
        });

        // Sort descending by score
        $rankedIds = $scoredPosts->sortByDesc('score')->pluck('id')->toArray();

        // 3. Store to Redis
        $redisKey = "feed:user:{$user->id}";
        
        // Use pipeline for atomic overwrite
        Redis::pipeline(function ($pipe) use ($redisKey, $rankedIds) {
            $pipe->del($redisKey);
            if (!empty($rankedIds)) {
                $pipe->rpush($redisKey, ...$rankedIds);
                // Expire after 2 hours if not regenerated
                $pipe->expire($redisKey, 7200); 
            }
        });
    }
}
