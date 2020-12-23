<?php

namespace App\Http\Requests;

class MenuRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->getRequestMethod()) {
            case 'store':
                return [
                    'name'      => 'required|string',
                    'parent_id' => 'integer|exists:menus,id',
                    'path'      => 'string',
                    'sort'      => 'integer|min:0',
                    'is_show'   => 'boolean',
                ];
                break;
            case 'update':
                return [
                    'name'      => 'string',
                    'parent_id' => 'integer|exists:menus,id',
                    'path'      => 'string',
                    'sort'      => 'integer|min:0',
                    'is_show'   => 'boolean',
                ];
                break;
        }
        return [];

    }
}
