<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;

class CrmBoAdminRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->getRequestMethod()) {
            case 'store':
                return [
                    'admin_name'      => 'required|exists:admins,name',
                    'on_duty'         => 'nullable|boolean',
                    'status'          => 'nullable|boolean',
                    'sort'            => 'nullable|integer|min:0',
                    'start_worked_at' => 'date_format:"H:i:s"',
                    'end_worked_at'   => 'date_format:"H:i:s"',
                ];
                break;

            case 'update':
                return [
                    'id'              => 'nullable|exists:crm_bo_admins,id',
                    'status'          => 'nullable|boolean',
                    'on_duty'         => 'nullable|boolean',
                    'sort'            => 'nullable|integer|min:0',
                    'end_worked_at'   => 'nullable|date_format:"H:i:s"',
                    'start_worked_at' => 'nullable|date_format:"H:i:s"',
                ];
                break;
            case 'index':
            case 'destroy':
            case 'show':
            case 'audit':
                return [];
                break;
        }

        return [];
    }
}
