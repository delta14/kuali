<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public const HOME = '/dashboard';

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
        if (request()->header('X-Forwarded-Proto') === 'https' || str_contains(request()->url(), '.loca.lt') || str_contains(request()->url(), '.ngrok')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
