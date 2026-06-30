<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // posts: visibility & is_anonymous used heavily in FeedService/FeedController
        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                if (!$this->indexExists('posts', 'posts_visibility_index')) {
                    $table->index('visibility', 'posts_visibility_index');
                }
                if (!$this->indexExists('posts', 'posts_is_anonymous_index')) {
                    $table->index('is_anonymous', 'posts_is_anonymous_index');
                }
            });
        }

        // museum_items: composite index for category+status+era_year
        if (Schema::hasTable('museum_items')) {
            Schema::table('museum_items', function (Blueprint $table) {
                if (!$this->indexExists('museum_items', 'museum_items_category_status_index')) {
                    $table->index(['category', 'status'], 'museum_items_category_status_index');
                }
                if (Schema::hasColumn('museum_items', 'era_year') && !$this->indexExists('museum_items', 'museum_items_era_year_index')) {
                    $table->index('era_year', 'museum_items_era_year_index');
                }
            });
        }

        // job_vacancies: company & type used in listing
        if (Schema::hasTable('job_vacancies')) {
            Schema::table('job_vacancies', function (Blueprint $table) {
                if (!$this->indexExists('job_vacancies', 'job_vacancies_type_index')) {
                    $table->index('type', 'job_vacancies_type_index');
                }
            });
        }

        // galleries: type+status composite for fast public gallery queries
        if (Schema::hasTable('galleries')) {
            Schema::table('galleries', function (Blueprint $table) {
                if (!$this->indexExists('galleries', 'galleries_type_status_index')) {
                    $table->index(['type', 'status'], 'galleries_type_status_index');
                }
            });
        }

        // programs: status index for fast active/published lookups
        if (Schema::hasTable('programs')) {
            Schema::table('programs', function (Blueprint $table) {
                if (!$this->indexExists('programs', 'programs_status_index')) {
                    $table->index('status', 'programs_status_index');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropIndexIfExists('posts_visibility_index');
                $table->dropIndexIfExists('posts_is_anonymous_index');
            });
        }
        if (Schema::hasTable('museum_items')) {
            Schema::table('museum_items', function (Blueprint $table) {
                $table->dropIndexIfExists('museum_items_category_status_index');
                $table->dropIndexIfExists('museum_items_era_year_index');
            });
        }
        if (Schema::hasTable('job_vacancies')) {
            Schema::table('job_vacancies', function (Blueprint $table) {
                $table->dropIndexIfExists('job_vacancies_type_index');
            });
        }
        if (Schema::hasTable('galleries')) {
            Schema::table('galleries', function (Blueprint $table) {
                $table->dropIndexIfExists('galleries_type_status_index');
            });
        }
        if (Schema::hasTable('programs')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->dropIndexIfExists('programs_status_index');
            });
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
            return !empty($indexes);
        } catch (\Exception $e) {
            return false;
        }
    }
};
