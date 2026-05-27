<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
            // Only admin users can manage polls (create/edit/delete)
            Gate::define('manage-polls', function ($user) {
                // Adjust this check if your User model uses a different role system
                return $user->hasRole('admin');
            });
    }
}
