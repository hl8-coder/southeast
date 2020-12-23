<?php

namespace App\Http\Requests;

class ActionRequest extends Request
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
                    'name'    => 'required|string',
                    'menu_id' => 'required|integer|exists:menus,id',
                    'action'  => 'required|string|unique:actions,action',
                    'sort'    => 'integer|min:0',
                ];
                break;
            case 'update':
                return [
                    'name'    => 'string',
                    'menu_id' => 'integer|exists:menus,id',
                    'sort'    => 'integer|min:0',
                    'remark'  => 'nullable|string',
                ];
                break;
            case 'storeAction':
                return [
                    'menu_id'  => 'required|exists:menus,id',
                    'route_id' => 'required|exists:routes,id',
                ];
                break;
        }
        return [];

    }
}
