<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            if (!Schema::hasColumn('programs', 'is_event')) {
                $table->boolean('is_event')->default(false)->after('status');
            }
            if (!Schema::hasColumn('programs', 'event_date')) {
                $table->dateTime('event_date')->nullable()->after('is_event');
            }
            if (!Schema::hasColumn('programs', 'event_location')) {
                $table->string('event_location')->nullable()->after('event_date');
            }
            if (!Schema::hasColumn('programs', 'max_slots')) {
                $table->integer('max_slots')->nullable()->after('event_location');
            }
        });

        Schema::table('program_registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('program_registrations', 'ticket_code')) {
                $table->string('ticket_code')->nullable()->unique()->after('status');
            }
            if (!Schema::hasColumn('program_registrations', 'checked_in_at')) {
                $table->timestamp('checked_in_at')->nullable()->after('ticket_code');
            }
            if (!Schema::hasColumn('program_registrations', 'qr_code_path')) {
                $table->string('qr_code_path')->nullable()->after('checked_in_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(['is_event', 'event_date', 'event_location', 'max_slots']);
        });

        Schema::table('program_registrations', function (Blueprint $table) {
            $table->dropColumn(['ticket_code', 'checked_in_at', 'qr_code_path']);
        });
    }
};
