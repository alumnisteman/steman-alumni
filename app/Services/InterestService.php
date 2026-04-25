<?php

namespace App\Services;

use App\Models\UserInterest;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InterestService
{
    /**
     * Increment interest score when user interacts with a post.
     */
    public function recordInterest(User $user, Post $post, int $points = 1)
    {
        $category = $post->type ?? 'general';

        UserInterest::updateOrCreate(
            ['user_id' => $user->id, 'category' => $category],
            ['score' => DB::raw("score + $points")]
        );
    }
}
