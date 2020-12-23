<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\RiskGroup;

class RiskGroupRequest extends Request
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
                    'remarks' => 'nullable|string',
                    'status'  => 'nullable|boolean',
                    'sort'    => 'nullable|integer|min:0',
                    'rules'   => 'nullable|array',
                    'rules.*' => 'nullable|in:' . get_validate_in_string(RiskGroup::$ruleLists)
                ];
                break;

            case 'update':
                return [
                    'name'    => 'string',
                    'remarks' => 'nullable|string',
                    'status'  => 'nullable|boolean',
                    'sort'    => 'nullable|integer|min:0',
                    'rules'   => 'nullable|array',
                    'rules.*' => 'nullable|in:' . get_validate_in_string(RiskGroup::$ruleLists)
                ];
                break;

            default:
                return [];
        }
    }
}
