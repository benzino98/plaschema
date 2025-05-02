<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ContactMessage extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'message_category_id',
        'status',
        'is_read',
        'auto_archive',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_read' => 'boolean',
        'auto_archive' => 'boolean',
        'responded_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    /**
     * Get the category that the message belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(MessageCategory::class, 'message_category_id');
    }

    /**
     * Get the user who responded to this message.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    /**
     * Scope a query to only include messages with a specific status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include unread messages.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope a query to only include messages that need archiving.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $months
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReadyForArchiving($query, int $months = 3)
    {
        $cutoffDate = Carbon::now()->subMonths($months);
        return $query->where('auto_archive', true)
                     ->where('created_at', '<', $cutoffDate)
                     ->whereNull('archived_at');
    }

    /**
     * Mark the message as read.
     *
     * @return bool
     */
    public function markAsRead(): bool
    {
        if (!$this->is_read) {
            $this->is_read = true;
            if ($this->status === 'new') {
                $this->status = 'read';
            }
            return $this->save();
        }
        return false;
    }

    /**
     * Mark the message as responded.
     *
     * @param int $userId
     * @return bool
     */
    public function markAsResponded(int $userId): bool
    {
        $this->status = 'responded';
        $this->responded_by = $userId;
        $this->responded_at = Carbon::now();
        return $this->save();
    }

    /**
     * Archive the message.
     *
     * @return bool
     */
    public function archive(): bool
    {
        if ($this->archived_at === null) {
            $this->status = 'archived';
            $this->archived_at = Carbon::now();
            return $this->save();
        }
        return false;
    }
}
