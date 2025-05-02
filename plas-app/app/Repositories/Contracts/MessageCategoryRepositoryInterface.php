<?php

namespace App\Repositories\Contracts;

use App\Models\MessageCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface MessageCategoryRepositoryInterface
{
    /**
     * Get all message categories.
     *
     * @return Collection
     */
    public function getAll(): Collection;

    /**
     * Get all message categories ordered by priority.
     *
     * @return Collection
     */
    public function getAllOrderedByPriority(): Collection;

    /**
     * Get message categories paginated.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a message category by ID.
     *
     * @param int $id
     * @return MessageCategory|null
     */
    public function findById(int $id): ?MessageCategory;

    /**
     * Find a message category by slug.
     *
     * @param string $slug
     * @return MessageCategory|null
     */
    public function findBySlug(string $slug): ?MessageCategory;

    /**
     * Create a new message category.
     *
     * @param array $data
     * @return MessageCategory
     */
    public function create(array $data): MessageCategory;

    /**
     * Update a message category.
     *
     * @param int $id
     * @param array $data
     * @return MessageCategory
     */
    public function update(int $id, array $data): MessageCategory;

    /**
     * Delete a message category.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
} 