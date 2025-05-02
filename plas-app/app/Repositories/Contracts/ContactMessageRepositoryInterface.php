<?php

namespace App\Repositories\Contracts;

use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ContactMessageRepositoryInterface
{
    /**
     * Get all contact messages.
     *
     * @return Collection
     */
    public function getAll(): Collection;

    /**
     * Get contact messages paginated.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a contact message by ID.
     *
     * @param int $id
     * @return ContactMessage|null
     */
    public function findById(int $id): ?ContactMessage;

    /**
     * Create a new contact message.
     *
     * @param array $data
     * @return ContactMessage
     */
    public function create(array $data): ContactMessage;

    /**
     * Update a contact message.
     *
     * @param int $id
     * @param array $data
     * @return ContactMessage
     */
    public function update(int $id, array $data): ContactMessage;

    /**
     * Delete a contact message.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Get messages by status.
     *
     * @param string $status
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByStatus(string $status, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get messages by category.
     *
     * @param int $categoryId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get unread messages.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUnread(int $perPage = 15): LengthAwarePaginator;

    /**
     * Mark a message as read.
     *
     * @param int $id
     * @return bool
     */
    public function markAsRead(int $id): bool;

    /**
     * Mark a message as responded.
     *
     * @param int $id
     * @param int $userId
     * @return bool
     */
    public function markAsResponded(int $id, int $userId): bool;

    /**
     * Archive a message.
     *
     * @param int $id
     * @return bool
     */
    public function archive(int $id): bool;

    /**
     * Get messages ready for automatic archiving.
     *
     * @param int $months
     * @return Collection
     */
    public function getMessagesReadyForArchiving(int $months = 3): Collection;

    /**
     * Archive old messages automatically.
     *
     * @param int $months
     * @return int Number of archived messages
     */
    public function archiveOldMessages(int $months = 3): int;

    /**
     * Search for messages.
     *
     * @param array $criteria
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(array $criteria, int $perPage = 15): LengthAwarePaginator;
} 