<?php

namespace App\Models;

class News extends Model
{

    protected $fillable = [
        'currency', 'title', 'content', 'sort', 'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function scopeEnable($query)
    {
        return $query->where('status', true);
    }
}
