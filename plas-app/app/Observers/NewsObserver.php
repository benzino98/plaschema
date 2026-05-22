<?php

namespace App\Observers;

use App\Models\News;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;

class NewsObserver
{
    /**
     * The cache service instance.
     */
    protected $cacheService;

    /**
     * Create a new observer instance.
     */
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the News "created" event.
     */
    public function created(News $news): void
    {
        $this->clearCaches($news);
    }

    /**
     * Handle the News "updated" event.
     */
    public function updated(News $news): void
    {
        $this->clearCaches($news);
    }

    /**
     * Handle the News "deleted" event.
     */
    public function deleted(News $news): void
    {
        $this->clearCaches($news);
    }

    /**
     * Handle the News "restored" event.
     */
    public function restored(News $news): void
    {
        $this->clearCaches($news);
    }

    /**
     * Handle the News "force deleted" event.
     */
    public function forceDeleted(News $news): void
    {
        $this->clearCaches($news);
    }

    /**
     * Clear all news-related caches.
     */
    private function clearCaches(?News $news = null): void
    {
        // Clear home page latest news cache
        $this->cacheService->forget('home_latest_news');

        if ($news?->slug) {
            $this->cacheService->forget("news_article_{$news->slug}");
        }

        if ($news?->id) {
            $this->cacheService->forget("related_news_{$news->id}");
        }

        // Clear other news-related caches
        $this->cacheService->clearByTags(['news', 'collections']);
    }
} 