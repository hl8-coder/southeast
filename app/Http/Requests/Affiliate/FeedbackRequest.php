<?php

namespace App\Http\Requests\Affiliate;

use App\Http\Requests\Request;

class FeedbackRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'            => [
                'required', 'array'
            ],
            'name.label'      => [
                'required', 'string'
            ],
            'name.value'      => [
                'required', 'string'
            ],
            'user_name'       => [
                'nullable', 'array'
            ],
            'user_name.label' => [
                'nullable', 'string'
            ],
            'user_name.value' => [
                'nullable', 'exists:users,name'
            ],
            'email'           => [
                'nullable', 'array'
            ],
            'email.label'     => [
                'nullable', 'string'
            ],
            'email.value'     => [
                'nullable', 'email'
            ],
            'phone'           => [
                'nullable', 'array'
            ],
            'phone.label'     => [
                'nullable', 'string'
            ],
            'phone.value'     => [
                'nullable', 'string', 'digits_between:1,10', 'regex:/^[0-9]{1,11}/'
            ],
            'message'         => [
                'required', 'array'
            ],
            'message.label'   => [
                'required', 'string'
            ],
            'message.value'   => [
                'required', 'string'
            ],
            'path'            => [
                'nullable', 'string'
            ],
            'captcha_key'     => 'required|string',
            'captcha_code'    => 'required|string',
        ];
    }

    public function attributes()
    {
        return [
            'name'         => __('request/affiliate/feedback.name'),
            'user_name'    => __('request/affiliate/feedback.user_name'),
            'email'        => __('request/affiliate/feedback.email'),
            'phone'        => __('request/affiliate/feedback.phone'),
            'message'      => __('request/affiliate/feedback.message'),
            'captcha_key'  => __('request/affiliate/feedback.captcha_key'),
            'captcha_code' => __('request/affiliate/feedback.captcha_code'),
        ];
    }
}
