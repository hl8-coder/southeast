<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Validation\Rule;

class UserRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->getRequestMethod()) {
            case 'store':
                $rule = [
                    'name'          => [
                        'required', 'between:5,16', 'regex:/^[a-z0-9]+$/',
                        Rule::unique('users')->where(function ($query) {
                            return $query->where('is_agent', false);
                        }),
                    ],
                    'password'      => 'required|string|min:6|confirmed',
                    'country_code'  => 'required|string',
                    'phone'         => [
                        'required', 'string',  'regex:/^[0-9]{1,11}/',
                        function ($attribute, $value, $fail) {
                            if (substr($value, 0, 1) == '0') {
                                $userInfo = UserInfo::where('is_agent', false)->where('phone', substr($value, 1))->first();
                            } else {
                                $userInfo = UserInfo::where('is_agent', false)->where('phone', $value)->first();
                            }
                            if ($userInfo) {
                                $fail(__('validation.exists'));
                            }
                        },
                    ],
                    'full_name'     => ['required', 'string', "not_regex:/\"|\¥|\/|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\.|\/|\;|\'|\`|\-|\=|\\\|\|\//"],
                    'email'         => [
                        'required', 'email',
                        Rule::unique('user_info')->where(function ($query) {
                            return $query->where('is_agent', false);
                        }),
                    ],
                    'currency'      => 'required|exists:currencies,code',
                    'birth_at'      => 'required|date',
                    'address'       => 'nullable|string',
                    'other_contact' => 'nullable|string',
                    'referrer_code' => 'nullable|string|exists:users',
                    'ip'            => 'nullable|ip',
                ];
                if ('VND' == $this->input('currency')) {
                    array_push($rule['phone'], 'digits_between:9,10');
                } else {
                    array_push($rule['phone'], 'digits_between:1,10');
                }
                return $rule;
                break;
            case 'update':
                return [
                    'password'                 => 'required|string|max:20',
                    'odds'                     => 'nullable|in:' . get_validate_in_string(User::$odds),
                    'gender'                   => 'nullable|in:' . get_validate_in_string(UserInfo::$genders),
                    'address'                  => 'nullable|string|max:512',
                    'security_question'        => 'nullable|in:' . get_validate_in_string(User::$securityQuestions),
                    'security_question_answer' => 'nullable|string|max:1024',
                ];
                break;
            case 'updateProfile':
                return [
                    'address'                  => 'nullable|string|max:512',
                    'security_question'        => 'nullable|in:' . get_validate_in_string(User::$securityQuestions),
                    'security_question_answer' => 'nullable|string|max:1024',
                ];
                break;
            case 'changePassword':
                return [
                    'old_password' => 'required|string|min:6',
                    'new_password' => 'required|different:old_password|string|min:6|confirmed',
                ];
                break;
            case 'forgetPassword':
                return [
                    'name'  => 'required|string|exists:users',
                    'email' => 'required|email',
                ];
                break;
            case 'checkFieldUnique':
                return [
                    'field' => 'nullable|string|in:name,email,phone',
                    'value' => 'nullable|string',
                ];
                break;
            case 'transfer':
                return [
                    'amount' => 'required|numeric',
                ];
                break;
            case 'claimVerifyPrize':
                return [
                    'platform_code' => 'required|exists:game_platforms,code',
                ];
                break;
            default:
                return [];
                break;
        }

    }

    public function attributes()
    {
        return [
            'name'                     => __('request/api/user.name'),
            'password'                 => __('request/api/user.password'),
            'country_code'             => __('request/api/user.country_code'),
            'phone'                    => __('request/api/user.phone'),
            'full_name'                => __('request/api/user.full_name'),
            'email'                    => __('request/api/user.email'),
            'birth_at'                 => __('request/api/user.birth_at'),
            'address'                  => __('request/api/user.address'),
            'other_contact'            => __('request/api/user.other_contact'),
            'referrer_code'            => __('request/api/user.referrer_code'),
            'odds'                     => __('request/api/user.odds'),
            'gender'                   => __('request/api/user.gender'),
            'security_question'        => __('request/api/user.security_question'),
            'security_question_answer' => __('request/api/user.security_question_answer'),
            'old_password'             => __('request/api/user.old_password'),
            'new_password'             => __('request/api/user.new_password'),
        ];
    }

    public function messages()
    {
        return [
            'platform_code.required' => __('request/api/user.platform_code_required'),
        ];
    }
}
