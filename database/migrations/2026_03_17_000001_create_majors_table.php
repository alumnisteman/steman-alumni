<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('majors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('group')->default('Modern');
            $table->string('status')->default('active');
            $table->timestamps();
        });

        // Seed with existing departments from auth/register.blade.php
        $majors = [
            ['name' => 'Teknik Komputer & Jaringan', 'group' => 'Modern'],
            ['name' => 'Rekayasa Perangkat Lunak', 'group' => 'Modern'],
            ['name' => 'Multimedia', 'group' => 'Modern'],
            ['name' => 'Teknik Gambar Bangunan', 'group' => 'Modern'],
            ['name' => 'Teknik Konstruksi Kayu', 'group' => 'Modern'],
            ['name' => 'Geomatika', 'group' => 'Modern'],
            ['name' => 'Kompetensi Umum', 'group' => 'Modern'],
            
            ['name' => 'Mesin Produksi', 'group' => 'Legacy'],
            ['name' => 'Mesin Otomotif', 'group' => 'Legacy'],
            ['name' => 'Bangunan Gedung', 'group' => 'Legacy'],
            ['name' => 'Survei Pemetaan', 'group' => 'Legacy'],
            ['name' => 'Listrik', 'group' => 'Legacy'],
        ];

        foreach ($majors as $major) {
            DB::table('majors')->insert($major + ['created_at' => now(), 'updated_at' => now()]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('majors');
    }
};
