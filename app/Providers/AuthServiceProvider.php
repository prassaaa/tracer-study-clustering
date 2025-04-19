<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Definisikan gate untuk admin
        Gate::define('admin', function ($user) {
            return $user->role === 'admin';
        });

        // Definisikan gate untuk alumni
        Gate::define('alumni', function ($user) {
            return $user->role === 'alumni';
        });
    }
}
