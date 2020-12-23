<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;

class NewsRequest extends Request
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
                    'currency'  => 'required|string|exists:currencies,name',
                    'title'     => 'required|string',
                    'content'   => 'required|string',
                    'sort'      => 'integer|min:0',
                    'status'    => 'nullable|boolean',
                ];
                break;

            case 'PATCH':
                return [
                    'currency'  => 'nullable|string|exists:currencies,name',
                    'title'     => 'nullable|string',
                    'content'   => 'nullable|string',
                    'sort'      => 'nullable|integer|min:0',
                    'status'    => 'nullable|boolean',
                ];
                break;
        }
    }
}
