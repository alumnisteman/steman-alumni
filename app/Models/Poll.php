<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Poll extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'options',
        'is_active',
    ];

    protected $casts = [
        'options'   => 'array',
        'is_active' => 'boolean',
    ];
}
?>
