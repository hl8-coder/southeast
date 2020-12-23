<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;

class AddActionsRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'action_ids'    => 'nullable|array',
            'action_ids.*'  => 'required|exists:actions,id',
        ];
    }
}
