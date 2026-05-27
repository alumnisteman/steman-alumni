<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('options'); // e.g. ["Option A","Option B"]
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes(); // optional – remove if you prefer permanent delete
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('polls');
    }
};
?>
