<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Poll extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'created_by', 'question', 'description', 'emoji',
        'type', 'status', 'is_anonymous', 'show_results_before_vote', 'ends_at',
    ];

    protected $casts = [
        'ends_at'                    => 'datetime',
        'is_anonymous'               => 'boolean',
        'show_results_before_vote'   => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function options()
    {
        return $this->hasMany(PollOption::class)->orderBy('sort_order');
    }

    public function votes()
    {
        return $this->hasMany(PollVote::class);
    }

    public function hasVoted(User $user): bool
    {
        return $this->votes()->where('user_id', $user->id)->exists();
    }

    public function getTotalVotesAttribute(): int
    {
        return $this->options->sum('votes_count');
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->ends_at && $this->ends_at->isPast();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()));
    }
}
