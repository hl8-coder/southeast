<?php

namespace App\Models;

class UserProductMonthlyReport extends Report
{
    public static function findSum($userId, $field, $date, $vendors = [])
    {
        $query = static::query()->where('user_id', $userId)
                ->where('date', $date);

        if (!empty($vendors)) {
            $query->whereIn('vendor_code', $vendors);
        }

        return $query->sum($field);
    }
}
