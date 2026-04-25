<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'action', 'user_id', 'meta', 'hash'
    ];

    protected $casts = [
        'meta' => 'json',
    ];

    /**
     * Immutable Log Protection
     */
    protected static function booted()
    {
        static::deleting(function ($log) {
            throw new \Exception("Audit log bersifat IMMUTABLE dan tidak boleh dihapus!");
        });

        static::updating(function ($log) {
            throw new \Exception("Audit log bersifat IMMUTABLE dan tidak boleh diubah!");
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
