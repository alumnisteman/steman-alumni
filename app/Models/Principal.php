<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Principal extends Model
{
    protected $fillable = [
        'name', 'period', 'photo_path', 'status', 'sort_order'
    ];
}
