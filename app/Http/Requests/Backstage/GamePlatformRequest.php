<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Repositories\UserRepository;
use App\Rules\GtZeroRule;

class GamePlatformRequest extends Request
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
                return [
                    'icon'                  => 'nullable|string',
                    'request_url'           => 'nullable|url',
                    'report_request_url'    => 'nullable|url',
                    'launcher_request_url'  => 'nullable|url',
                    'rsa_our_private_key'   => 'nullable|string',
                    'rsa_our_public_key'    => 'nullable|string',
                    'rsa_public_key'        => 'nullable|string',
                    'account'               => 'nullable|array',
                    'interval'              => 'nullable|integer',
                    'delay'                 => 'nullable|integer',
                    'limit'                 => 'nullable|integer',
                    'is_auto_transfer'      => 'nullable|boolean',
                    'is_maintain'           => 'nullable|boolean',
                    'is_update_odds'        => 'nullable|boolean',
                    'remark'                => 'nullable|string',
                    'status'                => 'nullable|boolean',
                    'sort'                  => 'nullable|integer|min:0',
                ];
                break;

            case 'transfer':
                if (!$user = UserRepository::findByName($this->get('user_name'))) {
                    error_response(422, 'No Member.');
                }

                $platformCode = UserRepository::getActiveGamePlatformDropList($user);

                return [
                    'user_name'          => 'required',
                    'from_platform_code' => 'required|string|in:' . get_validate_in_string($platformCode),
                    'to_platform_code'   => 'required|different:from_platform_code|string|in:' . get_validate_in_string($platformCode),
                    'amount'             => [
                        'required',
                        'integer',
                        new GtZeroRule(),
                    ],
                    'bonus_code'         => 'nullable|string|exists:bonuses,code',
                ];
                break;
        }
    }
}
