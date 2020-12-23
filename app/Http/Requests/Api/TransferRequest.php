<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;
use App\Models\GamePlatform;
use App\Repositories\UserRepository;
use App\Rules\GtZeroRule;
use Illuminate\Support\Facades\Log;

class TransferRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $platformCode = UserRepository::getActiveGamePlatformDropList($this->user());

        return [
            'from_platform_code' => 'required|string|in:' . get_validate_in_string($platformCode),
            'to_platform_code'   => 'required|different:from_platform_code|string|in:' . get_validate_in_string($platformCode),
            'amount'             => [
                'required',
                'integer',
                new GtZeroRule(),
            ],
            'bonus_code'         => 'nullable|string|exists:bonuses,code',
        ];
    }

    public function attributes()
    {
        return [
            'from_platform_code' => __('request/api/transfer.from_platform_code'),
            'to_platform_code'   => __('request/api/transfer.to_platform_code'),
            'amount'             => __('request/api/transfer.amount'),
            'bonus_code'         => __('request/api/transfer.bonus_code'),
        ];
    }
}
