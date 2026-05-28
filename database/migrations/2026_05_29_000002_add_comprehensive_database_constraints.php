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
        // Add foreign key constraints and indexes to critical tables
        
        // Feeds table
        Schema::table('feeds', function (Blueprint $table) {
            if (Schema::hasColumn('feeds', 'user_id') && !Schema::hasIndex('feeds', 'idx_feeds_user_id')) {
                $table->index('user_id', 'idx_feeds_user_id');
            }
            if (Schema::hasColumn('feeds', 'created_at') && !Schema::hasIndex('feeds', 'idx_feeds_created_at')) {
                $table->index('created_at', 'idx_feeds_created_at');
            }
            if (Schema::hasColumn('feeds', 'is_active') && !Schema::hasIndex('feeds', 'idx_feeds_is_active')) {
                $table->index('is_active', 'idx_feeds_is_active');
            }
        });

        // Comments table
        Schema::table('comments', function (Blueprint $table) {
            if (Schema::hasColumn('comments', 'feed_id') && !Schema::hasIndex('comments', 'idx_comments_feed_id')) {
                $table->index('feed_id', 'idx_comments_feed_id');
            }
            if (Schema::hasColumn('comments', 'user_id') && !Schema::hasIndex('comments', 'idx_comments_user_id')) {
                $table->index('user_id', 'idx_comments_user_id');
            }
        });

        // Likes table
        Schema::table('likes', function (Blueprint $table) {
            if (Schema::hasColumn('likes', 'feed_id') && !Schema::hasIndex('likes', 'idx_likes_feed_id')) {
                $table->index('feed_id', 'idx_likes_feed_id');
            }
            if (Schema::hasColumn('likes', 'user_id') && !Schema::hasIndex('likes', 'idx_likes_user_id')) {
                $table->index('user_id', 'idx_likes_user_id');
            }
            // Add unique constraint to prevent duplicate likes
            if (Schema::hasColumn('likes', 'user_id') && Schema::hasColumn('likes', 'feed_id') && !Schema::hasIndex('likes', 'unique_user_feed')) {
                $table->unique(['user_id', 'feed_id'], 'unique_user_feed');
            }
        });

        // Stories table
        Schema::table('stories', function (Blueprint $table) {
            if (Schema::hasColumn('stories', 'user_id') && !Schema::hasIndex('stories', 'idx_stories_user_id')) {
                $table->index('user_id', 'idx_stories_user_id');
            }
            if (Schema::hasColumn('stories', 'expires_at') && !Schema::hasIndex('stories', 'idx_stories_expires_at')) {
                $table->index('expires_at', 'idx_stories_expires_at');
            }
        });

        // Businesses table
        Schema::table('businesses', function (Blueprint $table) {
            if (!Schema::hasIndex('businesses', 'idx_businesses_user_id')) {
                $table->index('user_id', 'idx_businesses_user_id');
            }
            if (Schema::hasColumn('businesses', 'category') && !Schema::hasIndex('businesses', 'idx_businesses_category')) {
                $table->index('category', 'idx_businesses_category');
            }
        });

        // Jobs table
        Schema::table('job_vacancies', function (Blueprint $table) {
            if (!Schema::hasIndex('job_vacancies', 'idx_jobs_user_id')) {
                $table->index('user_id', 'idx_jobs_user_id');
            }
            if (!Schema::hasIndex('job_vacancies', 'idx_jobs_created_at')) {
                $table->index('created_at', 'idx_jobs_created_at');
            }
            if (Schema::hasColumn('job_vacancies', 'status') && !Schema::hasIndex('job_vacancies', 'idx_jobs_status')) {
                $table->index('status', 'idx_jobs_status');
            }
        });

        // Poll votes table
        Schema::table('poll_votes', function (Blueprint $table) {
            if (Schema::hasColumn('poll_votes', 'user_id') && !Schema::hasIndex('poll_votes', 'idx_poll_votes_user_id')) {
                $table->index('user_id', 'idx_poll_votes_user_id');
            }
            if (Schema::hasColumn('poll_votes', 'poll_id') && !Schema::hasIndex('poll_votes', 'idx_poll_votes_poll_id')) {
                $table->index('poll_id', 'idx_poll_votes_poll_id');
            }
            // Add unique constraint to prevent duplicate votes
            if (Schema::hasColumn('poll_votes', 'user_id') && Schema::hasColumn('poll_votes', 'poll_id') && !Schema::hasIndex('poll_votes', 'unique_user_poll')) {
                $table->unique(['user_id', 'poll_id'], 'unique_user_poll');
            }
        });

        // Follows table
        Schema::table('follows', function (Blueprint $table) {
            if (Schema::hasColumn('follows', 'follower_id') && !Schema::hasIndex('follows', 'idx_follows_follower_id')) {
                $table->index('follower_id', 'idx_follows_follower_id');
            }
            if (Schema::hasColumn('follows', 'following_id') && !Schema::hasIndex('follows', 'idx_follows_following_id')) {
                $table->index('following_id', 'idx_follows_following_id');
            }
            // Add unique constraint to prevent duplicate follows
            if (Schema::hasColumn('follows', 'follower_id') && Schema::hasColumn('follows', 'following_id') && !Schema::hasIndex('follows', 'unique_follower_following')) {
                $table->unique(['follower_id', 'following_id'], 'unique_follower_following');
            }
        });

        // Messages table
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'sender_id') && !Schema::hasIndex('messages', 'idx_messages_sender_id')) {
                $table->index('sender_id', 'idx_messages_sender_id');
            }
            if (Schema::hasColumn('messages', 'receiver_id') && !Schema::hasIndex('messages', 'idx_messages_receiver_id')) {
                $table->index('receiver_id', 'idx_messages_receiver_id');
            }
            if (Schema::hasColumn('messages', 'created_at') && !Schema::hasIndex('messages', 'idx_messages_created_at')) {
                $table->index('created_at', 'idx_messages_created_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes and constraints
        Schema::table('feeds', function (Blueprint $table) {
            $table->dropIndex(['idx_feeds_user_id']);
            $table->dropIndex(['idx_feeds_created_at']);
            $table->dropIndex(['idx_feeds_is_active']);
        });

        Schema::table('comments', function (Blueprint $table) {
            if (Schema::hasIndex('comments', 'idx_comments_feed_id')) {
                $table->dropIndex(['idx_comments_feed_id']);
            }
            if (Schema::hasIndex('comments', 'idx_comments_user_id')) {
                $table->dropIndex(['idx_comments_user_id']);
            }
        });

        Schema::table('likes', function (Blueprint $table) {
            $table->dropIndex(['idx_likes_feed_id']);
            $table->dropIndex(['idx_likes_user_id']);
            $table->dropUnique(['unique_user_feed']);
        });

        Schema::table('stories', function (Blueprint $table) {
            if (Schema::hasIndex('stories', 'idx_stories_user_id')) {
                $table->dropIndex(['idx_stories_user_id']);
            }
            if (Schema::hasIndex('stories', 'idx_stories_expires_at')) {
                $table->dropIndex(['idx_stories_expires_at']);
            }
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->dropIndex(['idx_businesses_user_id']);
            if (Schema::hasIndex('businesses', 'idx_businesses_category')) {
                $table->dropIndex(['idx_businesses_category']);
            }
        });

        Schema::table('job_vacancies', function (Blueprint $table) {
            $table->dropIndex(['idx_jobs_user_id']);
            $table->dropIndex(['idx_jobs_created_at']);
            $table->dropIndex(['idx_jobs_status']);
        });

        Schema::table('poll_votes', function (Blueprint $table) {
            if (Schema::hasIndex('poll_votes', 'idx_poll_votes_user_id')) {
                $table->dropIndex(['idx_poll_votes_user_id']);
            }
            if (Schema::hasIndex('poll_votes', 'idx_poll_votes_poll_id')) {
                $table->dropIndex(['idx_poll_votes_poll_id']);
            }
            if (Schema::hasIndex('poll_votes', 'unique_user_poll')) {
                $table->dropUnique(['unique_user_poll']);
            }
        });

        Schema::table('follows', function (Blueprint $table) {
            if (Schema::hasIndex('follows', 'idx_follows_follower_id')) {
                $table->dropIndex(['idx_follows_follower_id']);
            }
            if (Schema::hasIndex('follows', 'idx_follows_following_id')) {
                $table->dropIndex(['idx_follows_following_id']);
            }
            if (Schema::hasIndex('follows', 'unique_follower_following')) {
                $table->dropUnique(['unique_follower_following']);
            }
        });

        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasIndex('messages', 'idx_messages_sender_id')) {
                $table->dropIndex(['idx_messages_sender_id']);
            }
            if (Schema::hasIndex('messages', 'idx_messages_receiver_id')) {
                $table->dropIndex(['idx_messages_receiver_id']);
            }
            if (Schema::hasIndex('messages', 'idx_messages_created_at')) {
                $table->dropIndex(['idx_messages_created_at']);
            }
        });
    }
};
