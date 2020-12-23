<?php

namespace App\Http\Requests;

class AdminRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $methodName = $this->getRequestMethod();
        switch ($methodName) {
            case 'store':
                return [
                    'name'             => 'required|between:4,12|regex:/^[A-Za-z0-9\-\_]+$/|unique:admins',
                    'nick_name'        => 'nullable|string',
                    'password'         => 'required|string|min:6',
                    'operate_password' => 'nullable|string|min:6',
                    'language'         => 'required|string',
                    'remarks'          => 'nullable|string|max:255',
                    'sort'             => 'nullable|integer|min:0',
                    'status'           => 'nullable|boolean',
                    'admin_role_ids'   => 'required|array',
                    'admin_role_ids.*' => 'required|integer|exists:admin_roles,id',
                ];
                break;

            case 'changePassword':
            case 'updatePassword':
                return [
                    'password'              => 'required|string|min:6|confirmed',
                    'password_confirmation' => 'required|string|min:6',
                ];
                break;
        }

    }
}
