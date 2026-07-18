<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ShowcaseVideo extends Model
{
    protected $table = 'showcase_videos';

    protected $fillable = [
        'phase_name',
        'video_path',
        'video_url',
        'status',
        'order',
    ];

    protected static function booted()
    {
        static::saved(fn () => Cache::forget('home_showcase_videos'));
        static::deleted(fn () => Cache::forget('home_showcase_videos'));
    }

    /**
     * Get the final video URL depending on whether a file is uploaded or a URL is supplied.
     */
    public function getFinalVideoUrlAttribute()
    {
        if ($this->video_path) {
            return asset('videos/'.$this->video_path);
        }

        return $this->video_url;
    }
}
