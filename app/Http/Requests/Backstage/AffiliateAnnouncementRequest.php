<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\AffiliateAnnouncement;
use App\Models\Currency;
use App\Models\Language;

class AffiliateAnnouncementRequest extends Request
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
                    'name'               => 'required|string',
                    'currencies'         => 'required|array',
                    'currencies.*'       => 'required|in:' . get_validate_in_string(Currency::getDropList()),
                    'content'            => 'required|array',
                    'content.*.language' => 'required|string|in:' . get_validate_in_string(Language::getDropList()),
                    'content.*.message'  => 'required|string',
                    'content.*.title'    => 'required|string',
                    'category'           => 'required|in:' . implode(',', array_keys(AffiliateAnnouncement::$categories)),
                    'start_at'           => 'date',
                    'end_at'             => 'date',
                    'sort'               => 'integer|min:0',
                    'status'             => 'boolean',
                    'pop_up'             => 'nullable|boolean',
                ];
                break;

            case 'PATCH':
            case 'PUT':
                return [
                    'name'               => 'nullable|string',
                    'currencies'         => 'required|array',
                    'currencies.*'       => 'required|in:' . get_validate_in_string(Currency::getDropList()),
                    'content'            => 'nullable|array',
                    'content.*.language' => 'required|string|in:' . get_validate_in_string(Language::getDropList()),
                    'content.*.message'  => 'required|string',
                    'content.*.title'    => 'required|string',
                    'category'           => 'nullable|in:' . implode(',', array_keys(AffiliateAnnouncement::$categories)),
                    'start_at'           => 'nullable|date',
                    'end_at'             => 'nullable|date',
                    'sort'               => 'nullable|integer|min:0',
                    'status'             => 'nullable|boolean',
                    'pop_up'             => 'nullable|boolean',
                ];
                break;
        }
    }
}
