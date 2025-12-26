<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Force HTTPS links in production
        if (app()->environment('production')) {
            $appUrl = config('app.url');
            if (!empty($appUrl)) {
                URL::forceRootUrl($appUrl);
            }
            URL::forceScheme('https');
        }
    }
}
