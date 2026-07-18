<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'parent_id', 'status'];

    protected static function booted()
    {
        static::saved(fn () => Cache::forget('storefront_categories'));
        static::deleted(fn () => Cache::forget('storefront_categories'));
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function subcategories()
    {
        return $this->children()->with('subcategories');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function builderModels()
    {
        return $this->hasMany(BuilderModel::class);
    }
}
