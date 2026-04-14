<?php

namespace App\Models;

use App\Traits\HasContentProtection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory, HasContentProtection;

    protected $fillable = [
        'user_id',
        'name',
        'category',
        'description',
        'whatsapp',
        'location',
        'logo_url',
        'status',
    ];

    /**
     * Fields that should be automatically sanitized and moderated.
     */
    public $sanitizable = ['name', 'category', 'description', 'location'];

    /**
     * Get the owner (alumni) of the business.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the gallery photos for the business.
     */
    public function photos()
    {
        return $this->hasMany(BusinessPhoto::class);
    }

    /**
     * Get the central list of official business categories.
     */
    public static function getCategories()
    {
        return [
            'Produk Alumni',
            'Merchandise STEMAN',
            'Makanan & UMKM',
            'Jasa Alumni'
        ];
    }
}
