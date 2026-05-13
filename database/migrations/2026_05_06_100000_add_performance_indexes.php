<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->addIndexIfNotExists('users', 'role');
        $this->addIndexIfNotExists('users', 'points');
        $this->addIndexIfNotExists('users', 'status');
        
        $this->addIndexIfNotExists('galleries', 'type');
        $this->addIndexIfNotExists('galleries', 'status');
        
        $this->addIndexIfNotExists('news', 'status');
        
        $this->addIndexIfNotExists('activity_logs', 'user_id');
        $this->addIndexIfNotExists('activity_logs', 'action');
    }

    private function addIndexIfNotExists($tableName, $column)
    {
        $indexName = "{$tableName}_{$column}_index";
        $exists = \Illuminate\Support\Facades\DB::select("SHOW KEYS FROM {$tableName} WHERE Key_name = '{$indexName}'");
        
        if (empty($exists)) {
            Schema::table($tableName, function (Blueprint $table) use ($column) {
                $table->index($column);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'points', 'status']);
        });
        
        Schema::table('galleries', function (Blueprint $table) {
            $table->dropIndex(['type', 'status']);
        });

        Schema::table('news', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
        
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'action']);
        });
    }
};
