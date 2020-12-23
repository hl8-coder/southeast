<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'country',
        'country_code',
        'currency',
        'remark',
    ];
}
