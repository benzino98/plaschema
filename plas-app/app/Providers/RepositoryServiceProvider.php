<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\MessageCategoryRepositoryInterface;
use App\Repositories\Contracts\ContactMessageRepositoryInterface;
use App\Repositories\Contracts\ResourceCategoryRepositoryInterface;
use App\Repositories\Contracts\ResourceRepositoryInterface;
use App\Repositories\Eloquent\EloquentMessageCategoryRepository;
use App\Repositories\Eloquent\EloquentContactMessageRepository;
use App\Repositories\Eloquent\ResourceCategoryRepository;
use App\Repositories\Eloquent\ResourceRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind message category repository
        $this->app->bind(
            MessageCategoryRepositoryInterface::class,
            EloquentMessageCategoryRepository::class
        );

        // Bind contact message repository
        $this->app->bind(
            ContactMessageRepositoryInterface::class,
            EloquentContactMessageRepository::class
        );

        // Bind resource category repository
        $this->app->bind(
            ResourceCategoryRepositoryInterface::class,
            ResourceCategoryRepository::class
        );

        // Bind resource repository
        $this->app->bind(
            ResourceRepositoryInterface::class,
            ResourceRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
