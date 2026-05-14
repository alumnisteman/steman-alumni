<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use App\Models\UserInterest;
use App\Models\Like;
use App\Models\Comment;

class RankingService
{
    /**
     * Calculate score for a post relative to a user.
     * 
     * @param Post $post
     * @param User $user
     * @return float
     */
    public function score(Post $post, User $user)
    {
        // User Requested Formula: (like x 5) + (comment x 3) + (view time x 1) + (recency boost)
        
        $likesScore = ($post->likes_count ?? 0) * 5;
        $commentsScore = ($post->comments_count ?? 0) * 3;
        
        // View time (dwell time) score - 1 point per second viewed
        $viewTimeScore = $this->dwellTimeScore((int) $user->id, (int) $post->id);
        
        // Recency boost (Exponential decay)
        /** @var \DateTime $now */
        $now = new \DateTime();
        $hoursAgo = max(0.1, $post->created_at->diffInHours($now));
        $recencyBoost = 50 / ($hoursAgo + 1); 
        
        // Relationship bonus (Alumni Network specific)
        $relationshipBonus = $this->relationshipScore((int) $user->id, (int) $post->user_id);

        // Final Score
        return (float) ($likesScore + $commentsScore + $viewTimeScore + $recencyBoost + $relationshipBonus);
    }

    /**
     * @param int $userId
     * @param int $postId
     * @return float
     */
    private function dwellTimeScore(int $userId, int $postId)
    {
        /** @var mixed $query */
        $query = \App\Models\UserPostViewSummary::query();
        
        $summary = $query->where('user_id', $userId)
            ->where('post_id', $postId)
            ->first();

        if (!$summary) return 0;

        // 1 second viewed = 1 point
        return (float) ($summary->total_view_time / 1000);
    }

    /**
     * @param int $userId
     * @param int $authorId
     * @return float
     */
    private function relationshipScore(int $userId, int $authorId)
    {
        if ($userId === $authorId) {
            return 0; // Prevent self-bias
        }

        /** @var mixed $likeQuery */
        $likeQuery = Like::query();
        $likes = $likeQuery->where('user_id', $userId)
            ->where('likeable_type', Post::class)
            ->whereHasMorph('likeable', [Post::class], function ($q) use ($authorId) {
                $q->where('user_id', $authorId);
            })
            ->count();

        /** @var mixed $commentQuery */
        $commentQuery = Comment::query();
        $comments = $commentQuery->where('user_id', $userId)
            ->where('commentable_type', Post::class)
            ->whereHasMorph('commentable', [Post::class], function ($q) use ($authorId) {
                $q->where('user_id', $authorId);
            })
            ->count();

        /** @var mixed $storyQuery */
        $storyQuery = \App\Models\StoryView::query();
        $storyViews = $storyQuery->where('viewer_id', $userId)
            ->whereHas('story', function ($q) use ($authorId) {
                $q->where('user_id', $authorId);
            })
            ->count();

        return (float) (($likes * 2) + ($comments * 3) + ($storyViews * 5));
    }

    /**
     * @param User $user
     * @param Post $post
     * @return float
     */
    private function contentScore(User $user, Post $post)
    {
        $category = $post->type ?? 'general';
        
        /** @var mixed $interestQuery */
        $interestQuery = UserInterest::query();
        $interest = $interestQuery->where('user_id', $user->id)
            ->where('category', $category)
            ->value('score') ?? 0;

        return (float) $interest;
    }
}
