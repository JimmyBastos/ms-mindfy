<?php

namespace App\Models;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Media extends BaseModel
{
    protected $fillable = [
        'tag',
        'type',
        'name',
        'description',
        'content',
        'priority',
        'metadata',
    ];

    protected $attributes = [
        'content' => '[]'
    ];

    protected $casts = [
        'name'        => 'json',
        'description' => 'json',
        'content'     => 'json',
        'metadata'    => 'json',
    ];

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }
}
