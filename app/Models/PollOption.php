<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollOption extends Model
{
    protected $fillable = ['poll_id', 'option_text', 'option_emoji', 'votes_count', 'sort_order'];

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function votes()
    {
        return $this->hasMany(PollVote::class);
    }

    public function getPercentageAttribute(): float
    {
        $total = $this->poll->total_votes;
        return $total > 0 ? round(($this->votes_count / $total) * 100, 1) : 0;
    }
}
