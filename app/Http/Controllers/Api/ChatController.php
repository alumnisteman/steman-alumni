<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Events\NewMessageEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Get total unread messages count for the current user
     */
    public function getUnreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->whereNull('deleted_at')
            ->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * Get list of conversations for the current user
     */
    public function getConversations()
    {
        $userId = Auth::id();

        // Find latest message per conversation partner
        $subquery = DB::table('messages')
            ->select(DB::raw('MAX(id) as max_id'))
            ->where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->whereNull('deleted_at')
            ->whereNull('target_year')
            ->groupBy(DB::raw('CASE WHEN sender_id = ' . $userId . ' THEN receiver_id ELSE sender_id END'));

        $conversations = Message::whereIn('id', $subquery)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Batch unread count in ONE query instead of N queries
        $partnerIds = $conversations->map(function($msg) use ($userId) {
            return $msg->sender_id === $userId ? $msg->receiver_id : $msg->sender_id;
        })->filter()->unique()->values();

        $unreadCounts = Message::select('sender_id', DB::raw('COUNT(*) as count'))
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->whereIn('sender_id', $partnerIds)
            ->groupBy('sender_id')
            ->pluck('count', 'sender_id');

        // Batch online status in ONE query instead of N queries
        $onlineUserIds = DB::table('sessions')
            ->where('last_activity', '>=', now()->subMinutes(15)->timestamp)
            ->whereIn('user_id', $partnerIds)
            ->distinct()
            ->pluck('user_id');

        $result = $conversations->map(function ($msg) use ($userId, $unreadCounts, $onlineUserIds) {
            $otherUser = $msg->sender_id === $userId ? $msg->receiver : $msg->sender;
            if (!$otherUser) return null; // Guard against deleted users

            return [
                'id' => $otherUser->id,
                'name' => $otherUser->name,
                'avatar' => $otherUser->profile_picture_url,
                'last_message' => $msg->message,
                'last_message_time' => $msg->created_at->diffForHumans(),
                'is_read' => $msg->is_read,
                'is_sender' => $msg->sender_id === $userId,
                'unread_count' => $unreadCounts->get($otherUser->id, 0),
                'is_online' => $onlineUserIds->contains($otherUser->id),
            ];
        })->filter()->values();

        return response()->json($result);
    }

    /**
     * Get messages with a specific user
     */
    public function getMessages($userId)
    {
        $authId = Auth::id();

        // Mark messages as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', $authId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::where(function($q) use ($authId, $userId) {
                $q->where('sender_id', $authId)->where('receiver_id', $userId);
            })
            ->orWhere(function($q) use ($authId, $userId) {
                $q->where('sender_id', $userId)->where('receiver_id', $authId);
            })
            ->with(['sender', 'parent'])
            ->orderBy('created_at', 'asc')
            ->limit(100)
            ->get()
            ->map(function($msg) use ($authId) {
                return [
                    'id' => $msg->id,
                    'sender_id' => $msg->sender_id,
                    'message' => $msg->message,
                    'is_mine' => $msg->sender_id === $authId,
                    'time' => $msg->created_at->format('H:i'),
                    'avatar' => $msg->sender->profile_picture_url,
                    'parent' => $msg->parent ? [
                        'id' => $msg->parent->id,
                        'message' => $msg->parent->message,
                        'sender_name' => $msg->parent->sender_id === $authId ? 'Anda' : $msg->parent->sender->name
                    ] : null
                ];
            });

        return response()->json($messages);
    }

    /**
     * Send a new message
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:messages,id',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'parent_id' => $request->parent_id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        // Load relations for broadcast
        $message->load(['sender', 'parent.sender']);

        // Broadcast to receiver
        try {
            broadcast(new NewMessageEvent($message))->toOthers();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Chat Broadcast Failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'message' => $message->message,
                'is_mine' => true,
                'time' => $message->created_at->format('H:i'),
                'avatar' => $message->sender->profile_picture_url,
                'parent' => $message->parent ? [
                    'id' => $message->parent->id,
                    'message' => $message->parent->message,
                    'sender_name' => $message->parent->sender_id === Auth::id() ? 'Anda' : $message->parent->sender->name
                ] : null
            ]
        ]);
    }

    /**
     * Mark messages from a specific user as read
     */
    public function markAsRead(Request $request, $userId)
    {
        Message::where('sender_id', $userId)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Delete a message
     */
    public function deleteMessage($id)
    {
        $message = Message::findOrFail($id);
        
        if ($message->sender_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->delete();
        return response()->json(['success' => true]);
    }
}
