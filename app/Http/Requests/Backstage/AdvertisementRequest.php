<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\Advertisement;

class AdvertisementRequest extends Request
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
                    'country_name'      => 'required|exists:countries,name',
                    'web_image_id'      => 'required|exists:images,id',
                    'mobile_img_id'     => 'required|exists:images,id',
                    'login_img_id'      => 'required|exists:images,id',
                    'description'       => 'string|max:1024',
                    'img_link_url'      => 'string',
                    'alone_link_url'    => 'string',
                    'target_type'       => 'integer|in:' . implode(',', array_keys(Advertisement::$targetTypes)),
                    'show_type'         => 'integer|in:' . implode(',', array_keys(Advertisement::$showTypes)),
                    'sort'              => 'integer|min:0',
                    'status'            => 'boolean',
                ];
                break;

            case 'PUT':
                return [
                    'country_name'      => 'exists:countries,name',
                    'web_image_id'      => 'exists:images,id',
                    'mobile_img_id'     => 'exists:images,id',
                    'login_img_id'      => 'exists:images,id',
                    'description'       => 'string|max:1024',
                    'img_link_url'      => 'string',
                    'alone_link_url'    => 'string',
                    'target_type'       => 'in:' . implode(',', array_keys(Advertisement::$targetTypes)),
                    'show_type'         => 'in:' . implode(',', array_keys(Advertisement::$showTypes)),
                    'sort'              => 'integer|min:0',
                    'status'            => 'boolean',
                ];
                break;
        }
    }
}
