<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;

class CrmResourcesRequest extends Request
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
                    'full_name'                 => 'nullable|string',
                    'phone'                     => 'nullable|string',
                    'country_code'              => 'nullable|string',
                    'admin_name'                => 'nullable|string',
                    'tag_admin_name'            => 'nullable|string',
                    'last_save_case_admin_name' => 'nullable|string',
                    'tag_start'                 => 'nullable|date',
                    'tag_end'                   => 'nullable|date',
                    'last_save_start'           => 'nullable|date',
                    'last_save_end'             => 'nullable|date',
                    'status'                    => 'nullable|boolean',

                ];
                break;
            case 'store':
                return [
                    'excel' => 'required|file',
                ];
                break;
            case 'update':
                return [
                    'crm_resource_ids'   => 'required|array',
                    'crm_resource_ids.*' => 'required|exists:crm_resources,id',
                    'admin_id'           => 'required_if:distribute,1|exists:crm_bo_admins,admin_id',
                    'distribute'         => 'required|boolean',

                ];
                break;
            default:
                return [];
                break;
        }
    }
}
