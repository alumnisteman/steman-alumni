<?php
/*
 * Created At: 2026-04-09
 * Purpose: Grand Standardization - Converting all legacy Indonesian column names to standard English for long-term stability.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Standardizing 'users' table
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'graduation_year')) {
                    $table->renameColumn('graduation_year', 'graduation_year');
                }
                if (Schema::hasColumn('users', 'major')) {
                    $table->renameColumn('major', 'major');
                }
                if (Schema::hasColumn('users', 'current_job')) {
                    $table->renameColumn('current_job', 'current_job');
                }
                if (Schema::hasColumn('users', 'company_university')) {
                    $table->renameColumn('company_university', 'company_university');
                }
                if (Schema::hasColumn('users', 'phone_number')) {
                    $table->renameColumn('phone_number', 'phone_number');
                }
                if (Schema::hasColumn('users', 'address')) {
                    $table->renameColumn('address', 'address');
                }
                if (Schema::hasColumn('users', 'profile_picture')) {
                    $table->renameColumn('profile_picture', 'profile_picture');
                }
            });
        }

        // 2. Standardizing 'messages' table
        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $table) {
                if (Schema::hasColumn('messages', 'sender_id')) {
                    $table->renameColumn('sender_id', 'sender_id');
                }
                if (Schema::hasColumn('messages', 'receiver_id')) {
                    $table->renameColumn('receiver_id', 'receiver_id');
                }
                if (Schema::hasColumn('messages', 'target_year')) {
                    $table->renameColumn('target_year', 'target_year');
                }
                if (Schema::hasColumn('messages', 'message')) {
                    $table->renameColumn('message', 'message');
                }
            });
        }
    }

    public function down(): void
    {
        // Reverse 'users' renames
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'graduation_year')) {
                    $table->renameColumn('graduation_year', 'graduation_year');
                }
                if (Schema::hasColumn('users', 'major')) {
                    $table->renameColumn('major', 'major');
                }
                if (Schema::hasColumn('users', 'current_job')) {
                    $table->renameColumn('current_job', 'current_job');
                }
                if (Schema::hasColumn('users', 'company_university')) {
                    $table->renameColumn('company_university', 'company_university');
                }
                if (Schema::hasColumn('users', 'phone_number')) {
                    $table->renameColumn('phone_number', 'phone_number');
                }
                if (Schema::hasColumn('users', 'address')) {
                    $table->renameColumn('address', 'address');
                }
                if (Schema::hasColumn('users', 'profile_picture')) {
                    $table->renameColumn('profile_picture', 'profile_picture');
                }
            });
        }

        // Reverse 'messages' renames
        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $table) {
                if (Schema::hasColumn('messages', 'sender_id')) {
                    $table->renameColumn('sender_id', 'sender_id');
                }
                if (Schema::hasColumn('messages', 'receiver_id')) {
                    $table->renameColumn('receiver_id', 'receiver_id');
                }
                if (Schema::hasColumn('messages', 'target_year')) {
                    $table->renameColumn('target_year', 'target_year');
                }
                if (Schema::hasColumn('messages', 'message')) {
                    $table->renameColumn('message', 'message');
                }
            });
        }
    }
};
