<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class HomeBanner extends Model
{
    protected $table = 'banners';

    protected $fillable = [
        'image',
        'status',
        'order',
    ];

    protected static function booted()
    {
        static::saved(fn () => Cache::forget('home_banners'));
        static::deleted(fn () => Cache::forget('home_banners'));
    }
}
