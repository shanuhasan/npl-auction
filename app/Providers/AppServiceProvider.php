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
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            // Only fetch pages if the table exists to avoid errors before migration
            if (\Illuminate\Support\Facades\Schema::hasTable('pages')) {
                $globalPages = \App\Models\Page::where('is_active', true)->select('id', 'title', 'slug')->get();
                $view->with('globalPages', $globalPages);
            } else {
                $view->with('globalPages', collect());
            }
        });
    }
}
