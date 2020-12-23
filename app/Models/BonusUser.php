<?php

namespace App\Models;

class BonusUser extends Model
{
    protected $fillable = [
        'bonus_id', 'user_id', 'child_bonus_code',
    ];

}
