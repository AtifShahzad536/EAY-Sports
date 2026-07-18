<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuilderModel extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'model_url',
        'thumbnail',
        'mapping',
        'layers_metadata',
        'status',
    ];

    protected $casts = [
        'mapping' => 'array',
        'layers_metadata' => 'array',
        'status' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
