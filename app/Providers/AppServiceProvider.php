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
        // Only fetch pages if the table exists to avoid errors before migration
        if (\Illuminate\Support\Facades\Schema::hasTable('pages')) {
            $globalPages = \App\Models\Page::where('is_active', true)->select('id', 'title', 'slug')->get();
            \Illuminate\Support\Facades\View::share('globalPages', $globalPages);
        } else {
            \Illuminate\Support\Facades\View::share('globalPages', collect());
        }
    }
}
