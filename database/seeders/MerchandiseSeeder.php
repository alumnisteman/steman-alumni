<?php

namespace Database\Seeders;

use App\Models\Merchandise;
use Illuminate\Database\Seeder;

class MerchandiseSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'name'        => 'Kaos Reunion Alumni STEMAN',
                'category'    => 'kaos',
                'description' => 'Kaos reunion eksklusif bertema persatuan alumni SMKN 2 Ternate. Bahan cotton combed 30s berkualitas tinggi, sablon plastisol tahan lama.',
                'price'       => 120000,
                'price_member'=> 100000,
                'sizes'       => ['S', 'M', 'L', 'XL', 'XXL', '3XL'],
                'colors'      => ['Hitam', 'Putih', 'Navy'],
                'is_pre_order'=> true,
                'min_order'   => 1,
                'sort_order'  => 1,
            ],
            [
                'name'        => 'Polo Shirt Alumni STEMAN',
                'category'    => 'polo',
                'description' => 'Polo shirt resmi alumni dengan bordir logo STEMAN di dada kiri. Bahan lacoste cotton premium, nyaman dipakai acara formal maupun kasual.',
                'price'       => 180000,
                'price_member'=> 155000,
                'sizes'       => ['S', 'M', 'L', 'XL', 'XXL'],
                'colors'      => ['Hitam', 'Navy', 'Maroon'],
                'is_pre_order'=> true,
                'min_order'   => 1,
                'sort_order'  => 2,
            ],
            [
                'name'        => 'Jaket Varsity Alumni STEMAN',
                'category'    => 'jaket',
                'description' => 'Jaket varsity eksklusif edisi alumni STEMAN. Bordir nama angkatan tersedia, bahan wool premium dengan aksen kulit sintetis.',
                'price'       => 350000,
                'price_member'=> 320000,
                'sizes'       => ['S', 'M', 'L', 'XL', 'XXL'],
                'colors'      => ['Hitam-Kuning', 'Navy-Putih'],
                'is_pre_order'=> true,
                'min_order'   => 1,
                'sort_order'  => 3,
            ],
            [
                'name'        => 'Topi Snapback Alumni STEMAN',
                'category'    => 'topi',
                'description' => 'Topi snapback dengan logo STEMAN bordir 3D di bagian depan. Adjustable, cocok untuk semua ukuran kepala.',
                'price'       => 85000,
                'price_member'=> 70000,
                'sizes'       => ['One Size'],
                'colors'      => ['Hitam', 'Navy', 'Putih'],
                'is_pre_order'=> true,
                'min_order'   => 1,
                'sort_order'  => 4,
            ],
            [
                'name'        => 'Mug Keramik Alumni STEMAN',
                'category'    => 'mug',
                'description' => 'Mug keramik 11oz dengan cetak foto angkatan atau desain logo STEMAN. Cocok untuk hadiah wisuda dan koleksi kenangan.',
                'price'       => 65000,
                'price_member'=> 55000,
                'sizes'       => ['330ml'],
                'colors'      => ['Putih', 'Hitam'],
                'is_pre_order'=> true,
                'min_order'   => 1,
                'sort_order'  => 5,
            ],
            [
                'name'        => 'Tumbler Stainless Alumni STEMAN',
                'category'    => 'tumbler',
                'description' => 'Tumbler stainless steel 500ml dengan laser engraving logo STEMAN. Double wall, menjaga minuman tetap hangat/dingin selama 12 jam.',
                'price'       => 150000,
                'price_member'=> 130000,
                'sizes'       => ['500ml'],
                'colors'      => ['Silver', 'Hitam Matte', 'Gold'],
                'is_pre_order'=> true,
                'min_order'   => 1,
                'sort_order'  => 6,
            ],
            [
                'name'        => 'Pin Badge Alumni STEMAN',
                'category'    => 'pin',
                'description' => 'Set pin badge logo Alumni STEMAN. Tersedia ukuran 25mm dan 44mm dengan finishing glossy. Cocok untuk tas, jaket, topi.',
                'price'       => 25000,
                'price_member'=> 20000,
                'sizes'       => ['25mm', '44mm'],
                'colors'      => ['Emas', 'Perak'],
                'is_pre_order'=> false,
                'stock'       => 100,
                'min_order'   => 1,
                'sort_order'  => 7,
            ],
            [
                'name'        => 'Lanyard ID Card Alumni STEMAN',
                'category'    => 'lanyard',
                'description' => 'Lanyard polyester premium dengan full print logo dan nama Alumni STEMAN. Lebar 2cm, dilengkapi pengait logam dan safety buckle.',
                'price'       => 35000,
                'price_member'=> 28000,
                'sizes'       => ['90cm'],
                'colors'      => ['Hitam', 'Navy'],
                'is_pre_order'=> false,
                'stock'       => 200,
                'min_order'   => 5,
                'sort_order'  => 8,
            ],
            [
                'name'        => 'Stiker Pack Alumni STEMAN',
                'category'    => 'stiker',
                'description' => 'Set stiker vinyl cut desain logo dan maskot Alumni STEMAN. Tahan air, bisa untuk motor, laptop, hingga botol minum. 1 pack = 10 pcs stiker.',
                'price'       => 30000,
                'price_member'=> 25000,
                'sizes'       => ['Berbagai Ukuran'],
                'is_pre_order'=> false,
                'stock'       => 150,
                'min_order'   => 1,
                'sort_order'  => 9,
            ],
            [
                'name'        => 'Kalender Meja Alumni STEMAN 2026',
                'category'    => 'kalender',
                'description' => 'Kalender meja edisi 2026 menampilkan foto kenangan dan prestasi alumni STEMAN. Cetak full color hardcover, dilengkapi foto angkatan setiap bulan.',
                'price'       => 75000,
                'price_member'=> 60000,
                'sizes'       => ['A5 Landscape'],
                'is_pre_order'=> true,
                'min_order'   => 1,
                'sort_order'  => 10,
            ],
        ];

        foreach ($items as $data) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']) . '-' . \Illuminate\Support\Str::random(6);
            $data['stock'] = $data['stock'] ?? 0;
            Merchandise::create($data);
        }
    }
}
