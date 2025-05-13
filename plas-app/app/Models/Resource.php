<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Resource extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'description',
        'category_id',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'searchable_content',
        'download_count',
        'publish_date',
        'is_featured',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'publish_date' => 'date',
    ];

    /**
     * Get the category that owns the resource.
     */
    public function category()
    {
        return $this->belongsTo(ResourceCategory::class, 'category_id');
    }

    /**
     * Generate a slug from the title attribute.
     */
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    /**
     * Format the file size to a human-readable format.
     */
    public function getFormattedFileSizeAttribute()
    {
        $bytes = (int)$this->attributes['file_size'];
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Get the file extension.
     */
    public function getFileExtensionAttribute()
    {
        return pathinfo($this->attributes['file_name'], PATHINFO_EXTENSION);
    }

    /**
     * Increment the download count.
     */
    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }

    /**
     * Scope a query to only include active resources.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured resources.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include published resources.
     */
    public function scopePublished($query)
    {
        return $query->where('publish_date', '<=', now())
                     ->where('is_active', true);
    }

    /**
     * Scope a query to search by title, description, or searchable content.
     */
    public function scopeSearch($query, $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($query) use ($term) {
            $query->where('title', 'LIKE', "%{$term}%")
                  ->orWhere('description', 'LIKE', "%{$term}%")
                  ->orWhere('searchable_content', 'LIKE', "%{$term}%");
        });
    }

    /**
     * Generate a unique cache key for this resource.
     */
    public function cacheKey()
    {
        return "resource_{$this->id}";
    }

    /**
     * Generate a unique cache key for a collection of resources.
     */
    public static function collectionCacheKey(array $filters = [])
    {
        $key = 'resources';
        
        if (!empty($filters)) {
            $filterString = json_encode($filters);
            $key .= '_' . md5($filterString);
        }
        
        return $key;
    }
} 