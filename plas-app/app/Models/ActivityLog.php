<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Get the user that performed the activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Set entity details.
     *
     * @param  Model  $entity
     * @return $this
     */
    public function forEntity(Model $entity)
    {
        $this->entity_type = get_class($entity);
        $this->entity_id = $entity->getKey();
        return $this;
    }

    /**
     * Get the entity model.
     *
     * @return Model|null
     */
    public function getEntity()
    {
        if ($this->entity_type && $this->entity_id) {
            return app($this->entity_type)->find($this->entity_id);
        }
        
        return null;
    }
} 