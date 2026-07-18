<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_key',
        'section_key',
        'content_data',
    ];

    protected $casts = [
        'content_data' => 'array',
    ];
}
