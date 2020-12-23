<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\Currency;
use App\Models\Language;
use App\Models\User;

class GamePlatformProductRequest extends Request
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
            case 'update':
                return [
                    'currencies'              => 'nullable|array',
                    'currencies.*'            => 'required|in:' . implode(',', $currencies),
                    'languages'               => 'nullable|array',
                    'languages.*.language'    => 'required|in:' . implode(',', $languages),
                    'languages.*.name'        => 'required|string|max:255',
                    'languages.*.description' => 'required|string|max:1024',
                    'languages*.content'     => 'required|string',
                    'devices'                 => 'nullable|array',
                    'devices.*'               => 'required|in:' . implode(',', array_keys(User::$devices)),
                    'is_close_bonus'          => 'nullable|boolean',
                    'is_close_cash_back'      => 'nullable|boolean',
                    'is_calculate_reward'     => 'nullable|boolean',
                    'is_calculate_cash_back'  => 'nullable|boolean',
                    'is_calculate_rebate'     => 'nullable|boolean',
                    'is_close_adjustment'     => 'nullable|boolean',
                    'is_can_try'              => 'nullable|boolean',
                    'sort'                    => 'nullable|integer|min:0',
                    'status'                  => 'nullable|boolean',
                ];
                break;
        }
    }
}
