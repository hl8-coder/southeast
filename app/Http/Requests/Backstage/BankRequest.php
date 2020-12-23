<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\Language;

class BankRequest extends Request
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
                    'currency'               => 'required|exists:currencies,code',
                    'name'                   => 'required|string|unique:banks',
                    'code'                   => 'required|string|unique:banks',
                    'min_deposit_amount'     => 'nullable|numeric|min:0',
                    'max_deposit_amount'     => 'nullable|numeric|min:0',
                    'min_withdraw_amount'    => 'nullable|numeric|min:0',
                    'max_withdraw_amount'    => 'nullable|numeric|min:0',
                    'is_auto_deposit'        => 'nullable|boolean',
                    'status'                 => 'nullable|boolean',
                    'image'                  => 'nullable|integer',
                    'languages'              => 'required|array',
                    'languages.*.language'   => 'required|in:' . implode(',', $languages),
                    'languages.*.front_name' => 'required|string|max:255',
                    'languages.*.maintenance_schedules'   => 'nullable|string',
                ];
                break;

            case 'update':
                return [
                    'front_name'             => 'nullable|string|max:255',
                    'min_deposit_amount'     => 'nullable|numeric|min:0',
                    'max_deposit_amount'     => 'nullable|numeric|min:0',
                    'min_withdraw_amount'    => 'nullable|numeric|min:0',
                    'max_withdraw_amount'    => 'nullable|numeric|min:0',
                    'is_auto_deposit'        => 'nullable|boolean',
                    'status'                 => 'nullable|boolean',
                    'languages'              => 'nullable|array',
                    'languages.*.language'   => 'required|in:' . implode(',', $languages),
                    'languages.*.front_name' => 'required|string|max:255',
                    'languages.*.maintenance_schedules'   => 'nullable|string',
                ];
                break;
            default:
                return [];
        }
    }
}
