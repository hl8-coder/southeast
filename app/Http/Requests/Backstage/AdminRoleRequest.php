<?php

namespace App\Http\Requests;

class AdminRoleRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'name'        => 'required|string|unique:admin_roles',
                    'description' => 'string',
                    'sort'        => 'integer|min:0',
                    'status'      => 'boolean'
                ];
                break;

            case 'PUT':
                return [
                    'name'        => 'string|unique:admin_roles',
                    'description' => 'string',
                    'sort'        => 'integer|min:0',
                    'status'      => 'boolean'
                ];
                break;
            default:
                return [];
        }
    }
}
