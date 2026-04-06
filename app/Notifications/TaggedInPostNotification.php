<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaggedInPostNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $post;
    protected $tagger;

    public function __construct(Post $post, User $tagger)
    {
        $this->post = $post;
        $this->tagger = $tagger;
    }

    public function via($notifiable)
    {
        // For now, let's use Database notification. 
        // If you want Email/SMS, you can add 'mail' here.
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'post_id' => $this->post->id,
            'tagger_id' => $this->tagger->id,
            'tagger_name' => $this->tagger->name,
            'message' => "{$this->tagger->name} menandai Anda dalam sebuah postingan nostalgia!",
            'type' => 'post_tag',
            'url' => route('nostalgia.index') . '#post-' . $this->post->id,
        ];
    }
}
