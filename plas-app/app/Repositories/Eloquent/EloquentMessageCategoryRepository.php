<?php

namespace App\Repositories\Eloquent;

use App\Models\MessageCategory;
use App\Repositories\Contracts\MessageCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class EloquentMessageCategoryRepository implements MessageCategoryRepositoryInterface
{
    /**
     * @var MessageCategory
     */
    protected $model;

    /**
     * EloquentMessageCategoryRepository constructor.
     *
     * @param MessageCategory $model
     */
    public function __construct(MessageCategory $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(): Collection
    {
        return $this->model->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllOrderedByPriority(): Collection
    {
        return $this->model->orderByPriority()->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->orderBy('name')->paginate($perPage);
    }

    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?MessageCategory
    {
        return $this->model->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findBySlug(string $slug): ?MessageCategory
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): MessageCategory
    {
        // Generate slug if not provided
        if (!isset($data['slug']) || empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $this->model->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(int $id, array $data): MessageCategory
    {
        $category = $this->findById($id);
        
        if (!$category) {
            throw new \Exception("Message category with ID {$id} not found");
        }

        // Generate slug if name changed and slug not provided
        if (isset($data['name']) && (!isset($data['slug']) || empty($data['slug']))) {
            $data['slug'] = Str::slug($data['name']);
        }

        $category->update($data);
        return $category;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id): bool
    {
        $category = $this->findById($id);
        
        if (!$category) {
            return false;
        }

        return $category->delete();
    }
} 