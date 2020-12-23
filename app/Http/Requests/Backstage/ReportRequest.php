<?php

namespace App\Http\Requests\Backstage;

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
        switch ($this->getRequestMethod()) {
            case 'activeUserReport':
            case 'activeUserReportByAffiliate':
                return [
                    'currency' => 'nullable|exists:currencies,code',
                    'start_at' => 'nullable|date',
                    'end_at'   => 'nullable|date|after:start_at',
                ];
                break;
            default:
                return [
                    'filter.user_name'    => 'nullable|exists:users,name',
                    'filter.product_code' => 'nullable|exists:game_platform_products,code',
                ];
                break;
        }
    }

    public function messages()
    {
        return [
            'filter.user_name.exists'    => 'Member does not exist.',
            'filter.product_code.exists' => 'Product does not exist.',
        ];
    }
}
