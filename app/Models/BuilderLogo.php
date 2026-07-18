<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuilderLogo extends Model
{
    protected $fillable = [
        'name',
        'category',
        'image_path',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
