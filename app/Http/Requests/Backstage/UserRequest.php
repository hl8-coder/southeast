<?php

namespace App\Http\Requests\Backstage;

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
            case 'update':
                $user = $this->route('user');
                return [
                    'full_name'         => 'nullable|string',
                    'birth_at'          => 'nullable|date',
                    'country_code'      => 'nullable|exists:currencies,country_code',
                    'email' => [
                        'nullable',
                        'email',
                        Rule::unique('user_info')->where(function ($query) {
                            return $query->where('is_agent', false);
                        })->ignore($user->id, 'user_id'),
                    ],
                    'phone' => [
                        'nullable',
                        'integer',
                        Rule::unique('user_info')->where(function ($query) {
                            return $query->where('is_agent', false);
                        })->ignore($user->id, 'user_id'),
                    ],
                    'gender'            => 'nullable|string|in:male,female',
                    'risk_group_id'     => 'nullable|exists:risk_groups,id',
                    'payment_group_id'  => 'nullable|exists:payment_groups,id',
                    'vip_id'            => 'nullable|exists:vips,id',
                    'reward_id'         => 'nullable|exists:rewards,id',
                    'language'          => 'nullable|exists:languages,code'
                ];
                break;
            case 'resetPassword':
                return [
                    'type'         => 'required|in:manual,auto',
                    'new_password' => 'required_if:type,manual|nullable|string',
                ];
                break;
            case 'updateStatus':
                return [
                    'status' => 'required|integer|in:' . implode(',', array_keys(User::$statuses)),
                    'remark' => 'required|string'
                ];
                break;
            case 'updateRiskGroup':
                return [
                    'risk_group_id' => 'required|exists:risk_groups,id',
                    'remark'        => 'required|string|max:1024',
                ];
                break;
            case 'updatePaymentGroup':
                return [
                    'payment_group_id' => 'required|exists:payment_groups,id',
                    'remark'           => 'required|string|max:1024',
                ];
                break;
            case 'updateGameWalletStatus':
                return [
                    'status' => 'required|boolean',
                ];
                break;
            case  'updateReward':
                return [
                    'reward_id' => 'required|exists:rewards,id',
                    'remark'    => 'required|string|max:1024',
                ];
                break;
            case 'resetSecurityQuestion':
                return [
                    'remark' => 'required|string|max:1024',
                ];
                break;
            case 'showUserByName':
                return [
                    'user_name' => 'required|exists:users,name',
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
}
