<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'weight',
        'height',
        'bmi_category',
        'activity_level',
        'blood_pressure_status',
        'blood_sugar_status',
        'cholesterol_status',
        'last_symptoms',
        'ai_recommendation',
        'last_checkup_date',
    ];

    /**
     * The attributes that should be cast.
     * Menggunakan 'encrypted' agar data sensitif aman di database.
     *
     * @var array
     */
    protected $casts = [
        // Data sensitif PII → tetap terenkripsi
        'weight'               => 'encrypted',
        'height'               => 'encrypted',
        'last_symptoms'        => 'encrypted',
        'blood_pressure_status'=> 'encrypted',
        'blood_sugar_status'   => 'encrypted',
        'cholesterol_status'   => 'encrypted',
        // Teks rekomendasi AI & kategori → plain text (tidak mengandung PII)
        'ai_recommendation'    => 'string',
        'bmi_category'         => 'string',
        'activity_level'       => 'string',
        'last_checkup_date'    => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
