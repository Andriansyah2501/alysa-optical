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
        // Force HTTPS untuk seluruh aset dan form route di balik Cloudflare Tunnel
        if (config('app.env') === 'production' || str_contains(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
        // Bagikan seluruh StoreSettings ke semua view (Header, Footer, Layouts)
        view()->composer('*', function ($view) {
            if (\Illuminate\Support\Facades\Schema::hasTable('store_settings')) {
                $settings = \App\Models\StoreSetting::all()->pluck('value', 'key');
                $view->with('globalSettings', $settings);
            }
        });
        //
    }
}
