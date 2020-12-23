<?php

namespace App\Models;


class Route extends Model
{
    protected $fillable = ['name', 'method', 'action', 'remark', 'url', 'location'];

    public function scopeUrl($query, $value)
    {
        return $query->where('url', 'like', '%'. $value .'%');
    }

    public function scopeName($query, $value)
    {
        return $query->where('name', 'like', '%'. $value .'%');
    }
}
