<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('comments')) {
            Schema::table('comments', function (Blueprint $table) {
                // Rename Indonesian "konten" to standard "content" if not already done
                if (Schema::hasColumn('comments', 'konten') && !Schema::hasColumn('comments', 'content')) {
                    $table->renameColumn('konten', 'content');
                }

                // Add polymorphic columns if they don't exist
                if (!Schema::hasColumn('comments', 'commentable_id')) {
                    $table->unsignedBigInteger('commentable_id')->after('user_id')->nullable();
                }
                if (!Schema::hasColumn('comments', 'commentable_type')) {
                    $table->string('commentable_type')->after('commentable_id')->nullable();
                }

                // Drop legacy forum_id and its constraint
                if (Schema::hasColumn('comments', 'forum_id')) {
                    // First drop the foreign key and the index
                    $table->dropForeign('comments_forum_id_foreign');
                    $table->dropColumn('forum_id');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            if (Schema::hasColumn('comments', 'content')) {
                $table->renameColumn('content', 'konten');
            }
            $table->dropColumn(['commentable_id', 'commentable_type']);
            $table->unsignedBigInteger('forum_id')->after('user_id')->nullable();
        });
    }
};
