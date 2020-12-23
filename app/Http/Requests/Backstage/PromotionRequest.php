<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\Currency;
use App\Models\Language;
use App\Models\Promotion;
use Illuminate\Validation\Rule;

class PromotionRequest extends Request
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
        switch ($this->getRequestMethod()) {
            case 'store':
                return [
                    'show_types'                  => 'nullable|array',
                    'promotion_type_code'         => 'required|exists:promotion_types,code',
                    'code'                        => 'required|unique:promotions',
                    'codes'                       => 'nullable|string',
                    'backstage_title'             => 'required|string|max:1024',
                    'display_start_at'            => 'nullable|date',
                    'display_end_at'              => 'nullable|date|after:display_start_at',
                    'web_img_id'                  => 'nullable|integer|exists:images,id',
                    'web_content_img_id'          => 'nullable|integer|exists:images,id',
                    'mobile_img_id'               => 'nullable|integer|exists:images,id',
                    'mobile_content_img_id'       => 'nullable|integer|exists:images,id',
                    'status'                      => 'nullable|boolean',
                    'is_verified'                 => 'nullable|boolean',
                    'is_agent'                    => 'nullable|boolean',
                    'is_can_claim'                => 'nullable|boolean',
                    'related_type'                => 'nullable|in:' . get_validate_in_string(Promotion::$relatedTypes),
                    'sort'                        => 'nullable|integer|min:0',
                    'currencies'                  => 'required|array',
                    'currencies.*'                => 'required|in:' . implode(',', $currencies),
                    'languages'                   => 'required|array',
                    'languages.*.language'        => 'required|in:' . implode(',', $languages),
                    'languages.*.title'           => 'required|string|max:255',
                    'languages.*.description'     => 'required|string|max:1024',
                    'languages.*.content'         => 'required|string',
                    'languages.*.mobile_image_id' => 'nullable|integer',
                ];
                break;
            case 'update':
                return [
                    'show_types'                  => 'nullable|array',
                    'code'                        => [
                        'string',
                        Rule::unique('promotions')->ignore($this->route('promotion')->id),
                    ],
                    'promotion_type_code'         => 'nullable|exists:promotion_types,code',
                    'backstage_title'             => 'nullable|string|max:1024',
                    'codes'                       => 'nullable|string',
                    'display_start_at'            => 'nullable|date',
                    'display_end_at'              => 'nullable|date|after:display_start_at',
                    'web_img_id'                  => 'nullable|integer|exists:images,id',
                    'web_content_img_id'          => 'nullable|integer|exists:images,id',
                    'mobile_img_id'               => 'nullable|integer|exists:images,id',
                    'mobile_content_img_id'       => 'nullable|integer|exists:images,id',
                    'is_verified'                 => 'nullable|boolean',
                    'is_agent'                    => 'nullable|boolean',
                    'status'                      => 'nullable|boolean',
                    'is_can_claim'                => 'nullable|boolean',
                    'sort'                        => 'nullable|integer|min:0',
                    'currencies'                  => 'nullable|array',
                    'related_type'                => 'nullable|in:' . get_validate_in_string(Promotion::$relatedTypes),
                    'currencies.*'                => 'required|in:' . implode(',', $currencies),
                    'languages'                   => 'nullable|array',
                    'languages.*.language'        => 'required|in:' . implode(',', $languages),
                    'languages.*.title'           => 'required|string|max:255',
                    'languages.*.description'     => 'required|string|max:1024',
                    'languages.*.content'         => 'required|string',
                    'languages.*.mobile_image_id' => 'nullable|integer',
                ];
                break;
            default:
                return [];
        }
    }

    public function attributes()
    {
        return [
            'promotion_type_code' => 'Type',
            'code'                => 'Promotion Code',
            'codes'               => 'Related Codes',
            'is_can_claim'        => 'Is Claim',
        ];
    }
}
