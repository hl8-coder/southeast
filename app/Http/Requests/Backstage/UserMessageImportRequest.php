<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;

class UserMessageImportRequest extends Request
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
                    'excel' => 'required|file',
                ];
                break;
        }
    }
}
