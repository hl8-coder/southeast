<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class AccessLogsRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'affiliate_code' => 'nullable|string',
            'url'            => 'nullable|string',
            'tracking_id'    => 'nullable|exists:tracking_statistics,id',
        ];
    }
}
