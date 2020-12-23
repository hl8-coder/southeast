<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\Currency;
use App\Models\Url;
use App\Models\User;

class UrlRequest extends Request
{
    public function rules()
    {
        switch ($this->getRequestMethod()) {
            case 'store':
                return [
                    'type'         => 'required|in:' . get_validate_in_string(Url::$type),
                    'device'       => 'required|in:' . get_validate_in_string(User::$devices),
                    'platform'     => 'required|in:' . get_validate_in_string(Url::$platform),
                    'currencies'   => 'required|array',
                    'currencies.*' => 'required|in:' . get_validate_in_string(Currency::getDropList()),
                    'address'      => 'required|string',
                    'status'       => 'required|boolean',
                    'remark'       => 'nullable|string',
                ];
                break;
            case 'update':
                return [
                    'type'         => 'nullable|in:' . get_validate_in_string(Url::$type),
                    'device'       => 'nullable|in:' . get_validate_in_string(User::$devices),
                    'platform'     => 'nullable|in:' . get_validate_in_string(Url::$platform),
                    'currencies'   => 'nullable|array',
                    'currencies.*' => 'nullable|in:' . get_validate_in_string(Currency::getDropList()),
                    'address'      => 'nullable|string',
                    'status'       => 'nullable|boolean',
                    'remark'       => 'nullable|string',
                ];
                break;
        }

    }
}
