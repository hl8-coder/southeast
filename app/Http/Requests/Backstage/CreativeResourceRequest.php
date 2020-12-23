<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\CreativeResource;
use App\Models\User;

class CreativeResourceRequest extends Request
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
                    'type'            => 'required|integer|in:' . implode(',', array_keys(CreativeResource::$type)),
                    'group'           => 'required|integer|in:' . implode(',', array_keys(CreativeResource::$group)),
                    'size'            => 'required|integer|in:' . implode(',', array_keys(CreativeResource::$size)),
                    'banner_url'      => 'required|string',
                    'currency'        => 'required|exists:currencies,code',
                    'banner_id'       => 'required|integer|exists:images,id',
//                    'devices'         => 'nullable|array',
//                    'devices.*'       => 'required|in:' . get_validate_in_string(User::$devices),
                    'code'            => 'required|string|unique:creative_resources,code',
                ];
                break;
            case 'PATCH':
                return [
                    'type'            => 'nullable|integer|in:' . implode(',', array_keys(CreativeResource::$type)),
                    'group'           => 'nullable|integer|in:' . implode(',', array_keys(CreativeResource::$group)),
                    'size'            => 'nullable|integer|in:' . implode(',', array_keys(CreativeResource::$size)),
                    'banner_url'      => 'nullable|string',
                    'currency'        => 'nullable|exists:currencies,code',
                    'banner_id'       => 'nullable|integer|exists:images,id',
//                    'devices'         => 'nullable|array',
//                    'devices.*'       => 'nullable|in:' . get_validate_in_string(User::$devices),
                ];
                break;
        }
        return [];
    }
}
