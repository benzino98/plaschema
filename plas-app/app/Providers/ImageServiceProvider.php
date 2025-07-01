<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\ImageHelper;

class ImageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('imageHelper', function ($app) {
            return new ImageHelper();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Add a Blade directive for image paths
        Blade::directive('image', function ($expression) {
            return "<?php echo \App\Helpers\ImageHelper::url($expression); ?>";
        });
    }
} 