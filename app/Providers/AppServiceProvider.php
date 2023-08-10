<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Validator::extend('strong_password', function ($value) {
            // Check for at least one uppercase letter, one lowercase letter, and one symbol.
            return preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*[\W_]).+$/', $value);
        });
    }
}
