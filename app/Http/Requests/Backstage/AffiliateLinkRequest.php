<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\AffiliateLink;
use App\Models\Currency;
use App\Models\GamePlatformProduct;
use App\Models\Language;

class AffiliateLinkRequest extends Request
{
    public function rules()
    {
        $currencies = Currency::getAll()->pluck('code')->toArray();
        $languages  = Language::getAll()->pluck('code')->toArray();
        switch ($this->getRequestMethod())
        {
            case 'store':
                return [
                    'type'                 => 'required|integer|in:' . implode(',', array_keys(AffiliateLink::$type)),
                    'platform'             => 'required|integer|in:' . implode(',', array_keys(AffiliateLink::$platform)),
                    'link'                 => 'required|string',
                    'sort'                 => 'required|integer',
                    'currencies'           => 'required|array',
                    'currencies.*'         => 'required|in:' . implode(',', $currencies),
                    'languages'            => 'required|array',
                    'languages.*.language' => 'required|in:' . implode(',', $languages),
                    'languages.*.title'    => 'required|string|max:255',
                    'status'               => 'nullable|boolean|in:' . implode(',', array_keys(AffiliateLink::$status)),
                ];
                break;
            case 'update':
                return [
                    'type'                 => 'nullable|integer|in:' . implode(',', array_keys(AffiliateLink::$type)),
                    'platform'             => 'nullable|integer|in:' . implode(',', array_keys(AffiliateLink::$platform)),
                    'link'                 => 'nullable|string',
                    'sort'                 => 'nullable|integer',
                    'currencies'           => 'nullable|array',
                    'currencies.*'         => 'nullable|in:' . implode(',', $currencies),
                    'languages'            => 'nullable|array',
                    'languages.*.language' => 'nullable|in:' . implode(',', $languages),
                    'languages.*.title'    => 'nullable|string|max:255',
                    'status'               => 'nullable|boolean|in:' . implode(',', array_keys(AffiliateLink::$status)),
                ];
                break;
        }
    }
}
