<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;

class AuthorizationRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|between:4,12|exists:admins',
            'password' => 'required|string|min:6',
        ];
    }
}
