<?php

namespace App\Providers;

use App\Services\CacheService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;

class CacheServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(CacheService::class, function ($app) {
            return new CacheService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Add cache clear observers to models
        Model::created(function (Model $model) {
            $this->clearCacheForModel($model);
        });

        Model::updated(function (Model $model) {
            $this->clearCacheForModel($model);
        });

        Model::deleted(function (Model $model) {
            $this->clearCacheForModel($model);
        });
    }

    /**
     * Clear cache for a specific model
     *
     * @param Model $model
     * @return void
     */
    private function clearCacheForModel(Model $model): void
    {
        $cacheService = app(CacheService::class);
        $cacheService->clearModelCache($model);
    }
} 