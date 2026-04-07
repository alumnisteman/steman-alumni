<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Gallery extends Model
{
    use SoftDeletes;

    protected $fillable = [
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
        return $this->status === 'published';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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
