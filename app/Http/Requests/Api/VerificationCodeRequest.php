<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class VerificationCodeRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'verification_key' => 'required|string',
            'code'             => 'required|numeric',
            'type'             => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'verification_key' => __('request/api/verificationcode.verification_key'),
            'code'             => __('request/api/verificationcode.code'),
            'type'             => __('request/api/verificationcode.type'),
        ];
    }
}
