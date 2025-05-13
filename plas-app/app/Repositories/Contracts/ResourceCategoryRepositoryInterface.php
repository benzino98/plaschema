<?php

namespace App\Repositories\Contracts;

use App\Models\ResourceCategory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ResourceCategoryRepositoryInterface
{
    /**
     * Get all resource categories.
     *
     * @param array $filters
     * @return Collection
     */
    public function getAll(array $filters = []): Collection;
    
    /**
     * Get paginated resource categories.
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    
    /**
     * Get a resource category by ID.
     *
     * @param int $id
     * @return ResourceCategory|null
     */
    public function getById(int $id): ?ResourceCategory;
    
    /**
     * Get a resource category by slug.
     *
     * @param string $slug
     * @return ResourceCategory|null
     */
    public function getBySlug(string $slug): ?ResourceCategory;
    
    /**
     * Create a new resource category.
     *
     * @param array $data
     * @return ResourceCategory
     */
    public function create(array $data): ResourceCategory;
    
    /**
     * Update a resource category.
     *
     * @param int $id
     * @param array $data
     * @return ResourceCategory
     */
    public function update(int $id, array $data): ResourceCategory;
    
    /**
     * Delete a resource category.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
    
    /**
     * Get active resource categories.
     *
     * @return Collection
     */
    public function getActive(): Collection;
    
    /**
     * Get resource categories ordered by the order field.
     *
     * @return Collection
     */
    public function getOrdered(): Collection;
    
    /**
     * Get resource categories as a hierarchical tree.
     *
     * @return Collection
     */
    public function getHierarchical(): Collection;
} 