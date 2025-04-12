<?php

namespace App\Models;

use App\Models\Base\BaseModel;

class Creator extends BaseModel
{
    protected $fillable = [
        'slug',
        'name',
        'biography'
    ];

    protected $casts = [
        'biography' => 'object'
    ];
}
