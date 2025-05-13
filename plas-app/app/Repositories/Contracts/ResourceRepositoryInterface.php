<?php

namespace App\Repositories\Contracts;

use App\Models\Resource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ResourceRepositoryInterface
{
    /**
     * Get all resources.
     *
     * @param array $filters
     * @return Collection
     */
    public function getAll(array $filters = []): Collection;
    
    /**
     * Get paginated resources.
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    
    /**
     * Get a resource by ID.
     *
     * @param int $id
     * @return Resource|null
     */
    public function getById(int $id): ?Resource;
    
    /**
     * Get a resource by slug.
     *
     * @param string $slug
     * @return Resource|null
     */
    public function getBySlug(string $slug): ?Resource;
    
    /**
     * Create a new resource.
     *
     * @param array $data
     * @return Resource
     */
    public function create(array $data): Resource;
    
    /**
     * Update a resource.
     *
     * @param int $id
     * @param array $data
     * @return Resource
     */
    public function update(int $id, array $data): Resource;
    
    /**
     * Delete a resource.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
    
    /**
     * Increment download count for a resource.
     *
     * @param int $id
     * @return bool
     */
    public function incrementDownloadCount(int $id): bool;
    
    /**
     * Get active resources.
     *
     * @return Collection
     */
    public function getActive(): Collection;
    
    /**
     * Get featured resources.
     *
     * @param int $limit
     * @return Collection
     */
    public function getFeatured(int $limit = 5): Collection;
    
    /**
     * Get published resources.
     *
     * @return Collection
     */
    public function getPublished(): Collection;
    
    /**
     * Search resources.
     *
     * @param string $term
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(string $term, int $perPage = 15): LengthAwarePaginator;
    
    /**
     * Get resources by category.
     *
     * @param int $categoryId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator;
    
    /**
     * Get resources by file type.
     *
     * @param string $fileType
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByFileType(string $fileType, int $perPage = 15): LengthAwarePaginator;
    
    /**
     * Get most downloaded resources.
     *
     * @param int $limit
     * @return Collection
     */
    public function getMostDownloaded(int $limit = 5): Collection;
} 