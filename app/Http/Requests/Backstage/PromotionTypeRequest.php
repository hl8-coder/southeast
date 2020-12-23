<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\Currency;
use App\Models\Language;

class PromotionTypeRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $currencies = Currency::getAll()->pluck('code')->toArray();
        $languages  = Language::getAll()->pluck('code')->toArray();

        switch ($this->method()) {
            case 'POST':
                return [
                    'code'                    => 'required|unique:promotion_types',
                    'web_img_id'              => 'required|integer|exists:images,id',
                    'mobile_img_id'           => 'required|integer|exists:images,id',
                    'status'                  => 'nullable|boolean',
                    'sort'                    => 'nullable|integer|min:0',
                    'currencies'              => 'required|array',
                    'currencies.*'            => 'required|in:' . implode(',', $currencies),
                    'languages'               => 'required|array',
                    'languages.*.language'    => 'required|in:' . implode(',', $languages),
                    'languages.*.title'       => 'required|string|max:255',
                    'languages.*.description' => 'required|string|max:1024',
                ];
                break;

            case 'PATCH':
                return [
                    'web_img_id'              => 'nullable|integer|exists:images,id',
                    'mobile_img_id'           => 'nullable|integer|exists:images,id',
                    'status'                  => 'nullable|boolean',
                    'sort'                    => 'nullable|integer|min:0',
                    'currencies'              => 'nullable|array',
                    'currencies.*'            => 'required|in:' . implode(',', $currencies),
                    'languages'               => 'nullable|array',
                    'languages.*.language'    => 'required|in:' . implode(',', $languages),
                    'languages.*.title'       => 'required|string|max:255',
                    'languages.*.description' => 'required|string|max:1024',
                ];
                break;
        }

    }
}
