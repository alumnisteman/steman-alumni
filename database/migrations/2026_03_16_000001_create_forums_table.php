<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('forums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('judul_diskusi');
            $table->text('deskripsi_masalah');
            $table->integer('jumlah_komentar')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('forums');
    }
};