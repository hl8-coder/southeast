<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;

class VerifiedPrizeBlackUsersRequest extends Request
{
    public function rules()
    {
        switch ($this->getRequestMethod()){
            case 'store':
                return [
                    'user_name' => 'required|exists:users,name'
                ];
                break;
            case 'importByExcel':
                return [
                    'excel' => 'required|file'
                ];
                break;
            case 'delete':
            case 'index':
            case 'excelTemplate':
            default:
                return [];
                break;
        }
    }
}
