<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\Currency;
use App\Models\Language;

class ContactInformationRequest extends Request
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
        switch ($methodName)
        {
            case 'store':
                return [
                    'currencies'           => 'required|array',
                    'currencies.*'         => 'required|in:' . implode(',', $currencies),
                    'languages'            => 'required|array',
                    'languages.*.language' => 'required|in:' . implode(',', $languages),
                    'languages.*.title'    => 'required|string|max:255',
                    'languages.*.content'  => 'required|string',
                    'icon_id'              => 'required|integer|exists:images,id',
                    'api_url'              => 'nullable|string|max:2048',
                    'is_affiliate'         => 'nullable|boolean',
                    'is_enable'            => 'nullable|boolean',
                ];
                break;
            case 'update':
                return [
                    'currencies'           => 'nullable|array',
                    'currencies.*'         => 'nullable|in:' . implode(',', $currencies),
                    'languages'            => 'nullable|array',
                    'languages.*.language' => 'nullable|in:' . implode(',', $languages),
                    'languages.*.title'    => 'nullable|string|max:255',
                    'languages.*.content'  => 'nullable|string',
                    'icon_id'              => 'nullable|integer|exists:images,id',
                    'api_url'              => 'nullable|string|max:2048',
                    'is_affiliate'         => 'nullable|boolean',
                    'is_enable'            => 'nullable|boolean',
                ];
                break;
        }
    }
}
