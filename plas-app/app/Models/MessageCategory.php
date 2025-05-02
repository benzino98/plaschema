<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MessageCategory extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'priority',
    ];

    /**
     * Get the contact messages associated with this category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contactMessages(): HasMany
    {
        return $this->hasMany(ContactMessage::class, 'message_category_id');
    }

    /**
     * Scope a query to order categories by priority.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }
}
