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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // e.g., 'donation_created', 'donation_verified'
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('meta')->nullable(); // Stores the snapshot of data
            $table->string('hash')->nullable(); // Integrity hash
            $table->timestamps();
        });

        // 2. Add Hash and Proof of Distribution to existing tables
        Schema::table('donations', function (Blueprint $table) {
            $table->string('hash')->nullable()->after('proof_path');
        });

        Schema::table('donation_campaigns', function (Blueprint $table) {
            $table->json('distribution_reports')->nullable()->after('status'); // Store PDF/Images as JSON
        });
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
