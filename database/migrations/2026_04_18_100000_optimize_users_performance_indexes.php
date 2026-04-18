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
        Schema::table('users', function (Blueprint $table) {
            // Optimization: Add indexes to frequently queried columns for the global network and search
            
            // Check if index exists before adding to prevent errors
            if (!$this->hasIndex('users', 'users_latitude_longitude_index')) {
                $table->index(['latitude', 'longitude']);
            }
            
            if (!$this->hasIndex('users', 'users_major_index')) {
                $table->index('major');
            }
            
            if (!$this->hasIndex('users', 'users_graduation_year_index')) {
                $table->index('graduation_year');
            }

            if (Schema::hasColumn('users', 'city_name')) {
                if (!$this->hasIndex('users', 'users_city_name_index')) {
                    $table->index('city_name');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropIndex(['major']);
            $table->dropIndex(['graduation_year']);
            if (Schema::hasColumn('users', 'city_name')) {
                $table->dropIndex(['city_name']);
            }
        });
    }

    /**
     * Helper to check if an index exists
     */
    private function hasIndex($table, $index)
    {
        $conn = Schema::getConnection();
        $dbName = $conn->getDatabaseName();
        $results = $conn->select("SELECT * FROM information_schema.statistics WHERE table_schema = '$dbName' AND table_name = '$table' AND index_name = '$index'");
        return count($results) > 0;
    }
};
