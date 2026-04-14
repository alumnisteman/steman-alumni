<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ContentViolationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $modelName;
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct($model, $reason)
    {
        $this->modelName = class_basename($model);
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        $typeLabel = $this->modelName === 'Forum' ? 'Diskusi Forum' : ($this->modelName === 'Post' ? 'Postingan Nostalgia' : 'Komentar');
        
        return [
            'type' => 'moderation_warning',
            'title' => '⚠️ TEGURAN ADMIN: Pelanggaran Konten',
            'message' => "Konten Anda pada {$typeLabel} telah dihapus otomatis karena terdeteksi mengandung unsur {$this->reason}. Mohon jaga kesantunan dalam berinteraksi di portal Alumni.",
            'reason' => $this->reason,
            'url' => '#',
        ];
    }
}
