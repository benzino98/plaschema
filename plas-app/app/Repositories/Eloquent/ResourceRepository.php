<?php

namespace App\Repositories\Eloquent;

use App\Models\Resource;
use App\Repositories\Contracts\ResourceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ResourceRepository implements ResourceRepositoryInterface
{
    /**
     * @var Resource
     */
    protected $model;

    /**
     * ResourceRepository constructor.
     *
     * @param Resource $model
     */
    public function __construct(Resource $model)
    {
        $this->model = $model;
    }

    /**
     * Get all resources.
     *
     * @param array $filters
     * @return Collection
     */
    public function getAll(array $filters = []): Collection
    {
        $query = $this->buildQueryFromFilters($filters);
        return $query->get();
    }

    /**
     * Get paginated resources.
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->buildQueryFromFilters($filters);
        return $query->paginate($perPage);
    }

    /**
     * Get a resource by ID.
     *
     * @param int $id
     * @return Resource|null
     */
    public function getById(int $id): ?Resource
    {
        return $this->model->with('category')->find($id);
    }

    /**
     * Get a resource by slug.
     *
     * @param string $slug
     * @return Resource|null
     */
    public function getBySlug(string $slug): ?Resource
    {
        return $this->model->with('category')->where('slug', $slug)->first();
    }

    /**
     * Create a new resource.
     *
     * @param array $data
     * @return Resource
     */
    public function create(array $data): Resource
    {
        return $this->model->create($data);
    }

    /**
     * Update a resource.
     *
     * @param int $id
     * @param array $data
     * @return Resource
     */
    public function update(int $id, array $data): Resource
    {
        $resource = $this->getById($id);
        $resource->update($data);
        return $resource;
    }

    /**
     * Delete a resource.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $resource = $this->getById($id);
        if (!$resource) {
            return false;
        }
        return $resource->delete();
    }

    /**
     * Increment download count for a resource.
     *
     * @param int $id
     * @return bool
     */
    public function incrementDownloadCount(int $id): bool
    {
        $resource = $this->getById($id);
        if (!$resource) {
            return false;
        }
        
        $resource->incrementDownloadCount();
        return true;
    }

    /**
     * Get active resources.
     *
     * @return Collection
     */
    public function getActive(): Collection
    {
        return $this->model->active()->with('category')->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get featured resources.
     *
     * @param int $limit
     * @return Collection
     */
    public function getFeatured(int $limit = 5): Collection
    {
        return $this->model->active()->featured()->with('category')
                          ->orderBy('created_at', 'desc')
                          ->limit($limit)
                          ->get();
    }

    /**
     * Get published resources.
     *
     * @return Collection
     */
    public function getPublished(): Collection
    {
        return $this->model->published()->with('category')
                          ->orderBy('publish_date', 'desc')
                          ->get();
    }

    /**
     * Search resources.
     *
     * @param string $term
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(string $term, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->published()->search($term)
                          ->with('category')
                          ->orderBy('created_at', 'desc')
                          ->paginate($perPage);
    }

    /**
     * Get resources by category.
     *
     * @param int $categoryId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->published()
                          ->where('category_id', $categoryId)
                          ->with('category')
                          ->orderBy('created_at', 'desc')
                          ->paginate($perPage);
    }

    /**
     * Get resources by file type.
     *
     * @param string $fileType
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByFileType(string $fileType, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->published()
                          ->where('file_type', $fileType)
                          ->with('category')
                          ->orderBy('created_at', 'desc')
                          ->paginate($perPage);
    }

    /**
     * Get most downloaded resources.
     *
     * @param int $limit
     * @return Collection
     */
    public function getMostDownloaded(int $limit = 5): Collection
    {
        return $this->model->published()
                          ->with('category')
                          ->orderBy('download_count', 'desc')
                          ->limit($limit)
                          ->get();
    }

    /**
     * Build query from filters.
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function buildQueryFromFilters(array $filters = [])
    {
        $query = $this->model->with('category');

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['is_featured'])) {
            $query->where('is_featured', $filters['is_featured']);
        }

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['file_type'])) {
            $query->where('file_type', $filters['file_type']);
        }

        if (isset($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($query) use ($searchTerm) {
                $query->where('title', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('searchable_content', 'LIKE', "%{$searchTerm}%");
            });
        }

        if (isset($filters['published'])) {
            $query->where('publish_date', '<=', now())
                  ->where('is_active', true);
        }

        if (isset($filters['order_by'])) {
            $query->orderBy($filters['order_by'], $filters['order_direction'] ?? $filters['direction'] ?? 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    /**
     * Get resources related to a specific resource.
     *
     * @param int $resourceId
     * @param int $categoryId
     * @param int $limit
     * @return Collection
     */
    public function getRelated(int $resourceId, int $categoryId, int $limit = 3): Collection
    {
        return $this->model->published()
            ->where('id', '!=', $resourceId)
            ->where('category_id', $categoryId)
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Get resources by filters.
     *
     * @param array $filters
     * @return Collection
     */
    public function getByFilters(array $filters = []): Collection
    {
        $query = $this->buildQueryFromFilters($filters);
        
        if (isset($filters['limit'])) {
            $query->limit($filters['limit']);
        }
        
        return $query->get();
    }
} 