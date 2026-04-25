<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use App\Models\UserInterest;
use App\Models\Like;
use App\Models\Comment;

class RankingService
{
    public function score(Post $post, User $user)
    {
        // User Requested Formula: (like x 5) + (comment x 3) + (view time x 1) + (recency boost)
        
        $likesScore = ($post->likes_count ?? 0) * 5;
        $commentsScore = ($post->comments_count ?? 0) * 3;
        
        // View time (dwell time) score - 1 point per second viewed
        $viewTimeScore = $this->dwellTimeScore($user->id, $post->id);
        
        // Recency boost (Exponential decay)
        $hoursAgo = max(0.1, $post->created_at->diffInHours(now()));
        $recencyBoost = 50 / ($hoursAgo + 1); 
        
        // Relationship bonus (Alumni Network specific)
        $relationshipBonus = $this->relationshipScore($user->id, $post->user_id);

        // Final Score
        return $likesScore + $commentsScore + $viewTimeScore + $recencyBoost + $relationshipBonus;
    }

    private function dwellTimeScore($userId, $postId)
    {
        $summary = \App\Models\UserPostViewSummary::where('user_id', $userId)
            ->where('post_id', $postId)
            ->first();

        if (!$summary) return 0;

        // 1 second viewed = 1 point
        return $summary->total_view_time / 1000;
    }

    private function relationshipScore($userId, $authorId)
    {
        if ($userId === $authorId) {
            return 0; // Prevent self-bias
        }

        $likes = Like::where('user_id', $userId)
            ->where('likeable_type', Post::class)
            ->whereHasMorph('likeable', [Post::class], function ($q) use ($authorId) {
                $q->where('user_id', $authorId);
            })
            ->count();

        $comments = Comment::where('user_id', $userId)
            ->where('commentable_type', Post::class)
            ->whereHasMorph('commentable', [Post::class], function ($q) use ($authorId) {
                $q->where('user_id', $authorId);
            })
            ->count();

        $storyViews = \App\Models\StoryView::where('viewer_id', $userId)
            ->whereHas('story', function ($q) use ($authorId) {
                $q->where('user_id', $authorId);
            })
            ->count();

        return ($likes * 2) + ($comments * 3) + ($storyViews * 5);
    }

    private function contentScore(User $user, Post $post)
    {
        $category = $post->type ?? 'general';
        
        $interest = UserInterest::where('user_id', $user->id)
            ->where('category', $category)
            ->value('score') ?? 0;

        return $interest;
    }
}
