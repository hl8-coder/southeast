<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class ImageRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'image' => 'required|image|mimes:jpeg,bmp,png,gif',
        ];
    }

    public function attributes()
    {
        return [
            'image' => __('request/api/image.image'),
        ];
    }

}
