<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\Currency;
use App\Models\Language;
use App\Models\User;

class GameRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $methodName = $this->getRequestMethod();
        $currencies = Currency::getAll()->pluck('code')->toArray();
        $languages  = Language::getAll()->pluck('code')->toArray();
        switch ($methodName) {
            case 'store':
                return [
                    'platform_code'           => 'required|exists:game_platforms,code',
                    'product_code'            => 'required|exists:game_platform_products,code',
                    'code'                    => 'required|string|unique:games,code',
                    'devices'                 => 'required|array',
                    'devices.*'               => 'required|in:' . get_validate_in_string(User::$devices),
                    'is_hot'                  => 'nullable|boolean',
                    'is_new'                  => 'nullable|boolean',
                    'is_soon'                 => 'nullable|boolean',
                    'is_iframe'               => 'nullable|boolean',
                    'is_mobile_iframe'        => 'nullable|boolean',
                    'is_using_cookie'         => 'nullable|boolean',
                    'is_close_bonus'          => 'nullable|boolean',
                    'is_close_cash_back'      => 'nullable|boolean',
                    'is_close_adjustment'     => 'nullable|boolean',
                    'is_calculate_reward'     => 'nullable|boolean',
                    'is_calculate_cash_back'  => 'nullable|boolean',
                    'is_calculate_rebate'     => 'nullable|boolean',
                    'remark'                  => 'nullable|string',
                    'sort'                    => 'nullable|integer|min:0',
                    'status'                  => 'nullable|boolean',
                    'currencies'              => 'required|array',
                    'currencies.*'            => 'required|in:' . implode(',', $currencies),
                    'languages'               => 'required|array',
                    'languages.*.language'    => 'required|in:' . implode(',', $languages),
                    'languages.*.name'        => 'required|string|max:255',
                    'languages.*.description' => 'required|string|max:1024',
                    'languages.*.content'     => 'required|string',
                ];
                break;

            case 'update':
                return [
                    'devices'                 => 'nullable|array',
                    'devices.*'               => 'required|in:' . get_validate_in_string(User::$devices),
                    'is_hot'                  => 'nullable|boolean',
                    'is_new'                  => 'nullable|boolean',
                    'is_soon'                 => 'nullable|boolean',
                    'is_iframe'               => 'nullable|boolean',
                    'is_mobile_iframe'        => 'nullable|boolean',
                    'is_using_cookie'         => 'nullable|boolean',
                    'is_close_bonus'          => 'nullable|boolean',
                    'is_close_cash_back'      => 'nullable|boolean',
                    'is_close_adjustment'     => 'nullable|boolean',
                    'is_calculate_reward'     => 'nullable|boolean',
                    'is_calculate_cash_back'  => 'nullable|boolean',
                    'is_calculate_rebate'     => 'nullable|boolean',
                    'remark'                  => 'nullable|string',
                    'sort'                    => 'nullable|integer|min:0',
                    'status'                  => 'nullable|boolean',
                    'currencies'              => 'nullable|array',
                    'currencies.*'            => 'required|in:' . implode(',', $currencies),
                    'languages'               => 'nullable|array',
                    'languages.*.language'    => 'required|in:' . implode(',', $languages),
                    'languages.*.name'        => 'required|string|max:255',
                    'languages.*.description' => 'required|string|max:1024',
                    'languages.*.content'     => 'required|string',
                ];
                break;
        }

        return [];
    }
}
