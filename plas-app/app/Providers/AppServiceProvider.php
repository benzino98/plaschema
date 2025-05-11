<?php

namespace App\Providers;

use App\Models\News;
use App\Observers\NewsObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(RepositoryServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers
        News::observe(NewsObserver::class);
    }
}
