<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchandise', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category'); // kaos, polo, jaket, topi, mug, tumbler, pin, lanyard, stiker, kalender
            $table->text('description')->nullable();
            $table->unsignedInteger('price');
            $table->unsignedInteger('price_member')->nullable()->comment('Harga khusus alumni terverifikasi');
            $table->string('image')->nullable();
            $table->json('images')->nullable()->comment('Galeri foto tambahan');
            $table->json('sizes')->nullable()->comment('Pilihan ukuran, e.g. ["S","M","L","XL","XXL"]');
            $table->json('colors')->nullable()->comment('Pilihan warna tersedia');
            $table->boolean('is_pre_order')->default(false);
            $table->dateTime('pre_order_open_at')->nullable();
            $table->dateTime('pre_order_close_at')->nullable();
            $table->dateTime('estimated_delivery_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('stock')->default(0)->comment('0 = unlimited/pre-order');
            $table->unsignedInteger('min_order')->default(1);
            $table->string('whatsapp_contact')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('category');
            $table->index('is_active');
            $table->index('is_pre_order');
        });

        Schema::create('merchandise_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchandise_id')->constrained('merchandise')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('order_code')->unique();
            $table->string('buyer_name');
            $table->string('buyer_phone');
            $table->string('buyer_email')->nullable();
            $table->text('buyer_address');
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->string('custom_note')->nullable();
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('unit_price');
            $table->unsignedInteger('total_price');
            $table->enum('status', ['pending', 'confirmed', 'paid', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->string('payment_proof')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('order_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchandise_orders');
        Schema::dropIfExists('merchandise');
    }
};
