<?php

namespace App\Http\Requests\Affiliate;

use App\Http\Requests\Request;

class ReportRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'month' => 'nullable|date'
        ];
    }
}
