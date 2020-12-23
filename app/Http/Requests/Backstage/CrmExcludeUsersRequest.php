<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;

class CrmExcludeUsersRequest extends Request
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
                    'admin_id'     => 'nullable|exists:admins,id',
                    'is_affiliate' => 'nullable|boolean',
                    'status'       => 'nullable|boolean',
                    'user_name'    => 'nullable|string',
                    'review_by'    => 'nullable|string',
                ];
                break;
            case 'store':
                return [
                    'user_name'    => 'required|exists:users,name',
                    'is_affiliate' => 'required|boolean',
                ];
                break;
            case 'update':
                return [
                    'status' => 'required|boolean',
                ];
                break;
            case 'delete':
                return [];
                break;
        }
        return [];
    }
}
