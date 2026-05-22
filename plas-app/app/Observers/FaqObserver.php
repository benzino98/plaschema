<?php

namespace App\Observers;

use App\Models\Faq;
use App\Services\CacheService;

class FaqObserver
{
    public function __construct(protected CacheService $cacheService)
    {
    }

    public function created(Faq $faq): void
    {
        $this->clearCaches();
    }

    public function updated(Faq $faq): void
    {
        $this->clearCaches();
    }

    public function deleted(Faq $faq): void
    {
        $this->clearCaches();
    }

    private function clearCaches(): void
    {
        $this->cacheService->forget('faqs_all');
        $this->cacheService->forget('faq_categories');
        $this->cacheService->forget('faqs_plans_page');
        $this->cacheService->deleteByPattern('faqs_*');
    }
}
