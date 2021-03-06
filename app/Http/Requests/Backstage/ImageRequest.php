<?php

namespace App\Http\Requests\Backstage;

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

}
