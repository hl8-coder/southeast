<?php

namespace App\Models;

class Image extends Model
{
    public function imageable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->morphTo();
    }
}
