<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;
use App\Models\User;
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
            'name'      => [
                'required',
                'string',
                'between:5,16',
                function($attribute, $value, $fail) {
                    if (!$user = UserRepository::findByName($value)) {
                        return $fail(__('authorization.wrong_name_or_password'));
                    }

                    if ($user->is_agent) {
                        return $fail('Insufficient permissions.');
                    }

                    if (!$user->isCanLogin()) {
                        if ($user->status == User::STATUS_BLOCKED) {
                            return $fail(__('authorization.blocked_login_not_allowed'));
                        }
                        return $fail(__('authorization.LOGIN_NOT_ALLOWED'));
                    }
                }
            ],
            'password'  => 'required|string|min:6',
        ];
    }

    public function attributes()
    {
        return [
            'name' => __('request/api/authorization.name'),
            'password' => __('request/api/authorization.password'),
        ];
    }
}
