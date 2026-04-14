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
        // Explicitly ensuring 'editor' is in the enum.
        // We use raw SQL because Laravel's change() on ENUM requires doctrine/dbal which might not be installed or behave correctly with ENUMs.
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'editor', 'alumni') DEFAULT 'alumni'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting to a state without editor if absolutely necessary, but usually we don't want to lose data.
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'alumni') DEFAULT 'alumni'");
    }
};
