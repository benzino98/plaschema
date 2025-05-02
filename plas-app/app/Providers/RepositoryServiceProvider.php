<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\MessageCategoryRepositoryInterface;
use App\Repositories\Contracts\ContactMessageRepositoryInterface;
use App\Repositories\Eloquent\EloquentMessageCategoryRepository;
use App\Repositories\Eloquent\EloquentContactMessageRepository;

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
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
