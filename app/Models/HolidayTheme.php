<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HolidayTheme extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'primary_color',
        'secondary_color',
        'banner',
        'priority',
        'is_active',
    ];

    public $timestamps = true;
}
?>
