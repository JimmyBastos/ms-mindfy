<?php

namespace App\Models;

use App\Models\Base\BasePivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryContent extends BasePivot
{
    protected $fillable = [
        'category_id',
        'content_id',
    ];

    protected function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    protected function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }
}
