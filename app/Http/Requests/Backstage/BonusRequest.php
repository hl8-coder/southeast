<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\Bonus;
use App\Models\Language;
use App\Rules\GtZeroRule;

class BonusRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $languages = Language::getAll()->pluck('code')->toArray();
        switch ($this->getRequestMethod()) {
            case 'store':
                return [
                    'is_claim'                   => 'nullable|boolean',
                    'category'                   => 'required|in:' . implode(',', array_keys(Bonus::$categories)),
                    'languages'                  => 'required|array',
                    'languages.*.title'          => 'required|string|max:1024',
                    'languages.*.language'       => 'required|string|in:' . implode(',', $languages),
                    'code'                       => 'required|string|unique:bonuses',
                    'product_code'               => 'required|exists:game_platform_products,code',
                    'effective_start_at'         => 'required|date',
                    'effective_end_at'           => 'required|date|after:effective_start_at',
                    'sign_start_at'              => 'nullable|date',
                    'sign_end_at'                => 'nullable|date|after:sign_start_at',
                    'status'                     => 'boolean',
                    'bonus_group_id'             => 'required|exists:bonus_groups,id',
                    'type'                       => 'required|in:' . implode(',', array_keys(Bonus::$types)),
                    'rollover'                   => 'required|integer|min:0',
                    'amount'                     => ['required', 'numeric', new GtZeroRule()],
                    'is_auto_hold_withdrawal'    => 'nullable|boolean',
                    'cycle'                      => 'required|integer|in:' . implode(',', array_keys(Bonus::$cycles)),
                    'user_type'                  => 'required|integer|in:' . implode(',', array_keys(Bonus::$userTypes)),
                    'risk_group_ids'             => 'required_if:user_type,' . Bonus::USER_TYPE_RISK . ',' . Bonus::USER_TYPE_RISK_AND_PAYMENT,
                    'payment_group_ids'          => 'required_if:user_type,' . Bonus::USER_TYPE_PAYMENT . ',' . Bonus::USER_TYPE_RISK_AND_PAYMENT,
                    'user_ids'                   => 'required_if:user_type,' . Bonus::USER_TYPE_LIST,
                    'currencies'                 => 'required|array',
                    'currencies.*.currency'      => 'required|exists:currencies,code',
                    'currencies.*.min_transfer'  => 'required|integer|min:0',
                    'currencies.*.deposit_count' => 'required|integer|min:0',
                    'currencies.*.max_prize'     => 'required|integer|min:0',
                ];
                break;

            case 'update':
                return [
                    'is_claim'                   => 'nullable|boolean',
                    'category'                   => 'nullable|in:' . implode(',', array_keys(Bonus::$categories)),
                    'languages'                  => 'nullable|array',
                    'languages.*.title'          => 'required|string|max:1024',
                    'languages.*.language'       => 'required|string|in:' . implode(',', $languages),
                    'product_code'               => 'required|exists:game_platform_products,code',
                    'effective_start_at'         => 'nullable|date',
                    'effective_end_at'           => 'nullable|date|after:effective_start_at',
                    'sign_start_at'              => 'nullable|date',
                    'sign_end_at'                => 'nullable|date|after:sign_start_at',
                    'status'                     => 'boolean',
                    'type'                       => 'nullable|in:' . implode(',', array_keys(Bonus::$types)),
                    'rollover'                   => 'nullable|integer|min:0',
                    'amount'                     => ['required', 'numeric', new GtZeroRule()],
                    'is_auto_hold_withdrawal'    => 'nullable|boolean',
                    'cycle'                      => 'nullable|integer|in:' . implode(',', array_keys(Bonus::$cycles)),
                    'user_type'                  => 'nullable|integer|in:' . implode(',', array_keys(Bonus::$userTypes)),
                    'risk_group_ids'             => 'required_if:user_type,' . Bonus::USER_TYPE_RISK . ',' . Bonus::USER_TYPE_RISK_AND_PAYMENT,
                    'payment_group_ids'          => 'required_if:user_type,' . Bonus::USER_TYPE_PAYMENT . ',' . Bonus::USER_TYPE_RISK_AND_PAYMENT,
                    'user_ids'                   => 'required_if:user_type,' . Bonus::USER_TYPE_LIST,
                    'currencies'                 => 'nullable|array',
                    'currencies.*.currency'      => 'required|exists:currencies,code',
                    'currencies.*.min_transfer'  => 'required|integer|min:0',
                    'currencies.*.deposit_count' => 'required|integer|min:0',
                    'currencies.*.max_prize'     => 'required|integer|min:0',
                    'languages'                  => 'required|array',
                    'languages.*.title'          => 'required|string|max:1024',
                    'languages.*.language'       => 'required|string|in:' . implode(',', $languages),
                ];
                break;
        }
    }
}
