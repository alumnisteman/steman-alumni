<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewStoryNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $storyType;

    /**
     * Create a new notification instance.
     */
    public function __construct($user, $storyType)
    {
        $this->user = $user;
        $this->storyType = $storyType;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        $typeName = 'cerita';
        if ($this->storyType === 'note') $typeName = 'catatan mood';
        if ($this->storyType === 'spotify') $typeName = 'rekomendasi musik';

        return [
            'title' => 'Story Baru!',
            'message' => "{$this->user->name} baru saja membagikan {$typeName} baru.",
            'user_id' => $this->user->id,
            'user_avatar' => $this->user->profile_picture_url,
            'action_url' => route('feed.index', ['open_story' => $this->user->id]),
            'type' => 'story'
        ];
    }
}
