<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $group
 * @property string $status
 * @mixin \Eloquent
 */
class Major extends Model
{
    protected $fillable = ['name', 'group', 'status'];
}
