<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ActivityLogService
{
    /**
     * Log an activity.
     *
     * @param string $action The action performed
     * @param Model|null $entity The entity affected
     * @param string|null $description Description of the activity
     * @param array|null $oldValues Previous values (for updates)
     * @param array|null $newValues New values (for updates/creates)
     * @return ActivityLog
     */
    public function log(
        string $action, 
        ?Model $entity = null, 
        ?string $description = null, 
        ?array $oldValues = null, 
        ?array $newValues = null
    ): ActivityLog {
        // Make sure we have a user ID even if not authenticated (use 0 for system)
        $userId = Auth::id() ?: 0;
        
        $log = new ActivityLog([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
        
        if ($entity) {
            $log->forEntity($entity);
        }
        
        try {
            $log->save();
            \Illuminate\Support\Facades\Log::info('Activity log saved', [
                'log_id' => $log->id,
                'action' => $action,
                'entity' => $entity ? get_class($entity) . ' #' . $entity->getKey() : 'None',
                'user_id' => $userId,
                'description' => $description
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to save activity log', [
                'exception' => $e->getMessage(),
                'action' => $action,
                'entity' => $entity ? get_class($entity) . ' #' . $entity->getKey() : 'None',
                'user_id' => $userId
            ]);
        }
        
        return $log;
    }
    
    /**
     * Log a model being created.
     *
     * @param Model $model
     * @param string|null $description
     * @return ActivityLog
     */
    public function logCreated(Model $model, ?string $description = null): ActivityLog
    {
        return $this->log(
            'created',
            $model,
            $description ?: 'Created a new ' . class_basename($model),
            null,
            $this->getModelAttributes($model)
        );
    }
    
    /**
     * Log a model being updated.
     *
     * @param Model $model
     * @param array $oldValues
     * @param string|null $description
     * @return ActivityLog
     */
    public function logUpdated(Model $model, array $oldValues, ?string $description = null): ActivityLog
    {
        return $this->log(
            'updated',
            $model,
            $description ?: 'Updated ' . class_basename($model),
            $oldValues,
            $this->getModelAttributes($model)
        );
    }
    
    /**
     * Log a model being deleted.
     *
     * @param Model $model
     * @param string|null $description
     * @return ActivityLog
     */
    public function logDeleted(Model $model, ?string $description = null): ActivityLog
    {
        return $this->log(
            'deleted',
            $model,
            $description ?: 'Deleted ' . class_basename($model),
            $this->getModelAttributes($model),
            null
        );
    }
    
    /**
     * Get attributes for a model that are safe to log.
     *
     * @param Model $model
     * @return array
     */
    protected function getModelAttributes(Model $model): array
    {
        // Remove sensitive data before logging
        $attributes = $model->attributesToArray();
        
        foreach (['password', 'remember_token', 'api_token'] as $sensitiveField) {
            if (isset($attributes[$sensitiveField])) {
                unset($attributes[$sensitiveField]);
            }
        }
        
        return $attributes;
    }
    
    /**
     * Get activity logs filtered by entity type.
     *
     * @param string $entityType The short class name (without namespace) of the entity
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getLogsByEntityType(string $entityType, int $perPage = 20): LengthAwarePaginator
    {
        // Convert to short name if a full class name is provided
        if (class_exists($entityType)) {
            $entityType = class_basename($entityType);
        }
        
        return ActivityLog::forEntityType($entityType)
            ->orderBy('created_at', 'desc')
            ->with('user')
            ->paginate($perPage);
    }
} 