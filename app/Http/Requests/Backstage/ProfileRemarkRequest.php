<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\ProfileRemark;

class ProfileRemarkRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'category'  => 'required|integer|in:' . implode(',', array_keys(ProfileRemark::$categories)),
            'remark'    => 'required|string|max:1024',
        ];
    }
}
