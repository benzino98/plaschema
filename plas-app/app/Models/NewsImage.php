<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsImage extends Model
{
    protected $fillable = [
        'news_id',
        'image_path',
        'image_path_small',
        'image_path_medium',
        'image_path_large',
        'caption',
        'link_url',
        'sort_order',
        'is_cover',
    ];

    protected $casts = [
        'is_cover' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }
}
