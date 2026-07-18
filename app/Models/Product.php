<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'sku',
        'short_description',
        'description',
        'price',
        'sale_price',
        'stock',
        'track_stock',
        'is_featured',
        'status',
        'featured_image',
        'features',
        'colors',
        'sizes',
        'show_price',
    ];

    protected $casts = [
        'features' => 'array',
        'colors' => 'array',
        'sizes' => 'array',
        'show_price' => 'boolean',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order', 'asc');
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }
}
