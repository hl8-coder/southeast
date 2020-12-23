<?php

namespace App\Http\Requests\Backstage;

use App\Models\Banner;
use App\Models\Currency;
use App\Models\Language;
use App\Http\Requests\Request;

class BannerRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $methodName = $this->getRequestMethod();
        $languages  = Language::getAll()->pluck('code')->toArray();
        $currencies = Currency::getAll()->pluck('code')->toArray();
        switch ($methodName) {
            case 'store':
                return [
                    'currency'                => 'required|in:' . implode(',', $currencies),
                    'code'                    => 'required|string',
                    'languages'               => 'required|array',
                    'languages.*.title'       => 'required|string|max:255',
                    'languages.*.content'     => 'required|string|max:2048',
                    'languages.*.language'    => 'required|in:' . implode(',', $languages),
                    'languages.*.description' => 'required|string|max:2048',
                    'show_type'               => 'required|integer|in:' . get_validate_in_string(Banner::$showTypes),
                    'position'                => 'required|integer|in:' . get_validate_in_string(Banner::$positions),
                    'target_type'             => 'required|integer|in:' . get_validate_in_string(Banner::$targetTypes),
                    'display_start_at'        => 'required|date',
                    'display_end_at'          => 'required|date|after:display_start_at',
                    'web_img_id'              => 'required|integer|exists:images,id',
                    'mobile_img_id'           => 'required|integer|exists:images,id',
                    'web_link_url'            => 'nullable|string|max:2048',
                    'mobile_link_url'         => 'nullable|string|max:2048',
                    'status'                  => 'nullable|boolean',
                    'sort'                    => 'nullable|integer|min:0',
                ];
                break;

            case 'update':
                return [
                    'code'                    => 'nullable|string',
                    'languages'               => 'nullable|array',
                    'languages.*.title'       => 'required|string|max:255',
                    'languages.*.content'     => 'required|string|max:2048',
                    'languages.*.language'    => 'required|in:' . implode(',', $languages),
                    'languages.*.description' => 'required|string|max:2048',
                    'show_type'               => 'nullable|integer|in:' . get_validate_in_string(Banner::$showTypes),
                    'position'                => 'nullable|integer|in:' . get_validate_in_string(Banner::$positions),
                    'target_type'             => 'nullable|integer|in:' . get_validate_in_string(Banner::$targetTypes),
                    'display_start_at'        => 'nullable|date',
                    'display_end_at'          => 'nullable|date|after:display_start_at',
                    'web_img_id'              => 'nullable|integer|exists:images,id',
                    'mobile_img_id'           => 'nullable|integer|exists:images,id',
                    'web_link_url'            => 'nullable|string|max:2048',
                    'mobile_link_url'         => 'nullable|string|max:2048',
                    'status'                  => 'nullable|boolean',
                    'sort'                    => 'nullable|integer|min:0',
                ];
                break;
        }
    }
}
