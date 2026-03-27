<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $title;
    public $message;
    public $action_url;
    public $type;
    public $userId;

    /**
     * Create a new event instance.
     */
    public function __construct($title, $message, $action_url = '#', $type = 'info', $userId = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->action_url = $action_url;
        $this->type = $type;
        $this->userId = $userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        if ($this->userId) {
            return [new PrivateChannel('App.Models.User.' . $this->userId)];
        }
        return [new Channel('notifications')];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'new-notification';
    }
}
