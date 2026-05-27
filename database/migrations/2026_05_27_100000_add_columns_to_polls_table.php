<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            if (!Schema::hasColumn('polls', 'emoji')) {
                $table->string('emoji', 5)->nullable()->default('🗳️')->after('id');
            }
            if (!Schema::hasColumn('polls', 'question')) {
                $table->string('question')->nullable()->after('emoji');
            }
            if (!Schema::hasColumn('polls', 'type')) {
                $table->enum('type', ['single', 'multiple'])->default('single')->after('description');
            }
            if (!Schema::hasColumn('polls', 'ends_at')) {
                $table->timestamp('ends_at')->nullable()->after('type');
            }
            if (!Schema::hasColumn('polls', 'is_anonymous')) {
                $table->boolean('is_anonymous')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('polls', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('is_anonymous');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['emoji', 'question', 'type', 'ends_at', 'is_anonymous', 'created_by']);
        });
    }
};
