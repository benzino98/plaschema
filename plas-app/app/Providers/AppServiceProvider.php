<?php

namespace App\Providers;

use App\Models\Faq;
use App\Models\News;
use App\Models\ResourceCategory;
use App\Observers\FaqObserver;
use App\Observers\NewsObserver;
use App\Observers\ResourceCategoryObserver;
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
        Faq::observe(FaqObserver::class);
        ResourceCategory::observe(ResourceCategoryObserver::class);
    }
}
