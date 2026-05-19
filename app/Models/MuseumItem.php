<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MuseumItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'description', 'category', 'image_url', 'video_url',
        'era_year', 'donated_by', 'uploaded_by', 'status', 'views', 'likes',
    ];

    public static array $categoryLabels = [
        'foto_sekolah'    => ['label' => 'Foto Tempo Dulu', 'icon' => '📸', 'color' => 'warning'],
        'ijazah'          => ['label' => 'Ijazah & Dokumen', 'icon' => '📜', 'color' => 'success'],
        'peralatan'       => ['label' => 'Mesin & Peralatan', 'icon' => '⚙️', 'color' => 'secondary'],
        'seragam'         => ['label' => 'Seragam & Atribut', 'icon' => '👔', 'color' => 'primary'],
        'guru_legendaris' => ['label' => 'Guru Legendaris', 'icon' => '👨‍🏫', 'color' => 'danger'],
        'prestasi'        => ['label' => 'Piala & Prestasi', 'icon' => '🏆', 'color' => 'info'],
        'lainnya'         => ['label' => 'Arsip Lainnya', 'icon' => '📦', 'color' => 'dark'],
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by')->withDefault(['name' => 'Alumni Steman']);
    }

    public function userLikes()
    {
        return $this->hasMany(MuseumItemLike::class);
    }

    public function isLikedBy(?User $user): bool
    {
        if (!$user) return false;
        return $this->userLikes()->where('user_id', $user->id)->exists();
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::$categoryLabels[$this->category]['label'] ?? $this->category;
    }

    public function getCategoryIconAttribute(): string
    {
        return self::$categoryLabels[$this->category]['icon'] ?? '📦';
    }

    public function getCategoryColorAttribute(): string
    {
        return self::$categoryLabels[$this->category]['color'] ?? 'dark';
    }

    public function getYoutubeEmbedIdAttribute(): ?string
    {
        if (!$this->video_url) return null;
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $this->video_url, $m);
        return $m[1] ?? null;
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
