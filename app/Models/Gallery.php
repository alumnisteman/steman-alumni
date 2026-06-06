<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Gallery extends Model
{
    use SoftDeletes;
    use \App\Traits\HasPublishStatus;

    protected $fillable = [
        'user_id',
        'title',
        'type',
        'file_path',
        'youtube_url',
        'tiktok_url',
        'description',
        'status',
    ];

    public function getIsPublishedAttribute(): bool
    {
        return $this->isPublished();
    }

    /** Normalize legacy "video" type to DB enum value. */
    public static function normalizeType(string $type): string
    {
        return $type === 'video' ? 'youtube' : $type;
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'System/Deleted User',
            'profile_picture' => null
        ]);
    }

    // Extract TikTok video ID from various TikTok URL formats
    public function getTiktokEmbedHtmlAttribute()
    {
        if (!$this->tiktok_url) return null;
        return '<blockquote class="tiktok-embed" cite="' . htmlspecialchars($this->tiktok_url) . '" data-video-id="' . $this->extractTiktokId() . '" style="max-width: 100%; min-width: 100%;"><section></section></blockquote><script async src="https://www.tiktok.com/embed.js"></script>';
    }

    public function extractTiktokId()
    {
        // Matches patterns like: https://www.tiktok.com/@user/video/1234567890
        if (preg_match('/video\/([0-9]+)/', $this->tiktok_url, $matches)) {
            return $matches[1];
        }
        return '';
    }
}
