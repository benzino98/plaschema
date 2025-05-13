<?php

namespace App\Repositories\Eloquent;

use App\Models\ResourceCategory;
use App\Repositories\Contracts\ResourceCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ResourceCategoryRepository implements ResourceCategoryRepositoryInterface
{
    /**
     * @var ResourceCategory
     */
    protected $model;

    /**
     * ResourceCategoryRepository constructor.
     *
     * @param ResourceCategory $model
     */
    public function __construct(ResourceCategory $model)
    {
        $this->model = $model;
    }

    /**
     * Get all resource categories.
     *
     * @param array $filters
     * @return Collection
     */
    public function getAll(array $filters = []): Collection
    {
        $query = $this->model->newQuery();

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['parent_id'])) {
            $query->where('parent_id', $filters['parent_id']);
        }

        if (isset($filters['order_by'])) {
            $query->orderBy($filters['order_by'], $filters['order_direction'] ?? 'asc');
        } else {
            $query->orderBy('order', 'asc');
        }

        return $query->get();
    }

    /**
     * Get paginated resource categories.
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['parent_id'])) {
            $query->where('parent_id', $filters['parent_id']);
        }

        if (isset($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($query) use ($searchTerm) {
                $query->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
        }

        if (isset($filters['order_by'])) {
            $query->orderBy($filters['order_by'], $filters['order_direction'] ?? 'asc');
        } else {
            $query->orderBy('order', 'asc');
        }

        return $query->paginate($perPage);
    }

    /**
     * Get a resource category by ID.
     *
     * @param int $id
     * @return ResourceCategory|null
     */
    public function getById(int $id): ?ResourceCategory
    {
        return $this->model->find($id);
    }

    /**
     * Get a resource category by slug.
     *
     * @param string $slug
     * @return ResourceCategory|null
     */
    public function getBySlug(string $slug): ?ResourceCategory
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * Create a new resource category.
     *
     * @param array $data
     * @return ResourceCategory
     */
    public function create(array $data): ResourceCategory
    {
        return $this->model->create($data);
    }

    /**
     * Update a resource category.
     *
     * @param int $id
     * @param array $data
     * @return ResourceCategory
     */
    public function update(int $id, array $data): ResourceCategory
    {
        $category = $this->getById($id);
        $category->update($data);
        return $category;
    }

    /**
     * Delete a resource category.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $category = $this->getById($id);
        if (!$category) {
            return false;
        }
        return $category->delete();
    }

    /**
     * Get active resource categories.
     *
     * @return Collection
     */
    public function getActive(): Collection
    {
        return $this->model->active()->ordered()->get();
    }

    /**
     * Get resource categories ordered by the order field.
     *
     * @return Collection
     */
    public function getOrdered(): Collection
    {
        return $this->model->orderBy('order')->get();
    }

    /**
     * Get resource categories as a hierarchical tree.
     *
     * @return Collection
     */
    public function getHierarchical(): Collection
    {
        // Get all categories with no parent (top level)
        $categories = $this->model->whereNull('parent_id')->ordered()->get();
        
        // Load their children recursively
        $categories->each(function ($category) {
            $this->loadChildrenRecursively($category);
        });
        
        return $categories;
    }
    
    /**
     * Load children recursively for a given category.
     *
     * @param ResourceCategory $category
     * @return void
     */
    protected function loadChildrenRecursively(ResourceCategory $category): void
    {
        $children = $this->model->where('parent_id', $category->id)->ordered()->get();
        
        if ($children->isNotEmpty()) {
            $category->setRelation('children', $children);
            
            $children->each(function ($child) {
                $this->loadChildrenRecursively($child);
            });
        }
    }
} 