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
        Schema::table('businesses', function (Blueprint $table) {
            if (!Schema::hasColumn('businesses', 'offers_alumni_discount')) {
                $table->boolean('offers_alumni_discount')->default(false)->after('status');
            }
            if (!Schema::hasColumn('businesses', 'discount_details')) {
                $table->string('discount_details')->nullable()->after('offers_alumni_discount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            if (Schema::hasColumn('businesses', 'offers_alumni_discount')) {
                $table->dropColumn('offers_alumni_discount');
            }
            if (Schema::hasColumn('businesses', 'discount_details')) {
                $table->dropColumn('discount_details');
            }
        });
    }
};
