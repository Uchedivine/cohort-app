<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Super admin gate — secretary can do everything
        Gate::before(function (User $user, string $ability) {
            if ($user->hasRole('secretary')) {
                return true;
            }
        });
    }
}