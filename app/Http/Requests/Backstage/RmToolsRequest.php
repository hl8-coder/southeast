<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;

class RmToolsRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->getRequestMethod()) {
            case 'userProductReport':
            case 'userProductReportExport':
                break;
            case 'userProductReportDetail':
                return [
                    'user_name'    => 'required|string',
                ];
                break;
            case 'userProductReportDetailDaily':
                return [
                    'user_name'    => 'required|string',
                    'product_code' => 'required|string',
                ];
                break;
        }
        return [
            //
        ];
    }
}
