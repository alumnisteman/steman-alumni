<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donation_campaigns', function (Blueprint $table) {
            $table->integer('manual_donor_count')->default(0)->after('sponsor_count');
            $table->integer('manual_transaction_count')->default(0)->after('manual_donor_count');
        });
    }

    public function down(): void
    {
        Schema::table('donation_campaigns', function (Blueprint $table) {
            $table->dropColumn(['manual_donor_count', 'manual_transaction_count']);
        });
    }
};
