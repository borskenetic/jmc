<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        Password::defaults(fn () => Password::min(6));

        // Set PHP's default timezone to Asia/Manila
        date_default_timezone_set(Config::get('app.timezone'));

        // Optional: Set Carbon locale if you use translated dates
        Carbon::setLocale(Config::get('app.locale'));
    }
}
