<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Override the Storage::url method to use our StorageHelper
        \Illuminate\Support\Facades\Storage::macro('url', function ($path) {
            return \App\Helpers\StorageHelper::url($path);
        });
    }
}
