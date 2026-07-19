<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MerchandiseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchandise_id', 'user_id', 'order_code',
        'buyer_name', 'buyer_phone', 'buyer_email', 'buyer_address',
        'size', 'color', 'custom_note', 'quantity', 'unit_price',
        'total_price', 'status', 'payment_proof', 'admin_note',
    ];

    public function merchandise()
    {
        return $this->belongsTo(Merchandise::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateOrderCode(): string
    {
        do {
            $code = 'MRCH-' . strtoupper(Str::random(8));
        } while (self::where('order_code', $code)->exists());
        return $code;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending'    => 'warning',
            'confirmed'  => 'info',
            'paid'       => 'primary',
            'processing' => 'secondary',
            'shipped'    => 'info',
            'delivered'  => 'success',
            'cancelled'  => 'danger',
            default      => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'    => 'Menunggu Konfirmasi',
            'confirmed'  => 'Dikonfirmasi',
            'paid'       => 'Sudah Bayar',
            'processing' => 'Diproses',
            'shipped'    => 'Dikirim',
            'delivered'  => 'Diterima',
            'cancelled'  => 'Dibatalkan',
            default      => $this->status,
        };
    }

    public function formattedTotal(): string
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }
}
