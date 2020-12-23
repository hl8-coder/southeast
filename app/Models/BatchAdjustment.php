<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchAdjustment extends Model
{
    public $fillable = [
        'type', 'file', 'upload_by', 'unique_key',
    ];

    public function adjustments()
    {
        return $this->hasMany(Adjustment::class, 'batch_adjustment_id');
    }

    public static function findUniqueKey()
    {
        do {
            $uniqueKey = str_random(24);
        } while (static::query()->where('unique_key', $uniqueKey)->exists());

        return $uniqueKey;
    }
}
