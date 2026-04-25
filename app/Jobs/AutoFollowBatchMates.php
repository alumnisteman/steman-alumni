<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutoFollowBatchMates implements ShouldQueue
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
    public function handle(): void
    {
        $user = User::find($this->userId);
        if (!$user || !$user->graduation_year) return;

        // Find all alumni from the same graduation year
        $batchMateIds = User::where('graduation_year', $user->graduation_year)
            ->where('id', '!=', $user->id)
            ->where('role', 'alumni')
            ->pluck('id');

        if ($batchMateIds->isEmpty()) {
            Log::info("No batch mates found for user {$user->id} (Year: {$user->graduation_year})");
            return;
        }

        $follows = [];
        foreach ($batchMateIds as $mateId) {
            $follows[] = [
                'follower_id' => $user->id,
                'following_id' => $mateId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Also make them follow the new user (Mutual Follow for batch mates)
            $follows[] = [
                'follower_id' => $mateId,
                'following_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Chunk insert to avoid large queries
        foreach (array_chunk($follows, 100) as $chunk) {
            \DB::table('follows')->insertOrIgnore($chunk);
        }

        Log::info("Auto-followed " . count($batchMateIds) . " batch mates for user {$user->id}");
        
        // Invalidate feed cache for the user
        \Illuminate\Support\Facades\Redis::del("feed:user:{$user->id}");
        \App\Jobs\GenerateFeedJob::dispatch($user->id);
    }
}
