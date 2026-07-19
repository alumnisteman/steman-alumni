<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Merchandise extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'merchandise';

    protected $fillable = [
        'name', 'slug', 'category', 'description', 'price', 'price_member',
        'image', 'images', 'sizes', 'colors',
        'is_pre_order', 'pre_order_open_at', 'pre_order_close_at',
        'estimated_delivery_at', 'is_active', 'stock', 'min_order',
        'whatsapp_contact', 'sort_order',
    ];

    protected $casts = [
        'images'                => 'array',
        'sizes'                 => 'array',
        'colors'                => 'array',
        'is_pre_order'          => 'boolean',
        'is_active'             => 'boolean',
        'pre_order_open_at'     => 'datetime',
        'pre_order_close_at'    => 'datetime',
        'estimated_delivery_at' => 'datetime',
    ];

    public function orders()
    {
        return $this->hasMany(MerchandiseOrder::class);
    }

    public function isPreOrderActive(): bool
    {
        if (! $this->is_pre_order) return false;
        $now = now();
        if ($this->pre_order_open_at && $now->lt($this->pre_order_open_at)) return false;
        if ($this->pre_order_close_at && $now->gt($this->pre_order_close_at)) return false;
        return true;
    }

    public function isOrderable(): bool
    {
        if (! $this->is_active) return false;
        if ($this->is_pre_order) return $this->isPreOrderActive();
        // Ready-stock: stock = 0 means unlimited (always orderable when active).
        // stock > 0 means counted stock — orderable while stock remains.
        // Admin sets is_active = false to stop orders entirely.
        if ($this->stock === 0) return true;   // unlimited
        return $this->stock > 0;               // counted stock available
    }

    public function formattedPrice(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function formattedPriceMember(): ?string
    {
        if (! $this->price_member) return null;
        return 'Rp ' . number_format($this->price_member, 0, ',', '.');
    }

    public static function getCategories(): array
    {
        return [
            'kaos'     => ['label' => 'Kaos Reunion',  'icon' => 'bi-person-standing'],
            'polo'     => ['label' => 'Polo Shirt',    'icon' => 'bi-person-badge'],
            'jaket'    => ['label' => 'Jaket',         'icon' => 'bi-cloud-snow'],
            'topi'     => ['label' => 'Topi',          'icon' => 'bi-emoji-sunglasses'],
            'mug'      => ['label' => 'Mug',           'icon' => 'bi-cup-hot'],
            'tumbler'  => ['label' => 'Tumbler',       'icon' => 'bi-cup-straw'],
            'pin'      => ['label' => 'Pin',           'icon' => 'bi-award'],
            'lanyard'  => ['label' => 'Lanyard',       'icon' => 'bi-tag'],
            'stiker'   => ['label' => 'Stiker',        'icon' => 'bi-stickies'],
            'kalender' => ['label' => 'Kalender',      'icon' => 'bi-calendar3'],
        ];
    }
}
