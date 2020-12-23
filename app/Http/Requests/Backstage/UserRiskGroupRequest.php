<?php

namespace App\Http\Requests\Backstage;


use App\Http\Requests\Request;

class UserRiskGroupRequest extends Request
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
                    'excel'         => 'required|file',
                    'risk_group_id' => 'required|exists:risk_groups,id'
                ];
                break;
            default:
                return [];
        }
    }
}
