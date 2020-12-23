<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Rules\GtZeroRule;
use Illuminate\Validation\Rule;

class RebateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'code'                      => 'required|unique:rebates,code',
                    'product_code'              => 'required|exists:game_platform_products,code',
                    'risk_group_id'             => 'nullable|exists:risk_groups,id',
                    'currencies'                => 'required|array',
                    'currencies.*.currency'     => 'required|exists:currencies,code',
                    'currencies.*.min_prize'    => 'required|integer|min:0',
                    'currencies.*.max_prize'    => 'required|integer|min:0',
                    'vips'                      => 'required|array',
                    'vips.*.vip_id'             => 'required|exists:vips,id',
                    'vips.*.multipiler'         => ['required', 'numeric', new GtZeroRule,],
                    'sort'                      => 'nullable|integer|min:0',
                    'is_manual_send'            => 'nullable|boolean',
                    'status'                    => 'nullable|boolean',
                ];
                break;

            case 'PATCH':
                return [
                    'product_code'              => 'nullable|exists:game_platform_products,code',
                    'risk_group_id'             => 'nullable|exists:risk_groups,id',
                    'currencies'                => 'nullable|array',
                    'currencies.*.currency'     => 'required|exists:currencies,code',
                    'currencies.*.min_prize'    => 'required|integer|min:0',
                    'currencies.*.max_prize'    => 'required|integer|min:0',
                    'vips'                      => 'nullable|array',
                    'vips.*.vip_id'             => 'required|exists:vips,id',
                    'vips.*.multipiler'         => ['required', 'numeric', new GtZeroRule,],
                    'sort'                      => 'nullable|integer|min:0',
                    'is_manual_send'            => 'nullable|boolean',
                    'status'                    => 'nullable|boolean',
                ];
                break;
        }
    }
}
