<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\CrmResourceCallLog;

class CrmResourceCallLogsRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->getRequestMethod()) {
            case 'index':
                return [
                    'full_name' => 'nullable|string',
                    'phone'     => 'nullable|string',
                ];
                break;
            case 'store':
                return [
                    'crm_resource_id' => 'required|exists:crm_resources,id',
                    'channel'         => 'required|in:' . get_validate_in_string(CrmResourceCallLog::$channel),
                    'call_status'     => 'required|in:' . get_validate_in_string(CrmResourceCallLog::$call_statuses),
                ];
                break;
            default:
                return [];
                break;
        }
    }
}
