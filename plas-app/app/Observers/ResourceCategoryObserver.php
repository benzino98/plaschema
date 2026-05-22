<?php

namespace App\Observers;

use App\Models\ResourceCategory;
use App\Services\ResourceCategoryService;

class ResourceCategoryObserver
{
    public function __construct(protected ResourceCategoryService $resourceCategoryService)
    {
    }

    public function created(ResourceCategory $category): void
    {
        $this->resourceCategoryService->clearPublicCategoryCache();
    }

    public function updated(ResourceCategory $category): void
    {
        $this->resourceCategoryService->clearPublicCategoryCache();
    }

    public function deleted(ResourceCategory $category): void
    {
        $this->resourceCategoryService->clearPublicCategoryCache();
    }
}
