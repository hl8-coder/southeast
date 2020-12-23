<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class PromotionRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->getRequestMethod()) {
            case 'claim':
                return [
                    'code' => 'nullable|string',
                    'front_remak' => 'nullable|string|max:255',
                ];
                break;
        }
    }
}
