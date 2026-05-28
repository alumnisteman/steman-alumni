<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Service provider for authentication / authorization services.
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Define gate for managing polls, allow admin, editor, alumni
        Gate::define('manage-polls', function ($user) {
            // Allow admin/editor/alumni roles OR specific admin email(s) (case‑insensitive)
            \Illuminate\Support\Facades\Log::info('Gate manage-polls evaluated for user: '.($user->email ?? 'guest'));
            return $user && (
                $user->hasRole(['admin', 'editor', 'alumni', 'staff']) ||
                strtolower($user->email) === 'valingir@gmail.com'
            );
        });
    }
}
