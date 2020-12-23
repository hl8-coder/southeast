<?php

namespace App\Http\Requests\Affiliate;

use App\Http\Requests\Request;

class TrackingStatisticRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id'       => 'nullable|integer|exists:tracking_statistics,id',
            'start_at' => 'nullable|date',
            'end_at'   => 'nullable|date',
        ];
    }
}
