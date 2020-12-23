<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;

class BatchRemarkRequest extends Request
{
    public function rules()
    {
        switch ($this->getRequestMethod())
        {
            case 'uploadFile':
                return [
                    'file' => 'required|file',
                ];
                break;
            default:
                return [
                    'file' => 'required|string',
                ];
                break;
        }
    }
}
