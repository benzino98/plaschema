<?php

namespace App\Repositories\Eloquent;

use App\Models\ContactMessage;
use App\Repositories\Contracts\ContactMessageRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentContactMessageRepository implements ContactMessageRepositoryInterface
{
    /**
     * @var ContactMessage
     */
    protected $model;

    /**
     * EloquentContactMessageRepository constructor.
     *
     * @param ContactMessage $model
     */
    public function __construct(ContactMessage $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(): Collection
    {
        return $this->model->with('category')->orderBy('created_at', 'desc')->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('category')->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?ContactMessage
    {
        return $this->model->with(['category', 'respondedBy'])->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): ContactMessage
    {
        return $this->model->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(int $id, array $data): ContactMessage
    {
        $message = $this->findById($id);
        
        if (!$message) {
            throw new \Exception("Contact message with ID {$id} not found");
        }

        $message->update($data);
        return $message;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id): bool
    {
        $message = $this->findById($id);
        
        if (!$message) {
            return false;
        }

        return $message->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function getByStatus(string $status, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('category')
                          ->withStatus($status)
                          ->orderBy('created_at', 'desc')
                          ->paginate($perPage);
    }

    /**
     * {@inheritdoc}
     */
    public function getByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('category')
                          ->where('message_category_id', $categoryId)
                          ->orderBy('created_at', 'desc')
                          ->paginate($perPage);
    }

    /**
     * {@inheritdoc}
     */
    public function getUnread(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('category')
                          ->unread()
                          ->orderBy('created_at', 'desc')
                          ->paginate($perPage);
    }

    /**
     * {@inheritdoc}
     */
    public function markAsRead(int $id): bool
    {
        $message = $this->findById($id);
        
        if (!$message) {
            return false;
        }

        return $message->markAsRead();
    }

    /**
     * {@inheritdoc}
     */
    public function markAsResponded(int $id, int $userId): bool
    {
        $message = $this->findById($id);
        
        if (!$message) {
            return false;
        }

        return $message->markAsResponded($userId);
    }

    /**
     * {@inheritdoc}
     */
    public function archive(int $id): bool
    {
        $message = $this->findById($id);
        
        if (!$message) {
            return false;
        }

        return $message->archive();
    }

    /**
     * {@inheritdoc}
     */
    public function getMessagesReadyForArchiving(int $months = 3): Collection
    {
        return $this->model->readyForArchiving($months)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function archiveOldMessages(int $months = 3): int
    {
        $messages = $this->getMessagesReadyForArchiving($months);
        $count = 0;

        foreach ($messages as $message) {
            if ($message->archive()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * {@inheritdoc}
     */
    public function search(array $criteria, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with('category');

        // Filter by status
        if (isset($criteria['status']) && !empty($criteria['status'])) {
            $query->where('status', $criteria['status']);
        }

        // Filter by category
        if (isset($criteria['category_id']) && !empty($criteria['category_id'])) {
            $query->where('message_category_id', $criteria['category_id']);
        }

        // Filter by date range
        if (isset($criteria['date_from']) && !empty($criteria['date_from'])) {
            $query->where('created_at', '>=', Carbon::parse($criteria['date_from'])->startOfDay());
        }

        if (isset($criteria['date_to']) && !empty($criteria['date_to'])) {
            $query->where('created_at', '<=', Carbon::parse($criteria['date_to'])->endOfDay());
        }

        // Search by keyword
        if (isset($criteria['keyword']) && !empty($criteria['keyword'])) {
            $keyword = $criteria['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                  ->orWhere('email', 'like', '%' . $keyword . '%')
                  ->orWhere('subject', 'like', '%' . $keyword . '%')
                  ->orWhere('message', 'like', '%' . $keyword . '%');
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
} 