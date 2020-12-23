<?php

namespace App\Http\Requests;

class CreateFundPasswordRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'fund_password' => 'required|string|min:6|confirmed',
        ];
    }

    public function attributes()
    {
        return [
            'fund_password' => __('request/api/createfundpassword.fund_password'),
        ];
    }
}
