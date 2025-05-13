<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ResourceCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'order',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the resources associated with this category.
     */
    public function resources()
    {
        return $this->hasMany(Resource::class, 'category_id');
    }

    /**
     * Get the parent category.
     */
    public function parent()
    {
        return $this->belongsTo(ResourceCategory::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children()
    {
        return $this->hasMany(ResourceCategory::class, 'parent_id');
    }

    /**
     * Generate a slug from the name attribute.
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by the order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Generate a unique cache key for this resource.
     */
    public function cacheKey()
    {
        return "resource_category_{$this->id}";
    }

    /**
     * Generate a unique cache key for a collection of resources.
     */
    public static function collectionCacheKey(array $filters = [])
    {
        $key = 'resource_categories';
        
        if (!empty($filters)) {
            $filterString = json_encode($filters);
            $key .= '_' . md5($filterString);
        }
        
        return $key;
    }
} 