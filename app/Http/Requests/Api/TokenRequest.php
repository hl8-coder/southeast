<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class TokenRequest extends Request
{
    public function rules()
    {
        return [
            't_code' => 'required|exists:tokens,code'
        ];
    }
}
