<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'description', 'bank_info', 'goal_amount', 'current_amount', 'image', 'type', 'status', 'is_featured', 'end_date',
        'total_expense', 'expense_distribution', 'sponsor_count', 'show_donor_list',
        'report_status', 'report_verified_at', 'lpj_pdf_path', 'finance_detail_pdf_path', 'documentation_images',
        'manual_donor_count', 'manual_transaction_count',
    ];

    protected $casts = [
        'end_date'                  => 'date',
        'report_verified_at'        => 'date',
        'goal_amount'               => 'decimal:2',
        'current_amount'            => 'decimal:2',
        'total_expense'             => 'decimal:2',
        'is_featured'               => 'boolean',
        'show_donor_list'           => 'boolean',
        'expense_distribution'      => 'array',
        'documentation_images'      => 'array',
        'manual_donor_count'        => 'integer',
        'manual_transaction_count'  => 'integer',
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
