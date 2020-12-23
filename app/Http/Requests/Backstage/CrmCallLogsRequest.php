<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;

class CrmCallLogsRequest extends Request
{
    public function rules()
    {
        switch ($this->getRequestMethod()) {
            case 'store':
                return [
                    'channel'      => 'required|integer',
                    'crm_order_id' => 'required|integer',
                    'call_status'  => 'required|integer',
                ];
                break;
            default:
                return [];
        }
    }
}
