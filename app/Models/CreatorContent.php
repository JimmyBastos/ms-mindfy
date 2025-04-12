<?php

namespace App\Models;

use App\Models\Base\BasePivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreatorContent extends BasePivot
{
    protected $fillable = [
        'creator_id',
        'content_id',
    ];

    protected function creator(): BelongsTo
    {
        return $this->belongsTo(Creator::class);
    }

    protected function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }
}
