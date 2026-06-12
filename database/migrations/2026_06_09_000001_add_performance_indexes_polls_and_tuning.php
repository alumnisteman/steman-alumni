<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Polls: tambahkan index is_active dan ends_at (sering difilter untuk poll aktif)
        Schema::table('polls', function (Blueprint $table) {
            if (!$this->hasIndex('polls', 'polls_is_active_ends_at_index')) {
                $table->index(['is_active', 'ends_at'], 'polls_is_active_ends_at_index');
            }
            if (!$this->hasIndex('polls', 'polls_is_active_index')) {
                $table->index('is_active', 'polls_is_active_index');
            }
        });

        // Podcasts: index is_published + category sudah ada, tambah created_at
        if (Schema::hasTable('podcasts')) {
            Schema::table('podcasts', function (Blueprint $table) {
                if (!$this->hasIndex('podcasts', 'podcasts_created_at_index')) {
                    $table->index('created_at', 'podcasts_created_at_index');
                }
            });
        }

        // Activity logs: index untuk tampilan dashboard (sering query by created_at DESC)
        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                if (!$this->hasIndex('activity_logs', 'activity_logs_created_at_index')) {
                    $table->index('created_at', 'activity_logs_created_at_index');
                }
            });
        }

        // Audit logs: index by created_at untuk chunk query
        if (Schema::hasTable('audit_logs')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                if (!$this->hasIndex('audit_logs', 'audit_logs_created_at_index')) {
                    $table->index('created_at', 'audit_logs_created_at_index');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->dropIndex('polls_is_active_ends_at_index');
            $table->dropIndex('polls_is_active_index');
        });
        if (Schema::hasTable('podcasts')) {
            Schema::table('podcasts', function (Blueprint $table) {
                $table->dropIndex('podcasts_created_at_index');
            });
        }
        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropIndex('activity_logs_created_at_index');
            });
        }
        if (Schema::hasTable('audit_logs')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                $table->dropIndex('audit_logs_created_at_index');
            });
        }
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        return !empty($indexes);
    }
};
