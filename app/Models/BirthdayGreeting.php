<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BirthdayGreeting extends Model
{
    protected $fillable = ['from_user_id', 'to_user_id', 'message', 'emoji'];

    public function sender()    { return $this->belongsTo(User::class, 'from_user_id'); }
    public function recipient() { return $this->belongsTo(User::class, 'to_user_id'); }
}
