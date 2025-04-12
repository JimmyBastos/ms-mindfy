<?php

namespace App\Models;

use App\Models\Base\BaseModel;

class Category extends BaseModel
{
    protected $fillable = [
        'name',
        'description'
    ];

    protected $casts = [
        'name'        => 'object',
        'description' => 'object'
    ];
}
