<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
    protected $defaultDuration = 300; // 5 minutes (reduced from 1 hour)

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
     * Retrieve an expired item from the cache if possible.
     * This method tries to get a value from the cache storage
     * even if it's technically expired but still physically present.
     * 
     * Note: Only works with some cache drivers like database and file.
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getExpired(string $key, $default = null)
    {
        $prefixedKey = $this->prefix . $key;
        
        // For database cache driver
        if (config('cache.default') === 'database') {
            try {
                $cacheRecord = DB::table(config('cache.stores.database.table', 'cache'))
                    ->where('key', '=', $prefixedKey)
                    ->first();
                
                if ($cacheRecord) {
                    // The value exists but might be expired
                    try {
                        return unserialize($cacheRecord->value);
                    } catch (\Exception $e) {
                        Log::warning("Failed to unserialize expired cache value", [
                            'key' => $key, 
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::warning("Error retrieving expired cache from database", [
                    'key' => $key, 
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // For file cache driver
        if (config('cache.default') === 'file') {
            try {
                $path = storage_path('framework/cache/data/' . sha1($prefixedKey));
                if (file_exists($path)) {
                    $contents = file_get_contents($path);
                    $data = unserialize($contents);
                    
                    // The file exists but might be expired
                    if (is_array($data) && isset($data[0])) {
                        return $data[0];
                    }
                }
            } catch (\Exception $e) {
                Log::warning("Error retrieving expired cache from file", [
                    'key' => $key, 
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return $default;
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
        try {
            return Cache::tags($tags)->flush();
        } catch (\Exception $e) {
            // If tagging is not supported, try to clear known patterns instead
            if (is_array($tags)) {
                foreach ($tags as $tag) {
                    $this->deleteByPattern($tag . '*');
                }
            } else {
                $this->deleteByPattern($tags . '*');
            }
            return true;
        }
    }

    /**
     * Clear cached items by a single tag
     * 
     * @param string $tag
     * @return bool
     */
    public function clearTag(string $tag): bool
    {
        try {
            return Cache::tags($tag)->flush();
        } catch (\Exception $e) {
            // If tagging is not supported, try to clear known patterns instead
            return $this->deleteByPattern($tag . '_*');
        }
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

    /**
     * Remove cache items by pattern
     *
     * @param string $pattern
     * @return bool
     */
    public function deleteByPattern(string $pattern): bool
    {
        $prefixedPattern = $this->prefix . $pattern;
        
        // For database cache, we can't get all keys easily, so let's handle key patterns differently
        if (str_contains($pattern, '*')) {
            // Extract the base key without the wildcard
            $baseKey = str_replace('*', '', $prefixedPattern);
            
            // Handle specific patterns for common entities
            
            // Resources patterns
            if (str_contains($pattern, 'resources_')) {
                // Clear various resource collection caches
                Cache::forget($this->prefix . 'resources_featured_5');
                Cache::forget($this->prefix . 'resources_featured_limit_5');
                Cache::forget($this->prefix . 'resources_top_downloaded_10');
                
                // Clear paginated resources caches - multiple pages
                for ($i = 1; $i <= 10; $i++) {
                    Cache::forget($this->prefix . "resources_public_page{$i}");
                    Cache::forget($this->prefix . "resources_collection_page{$i}");
                }
            }
            
            // News patterns
            if (str_contains($pattern, 'news_') || str_starts_with($pattern, 'news*')) {
                // Clear news article caches
                Cache::forget($this->prefix . 'home_latest_news');
                Cache::forget($this->prefix . 'featured_news');
                
                // Clear pagination cache - multiple pages
                for ($i = 1; $i <= 10; $i++) {
                    Cache::forget($this->prefix . "news_collection_page{$i}");
                }
                
                // If we have access to the database, we can search for keys containing the pattern
                try {
                    $db = DB::connection(config('cache.stores.database.connection'));
                    $keyColumn = 'key';
                    $table = config('cache.stores.database.table', 'cache');
                    
                    // Find cache keys matching the pattern
                    $keys = $db->table($table)
                        ->where($keyColumn, 'like', $baseKey . '%')
                        ->pluck($keyColumn);
                    
                    // Delete matched keys
                    foreach ($keys as $key) {
                        Cache::forget($key);
                    }
                } catch (\Exception $e) {
                    // If database query fails, continue with other methods
                    Log::warning('Failed to search for cache keys: ' . $e->getMessage());
                }
            }
        } else {
            // If it's a specific key (no wildcard), just forget it directly
            Cache::forget($prefixedPattern);
        }
        
        return true;
    }
    
    /**
     * Check if a key matches a wildcard pattern
     *
     * @param string $key
     * @param string $pattern
     * @return bool
     */
    protected function patternMatches(string $key, string $pattern): bool
    {
        // Convert wildcard pattern to regex
        $regex = str_replace('*', '.*', $pattern);
        $regex = '/^' . $regex . '$/';
        
        return (bool) preg_match($regex, $key);
    }
} 