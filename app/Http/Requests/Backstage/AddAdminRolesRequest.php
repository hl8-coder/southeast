<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;

class AddAdminRolesRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'admin_role_ids'    => 'required|array',
            'admin_role_ids.*'  => 'required|exists:admin_roles,id',
        ];
    }
}
