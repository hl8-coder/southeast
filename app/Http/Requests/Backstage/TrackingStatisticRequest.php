<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;

class TrackingStatisticRequest extends Request
{
    public function rules()
    {
        return [
            'tracking_name' => 'required|string|unique:tracking_statistics',
        ];
    }
}
