<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'photo_url',
    ];

    /**
     * Get the business that owns the photo.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
