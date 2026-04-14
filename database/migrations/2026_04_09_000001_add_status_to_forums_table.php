<?php
/*
 * Created At: 2026-04-09
 * Purpose: Fix inconsistent column names and missing status/views in forums table.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('forums')) {
            Schema::table('forums', function (Blueprint $table) {
                // Rename Indonesian columns to English (Standardizing with the code)
                if (Schema::hasColumn('forums', 'judul_diskusi')) {
                    $table->renameColumn('judul_diskusi', 'title');
                }
                
                if (Schema::hasColumn('forums', 'deskripsi_masalah')) {
                    $table->renameColumn('deskripsi_masalah', 'content');
                }
                
                if (Schema::hasColumn('forums', 'jumlah_komentar')) {
                    $table->renameColumn('jumlah_komentar', 'comments_count');
                }

                // Add missing columns
                if (!Schema::hasColumn('forums', 'status')) {
                    $table->enum('status', ['active', 'inactive', 'closed'])->default('active')->index();
                }

                if (!Schema::hasColumn('forums', 'views')) {
                    $table->integer('views')->default(0);
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('forums')) {
            Schema::table('forums', function (Blueprint $table) {
                // Reverse renames
                if (Schema::hasColumn('forums', 'title')) {
                    $table->renameColumn('title', 'judul_diskusi');
                }
                if (Schema::hasColumn('forums', 'content')) {
                    $table->renameColumn('content', 'deskripsi_masalah');
                }
                if (Schema::hasColumn('forums', 'comments_count')) {
                    $table->renameColumn('comments_count', 'jumlah_komentar');
                }

                // Drop added columns
                if (Schema::hasColumn('forums', 'status')) {
                    $table->dropColumn('status');
                }
                if (Schema::hasColumn('forums', 'views')) {
                    $table->dropColumn('views');
                }
            });
        }
    }
};
