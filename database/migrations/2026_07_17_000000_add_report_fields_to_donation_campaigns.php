<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donation_campaigns', function (Blueprint $table) {
            $table->decimal('total_expense', 15, 2)->default(0)->after('current_amount');
            $table->json('expense_distribution')->nullable()->after('total_expense');
            $table->integer('sponsor_count')->default(0)->after('expense_distribution');
            $table->boolean('show_donor_list')->default(true)->after('sponsor_count');
            $table->string('report_status')->nullable()->after('show_donor_list');
            $table->date('report_verified_at')->nullable()->after('report_status');
            $table->string('lpj_pdf_path')->nullable()->after('report_verified_at');
            $table->string('finance_detail_pdf_path')->nullable()->after('lpj_pdf_path');
            $table->json('documentation_images')->nullable()->after('finance_detail_pdf_path');
        });
    }

    public function down(): void
    {
        Schema::table('donation_campaigns', function (Blueprint $table) {
            $table->dropColumn([
                'total_expense',
                'expense_distribution',
                'sponsor_count',
                'show_donor_list',
                'report_status',
                'report_verified_at',
                'lpj_pdf_path',
                'finance_detail_pdf_path',
                'documentation_images',
            ]);
        });
    }
};
