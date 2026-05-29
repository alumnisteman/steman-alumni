<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Poll extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'emoji',
        'question',
        'description',
        'type',
        'ends_at',
        'is_anonymous',
        'is_active',
        'options',
        'created_by',
        'title',
    ];

    protected $casts = [
        'options'      => 'array',
        'is_active'    => 'boolean',
        'is_anonymous' => 'boolean',
        'ends_at'      => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTotalVotesAttribute(): int
    {
        $options = $this->options ?? collect([]);
        return $options->sum('votes_count');
    }

    public function getUserVotedAttribute(): bool
    {
        return false;
    }

    public function getUserVoteIdsAttribute()
    {
        return collect([]);
    }

    public function getOptionsAttribute($value)
    {
        $decoded = is_string($value) ? json_decode($value, true) : $value;
        if (!is_array($decoded)) return collect([]);

        $totalVotes = array_sum(array_column($decoded, 'votes_count'));
        return collect($decoded)->map(function ($opt) use ($totalVotes) {
            $votes = $opt['votes_count'] ?? 0;
            $opt['percentage'] = $totalVotes > 0 ? round(($votes / $totalVotes) * 100) : 0;
            return (object) $opt;
        });
    }

    public function getMaxVotesAttribute(): int
    {
        return $this->options->max('votes_count') ?? 0;
    }
}
