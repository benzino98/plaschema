<?php

namespace App\Services;

use App\Models\ContactMessage;
use App\Repositories\Contracts\ContactMessageRepositoryInterface;
use App\Repositories\Contracts\MessageCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ContactMessageService
{
    /**
     * @var ContactMessageRepositoryInterface
     */
    protected $messageRepository;

    /**
     * @var MessageCategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var ActivityLogService
     */
    protected $activityLogService;

    /**
     * ContactMessageService constructor.
     *
     * @param ContactMessageRepositoryInterface $messageRepository
     * @param MessageCategoryRepositoryInterface $categoryRepository
     * @param ActivityLogService $activityLogService
     */
    public function __construct(
        ContactMessageRepositoryInterface $messageRepository,
        MessageCategoryRepositoryInterface $categoryRepository,
        ActivityLogService $activityLogService
    ) {
        $this->messageRepository = $messageRepository;
        $this->categoryRepository = $categoryRepository;
        $this->activityLogService = $activityLogService;
    }

    /**
     * Get all contact messages.
     *
     * @return Collection
     */
    public function getAllMessages(): Collection
    {
        return $this->messageRepository->getAll();
    }

    /**
     * Get paginated messages.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedMessages(int $perPage = 15): LengthAwarePaginator
    {
        return $this->messageRepository->getPaginated($perPage);
    }

    /**
     * Find a message by its ID.
     *
     * @param int $id
     * @return ContactMessage|null
     */
    public function findMessageById(int $id): ?ContactMessage
    {
        $message = $this->messageRepository->findById($id);
        
        // If found, automatically mark as read
        if ($message && !$message->is_read) {
            $this->messageRepository->markAsRead($id);
        }
        
        return $message;
    }

    /**
     * Create a new contact message from public form.
     *
     * @param array $data
     * @return ContactMessage
     */
    public function createMessage(array $data): ContactMessage
    {
        try {
            return DB::transaction(function () use ($data) {
                $message = $this->messageRepository->create($data);
                
                // Log the creation (no user if from public form)
                if (Auth::check()) {
                    $this->activityLogService->log(
                        'contact_message',
                        'created',
                        $message->id,
                        "Contact message created"
                    );
                }
                
                return $message;
            });
        } catch (\Exception $e) {
            Log::error('Failed to create contact message: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update a contact message.
     *
     * @param int $id
     * @param array $data
     * @return ContactMessage
     */
    public function updateMessage(int $id, array $data): ContactMessage
    {
        try {
            return DB::transaction(function () use ($id, $data) {
                $message = $this->messageRepository->update($id, $data);
                
                // Log the update
                $this->activityLogService->log(
                    'contact_message',
                    'updated',
                    $message->id,
                    "Contact message updated"
                );
                
                return $message;
            });
        } catch (\Exception $e) {
            Log::error('Failed to update contact message: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a contact message.
     *
     * @param int $id
     * @return bool
     */
    public function deleteMessage(int $id): bool
    {
        try {
            return DB::transaction(function () use ($id) {
                $message = $this->messageRepository->findById($id);
                
                if (!$message) {
                    return false;
                }
                
                $result = $this->messageRepository->delete($id);
                
                if ($result) {
                    // Log the deletion
                    $this->activityLogService->log(
                        'contact_message',
                        'deleted',
                        $id,
                        "Contact message deleted"
                    );
                }
                
                return $result;
            });
        } catch (\Exception $e) {
            Log::error('Failed to delete contact message: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get messages by status.
     *
     * @param string $status
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getMessagesByStatus(string $status, int $perPage = 15): LengthAwarePaginator
    {
        return $this->messageRepository->getByStatus($status, $perPage);
    }

    /**
     * Get messages by category.
     *
     * @param int $categoryId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getMessagesByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->messageRepository->getByCategory($categoryId, $perPage);
    }

    /**
     * Get unread messages.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUnreadMessages(int $perPage = 15): LengthAwarePaginator
    {
        return $this->messageRepository->getUnread($perPage);
    }

    /**
     * Mark a message as read.
     *
     * @param int $id
     * @return bool
     */
    public function markAsRead(int $id): bool
    {
        try {
            $result = $this->messageRepository->markAsRead($id);
            
            if ($result) {
                // Log the status change
                $this->activityLogService->log(
                    'update',
                    auth()->user()->id,
                    'ContactMessage',
                    $id,
                    ['message' => 'Contact message marked as read']
                );
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to mark message as read: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mark a message as responded.
     *
     * @param int $id
     * @return bool
     */
    public function markAsResponded(int $id): bool
    {
        try {
            $userId = Auth::id();
            $result = $this->messageRepository->markAsResponded($id, $userId);
            
            if ($result) {
                // Log the status change
                $this->activityLogService->log(
                    'update',
                    auth()->user()->id,
                    'ContactMessage',
                    $id,
                    ['message' => 'Contact message marked as responded']
                );
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to mark message as responded: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Archive a message.
     *
     * @param int $id
     * @return bool
     */
    public function archiveMessage(int $id): bool
    {
        try {
            $result = $this->messageRepository->archive($id);
            
            if ($result) {
                // Log the archiving
                $this->activityLogService->log(
                    'contact_message',
                    'updated',
                    $id,
                    "Contact message archived"
                );
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to archive message: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Run the automatic archiving of old messages.
     *
     * @param int $months
     * @return int Number of archived messages
     */
    public function runAutoArchiving(int $months = 3): int
    {
        try {
            $count = $this->messageRepository->archiveOldMessages($months);
            
            if ($count > 0) {
                Log::info("Auto-archived {$count} old contact messages");
            }
            
            return $count;
        } catch (\Exception $e) {
            Log::error('Failed to auto-archive messages: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Search for messages using criteria.
     *
     * @param array $criteria
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function searchMessages(array $criteria, int $perPage = 15): LengthAwarePaginator
    {
        return $this->messageRepository->search($criteria, $perPage);
    }

    /**
     * Get message categories for dropdown.
     *
     * @return Collection
     */
    public function getCategories(): Collection
    {
        return $this->categoryRepository->getAllOrderedByPriority();
    }

    /**
     * Get count of unread messages.
     *
     * @return int
     */
    public function getUnreadCount(): int
    {
        return $this->messageRepository->getUnread(1)->total();
    }

    /**
     * Get filtered messages based on criteria.
     *
     * @param string|null $status
     * @param int|null $categoryId
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getFilteredMessages(?string $status = null, ?int $categoryId = null, ?string $dateFrom = null, ?string $dateTo = null)
    {
        $query = ContactMessage::query()->with('category');
        
        // Filter by status
        if ($status) {
            $query->where('status', $status);
        }
        
        // Filter by category
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        
        // Filter by date range
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        
        // Order by newest first
        $query->orderBy('created_at', 'desc');
        
        return $query;
    }

    /**
     * Update a message's status.
     *
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatus(int $id, string $status): bool
    {
        try {
            $message = $this->messageRepository->findById($id);
            
            if (!$message) {
                return false;
            }
            
            $data = ['status' => $status];
            
            // Additional data based on status
            if ($status === 'read' && !$message->is_read) {
                $data['is_read'] = true;
            } else if ($status === 'responded') {
                $data['responded_by'] = Auth::id();
                $data['responded_at'] = now();
            } else if ($status === 'archived') {
                $data['archived_at'] = now();
            }
            
            $this->messageRepository->update($id, $data);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update message status: ' . $e->getMessage());
            throw $e;
        }
    }
} 