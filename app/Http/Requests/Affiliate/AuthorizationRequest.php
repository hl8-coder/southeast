<?php

namespace App\Http\Requests\Affiliate;

use App\Http\Requests\Api\AuthorizationRequest as Request;
use App\Repositories\UserRepository;

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
            'name'     => [
                'required',
                'string',
                'between:5,16',
                function ($attribute, $value, $fail) {
                    if (!$user = UserRepository::findAffiliateByName($value)) {
                        return $fail('Incorrect username or password.');
                    }

                    if (!$user->is_agent) {
                        return $fail('Insufficient permissions.');
                    }
                    if (!$user->isCanLogin()) {
                        return $fail(__('authorization.LOGIN_NOT_ALLOWED'));
                    }
                },
            ],
            'password' => 'required|string|min:6',
            'device'   => 'required|in:1,2',
        ];
    }

    public function attributes()
    {
        return [
            'name'     => __('request/affiliate/authorization.name'),
            'password' => __('request/affiliate/authorization.password'),
            'device'   => __('request/affiliate/authorization.device'),
        ];
    }
}
