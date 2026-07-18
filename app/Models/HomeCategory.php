<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class HomeCategory extends Model
{
    protected $table = 'home_categories';

    protected $fillable = [
        'name',
        'count',
        'gradient',
        'image_path',
        'image_url',
        'status',
        'order',
    ];

    protected static function booted()
    {
        static::saved(fn () => Cache::forget('home_categories'));
        static::deleted(fn () => Cache::forget('home_categories'));
    }

    /**
     * Get the final image URL depending on whether a file is uploaded or a URL is supplied.
     */
    public function getFinalImageUrlAttribute()
    {
        if ($this->image_path) {
            return asset('images/categories/'.$this->image_path);
        }

        return $this->image_url;
    }
}
