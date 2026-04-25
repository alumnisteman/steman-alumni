<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'description', 'bank_info', 'goal_amount', 'current_amount', 'image', 'type', 'status', 'is_featured', 'end_date'
    ];

    protected $casts = [
        'end_date'     => 'date',
        'goal_amount'  => 'decimal:2',
        'current_amount' => 'decimal:2',
        'is_featured'  => 'boolean',
    ];

    public function scopeFoundation($query)
    {
        return $query->where('type', 'foundation');
    }

    public function scopeEvent($query)
    {
        return $query->where('type', 'event');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function getProgressAttribute()
    {
        if ($this->goal_amount <= 0) return 0;
        return ($this->current_amount / $this->goal_amount) * 100;
    }
}
