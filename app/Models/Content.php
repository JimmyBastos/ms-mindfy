<?php

namespace App\Models;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Content extends BaseModel
{
    protected $fillable = [
        'type',
        'title',
        'description',
        'status',
        'metadata'
    ];

    protected $attributes = [
        'type' => 'audiobook'
    ];

    protected $casts = [
        'title'       => 'object',
        'description' => 'object',
        'metadata'    => 'object'
    ];

    public function cover(): MorphOne
    {
        return $this->morphOne(Media::class, 'owner')
            ->where('tag', 'cover');
    }

    public function preview(): MorphOne
    {
        return $this->morphOne(Media::class, 'owner')
            ->where('tag', 'preview');
    }

    public function blocks(): MorphMany
    {
        return $this->morphMany(Media::class, 'owner')
            ->where('tag', 'block')
            ->orderBy('priority');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Media::class, 'owner')
            ->where('tag', 'attachment')
            ->orderBy('priority');
    }

    public function creators(): BelongsToMany
    {
        return $this->belongsToMany(Creator::class, 'creator_contents')
            ->using(CreatorContent::class)
            ->withTimestamps();
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_contents')
            ->using(CategoryContent::class)
            ->withTimestamps();
    }
}
