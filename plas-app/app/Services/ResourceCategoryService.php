<?php

namespace App\Services;

use App\Models\ResourceCategory;
use App\Repositories\Contracts\ResourceCategoryRepositoryInterface;
use Exception;

class ResourceCategoryService
{
    /**
     * @var ResourceCategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var CacheService
     */
    protected $cacheService;

    /**
     * @var ActivityLogService
     */
    protected $activityLogService;

    /**
     * ResourceCategoryService constructor.
     *
     * @param ResourceCategoryRepositoryInterface $categoryRepository
     * @param CacheService $cacheService
     * @param ActivityLogService $activityLogService
     */
    public function __construct(
        ResourceCategoryRepositoryInterface $categoryRepository,
        CacheService $cacheService,
        ActivityLogService $activityLogService
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->cacheService = $cacheService;
        $this->activityLogService = $activityLogService;
    }

    /**
     * Get all resource categories.
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(array $filters = [])
    {
        $cacheKey = ResourceCategory::collectionCacheKey($filters);
        
        return $this->cacheService->remember($cacheKey, 3600, function () use ($filters) {
            return $this->categoryRepository->getAll($filters);
        });
    }

    /**
     * Get paginated resource categories.
     *
     * @param int $perPage
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 15, array $filters = [])
    {
        $cacheKey = ResourceCategory::collectionCacheKey(array_merge($filters, ['page' => request()->get('page', 1), 'perPage' => $perPage]));
        
        return $this->cacheService->remember($cacheKey, 3600, function () use ($perPage, $filters) {
            return $this->categoryRepository->getPaginated($perPage, $filters);
        });
    }

    /**
     * Get a resource category by ID.
     *
     * @param int $id
     * @return ResourceCategory|null
     */
    public function getById(int $id)
    {
        $cacheKey = "resource_category_{$id}";
        
        return $this->cacheService->remember($cacheKey, 3600, function () use ($id) {
            return $this->categoryRepository->getById($id);
        });
    }

    /**
     * Get a resource category by slug.
     *
     * @param string $slug
     * @return ResourceCategory|null
     */
    public function getBySlug(string $slug)
    {
        $cacheKey = "resource_category_slug_{$slug}";
        
        return $this->cacheService->remember($cacheKey, 3600, function () use ($slug) {
            return $this->categoryRepository->getBySlug($slug);
        });
    }

    /**
     * Create a new resource category.
     *
     * @param array $data
     * @return ResourceCategory
     */
    public function create(array $data)
    {
        $category = $this->categoryRepository->create($data);
        
        // Log activity
        $this->activityLogService->log(
            'created',
            'resource_category',
            $category->id,
            "Created resource category: {$category->name}"
        );
        
        // Clear cache
        $this->clearCategoryCache();
        
        return $category;
    }

    /**
     * Update a resource category.
     *
     * @param int $id
     * @param array $data
     * @return ResourceCategory
     */
    public function update(int $id, array $data)
    {
        $category = $this->getById($id);
        
        if (!$category) {
            throw new Exception('Resource category not found');
        }
        
        $category = $this->categoryRepository->update($id, $data);
        
        // Log activity
        $this->activityLogService->log(
            'updated',
            'resource_category',
            $category->id,
            "Updated resource category: {$category->name}"
        );
        
        // Clear cache
        $this->clearCategoryCache($id);
        
        return $category;
    }

    /**
     * Delete a resource category.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id)
    {
        $category = $this->getById($id);
        
        if (!$category) {
            return false;
        }
        
        // Check if the category has resources
        if ($category->resources()->count() > 0) {
            throw new Exception('Cannot delete category with associated resources');
        }
        
        // Check if the category has child categories
        if ($category->children()->count() > 0) {
            throw new Exception('Cannot delete category with child categories');
        }
        
        $result = $this->categoryRepository->delete($id);
        
        if ($result) {
            // Log activity
            $this->activityLogService->log(
                'deleted',
                'resource_category',
                $id,
                "Deleted resource category: {$category->name}"
            );
            
            // Clear cache
            $this->clearCategoryCache($id);
        }
        
        return $result;
    }

    /**
     * Get active resource categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActive()
    {
        $cacheKey = 'resource_categories_active';
        
        return $this->cacheService->remember($cacheKey, 3600, function () {
            return $this->categoryRepository->getActive();
        });
    }

    /**
     * Get ordered resource categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrdered()
    {
        $cacheKey = 'resource_categories_ordered';
        
        return $this->cacheService->remember($cacheKey, 3600, function () {
            return $this->categoryRepository->getOrdered();
        });
    }

    /**
     * Get hierarchical resource categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getHierarchical()
    {
        $cacheKey = 'resource_categories_hierarchical';
        
        return $this->cacheService->remember($cacheKey, 3600, function () {
            return $this->categoryRepository->getHierarchical();
        });
    }

    /**
     * Clear resource category cache.
     *
     * @param int|null $categoryId
     * @return void
     */
    protected function clearCategoryCache(?int $categoryId = null)
    {
        // Clear specific category cache if ID is provided
        if ($categoryId) {
            $this->cacheService->forget("resource_category_{$categoryId}");
            
            // Get category to clear slug cache
            $category = $this->categoryRepository->getById($categoryId);
            if ($category) {
                $this->cacheService->forget("resource_category_slug_{$category->slug}");
            }
        }
        
        // Clear collection caches
        $this->cacheService->deleteByPattern('resource_categories_*');
    }
} 