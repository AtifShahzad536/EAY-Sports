<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuilderPattern extends Model
{
    protected $fillable = [
        'name',
        'image_path',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
