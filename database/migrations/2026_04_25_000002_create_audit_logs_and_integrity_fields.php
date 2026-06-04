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
        // 1. Create Audit Logs Table (Immutable Log)
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->string('action'); // e.g., 'donation_created', 'donation_verified'
                $table->unsignedBigInteger('user_id')->nullable();
                $table->json('meta')->nullable(); // Stores the snapshot of data
                $table->string('hash')->nullable(); // Integrity hash
                $table->timestamps();
            });
        }

        // 2. Add Hash and Proof of Distribution to existing tables
        if (Schema::hasTable('donations')) {
            Schema::table('donations', function (Blueprint $table) {
                if (!Schema::hasColumn('donations', 'hash')) {
                    $table->string('hash')->nullable()->after('proof_path');
                }
            });
        }

        if (Schema::hasTable('donation_campaigns')) {
            Schema::table('donation_campaigns', function (Blueprint $table) {
                if (!Schema::hasColumn('donation_campaigns', 'distribution_reports')) {
                    $table->json('distribution_reports')->nullable()->after('status'); // Store PDF/Images as JSON
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn('hash');
        });

        Schema::table('donation_campaigns', function (Blueprint $table) {
            $table->dropColumn('distribution_reports');
        });
    }
};
