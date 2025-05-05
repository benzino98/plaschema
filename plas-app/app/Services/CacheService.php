<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CacheService
{
    /**
     * Default cache duration in seconds
     * 
     * @var int
     */
    protected $defaultDuration = 3600; // 1 hour

    /**
     * Cache key prefix for better organization
     * 
     * @var string
     */
    protected $prefix = 'app:';

    /**
     * Get an item from the cache or store the default value
     * 
     * @param string $key
     * @param int|null $duration
     * @param callable $callback
     * @return mixed
     */
    public function remember(string $key, ?int $duration, callable $callback)
    {
        $duration = $duration ?? $this->defaultDuration;
        $prefixedKey = $this->prefix . $key;
        
        return Cache::remember($prefixedKey, $duration, $callback);
    }

    /**
     * Store an item in the cache
     * 
     * @param string $key
     * @param mixed $value
     * @param int|null $duration
     * @return bool
     */
    public function put(string $key, $value, ?int $duration = null): bool
    {
        $duration = $duration ?? $this->defaultDuration;
        $prefixedKey = $this->prefix . $key;
        
        return Cache::put($prefixedKey, $value, $duration);
    }

    /**
     * Retrieve an item from the cache
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $prefixedKey = $this->prefix . $key;
        
        return Cache::get($prefixedKey, $default);
    }

    /**
     * Remove an item from the cache
     * 
     * @param string $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        $prefixedKey = $this->prefix . $key;
        
        return Cache::forget($prefixedKey);
    }

    /**
     * Clear a group of cached items by tag
     * 
     * @param string|array $tags
     * @return bool
     */
    public function clearByTags($tags): bool
    {
        return Cache::tags($tags)->flush();
    }

    /**
     * Create a cache key for a model
     * 
     * @param Model $model
     * @param string $suffix
     * @return string
     */
    public function modelKey(Model $model, string $suffix = ''): string
    {
        $className = class_basename($model);
        $id = $model->getKey();
        $key = strtolower($className) . ':' . $id;
        
        if ($suffix) {
            $key .= ':' . $suffix;
        }
        
        return $key;
    }

    /**
     * Create a cache key for a collection of models
     * 
     * @param string $modelClass
     * @param array $params
     * @return string
     */
    public function collectionKey(string $modelClass, array $params = []): string
    {
        $className = class_basename($modelClass);
        $key = strtolower($className) . ':collection';
        
        if (!empty($params)) {
            $key .= ':' . md5(serialize($params));
        }
        
        return $key;
    }

    /**
     * Clear cache when a model is updated
     * 
     * @param Model $model
     * @return void
     */
    public function clearModelCache(Model $model): void
    {
        $className = class_basename($model);
        $modelType = strtolower($className);
        
        // Clear specific model cache
        $this->forget($this->modelKey($model));
        
        // Clear collection caches that might include this model
        $this->clearByTags([$modelType, 'collections']);
    }

    /**
     * Cache a paginated result
     *
     * @param string $key
     * @param LengthAwarePaginator $paginator
     * @param int|null $duration
     * @return LengthAwarePaginator
     */
    public function cachePaginator(string $key, LengthAwarePaginator $paginator, ?int $duration = null): LengthAwarePaginator
    {
        $this->put($key, $paginator, $duration);
        return $paginator;
    }
    
    /**
     * Cache a collection
     *
     * @param string $key
     * @param Collection $collection
     * @param int|null $duration
     * @return Collection
     */
    public function cacheCollection(string $key, Collection $collection, ?int $duration = null): Collection
    {
        $this->put($key, $collection, $duration);
        return $collection;
    }
} 