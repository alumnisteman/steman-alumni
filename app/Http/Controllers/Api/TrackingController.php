<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\UserEvent;
use App\Models\UserInterest;

class TrackingController extends Controller
{
    /**
     * Track user behavior from navigator.sendBeacon
     */
    public function store(Request $request)
    {
        // Handle both JSON payload (if any) and FormData
        $data = $request->all();
        if (empty($data) && $request->getContent()) {
            $data = json_decode($request->getContent(), true) ?? [];
        }

        if (!$data || !isset($data['type']) || !isset($data['content_id'])) {
            return response()->json(['status' => 'invalid_data'], 400);
        }

        // Only track authenticated users
        if (!auth()->check()) {
            return response()->json(['status' => 'unauthenticated'], 401);
        }

        $userId = auth()->id();
        $type = $data['type']; // 'view', 'like', 'click'
        $contentId = $data['content_id'];
        $duration = $data['duration'] ?? 0; // scroll time in seconds

        try {
            // Log the event asynchronously (ideally using a Job, but DB::table for speed now)
            DB::table('user_events')->insert([
                'user_id' => $userId,
                'type' => $type,
                'content_id' => $contentId,
                'content_type' => $data['content_type'] ?? 'news',
                'duration' => $duration,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update user interest (Smart Learning)
            if ($type === 'view' && $duration > 5) {
                // Determine keyword/category if possible
                $keyword = $data['keyword'] ?? 'general';
                
                DB::table('user_interests')->updateOrInsert(
                    ['user_id' => $userId, 'keyword' => $keyword],
                    ['score' => DB::raw('score + 1'), 'updated_at' => now()]
                );
            } elseif ($type === 'like') {
                $keyword = $data['keyword'] ?? 'general';
                
                DB::table('user_interests')->updateOrInsert(
                    ['user_id' => $userId, 'keyword' => $keyword],
                    ['score' => DB::raw('score + 5'), 'updated_at' => now()]
                );
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Track a post view from the social feed (IntersectionObserver beacon)
     */
    public function trackView(Request $request)
    {
        // Handle both JSON payload (sendBeacon) and FormData
        $data = $request->all();
        if (empty($data) && $request->getContent()) {
            $data = json_decode($request->getContent(), true) ?? [];
        }

        if (empty($data) || empty($data['post_id'])) {
            return response()->json(['status' => 'invalid_data'], 400);
        }

        if (!auth()->check()) {
            return response()->json(['status' => 'unauthenticated'], 401);
        }

        $userId = auth()->id();
        $postId = (int) $data['post_id'];
        $viewTime = (int) ($data['view_time'] ?? 0); // milliseconds

        try {
            DB::table('user_events')->insert([
                'user_id'      => $userId,
                'type'         => 'view',
                'content_id'   => $postId,
                'content_type' => 'post',
                'duration'     => (int) round($viewTime / 1000), // convert ms → seconds
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            // Silently fail — tracking should never crash the UX
            return response()->json(['status' => 'error'], 500);
        }
    }
}
