<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\Adjustment;

class BatchAdjustmentRequest extends Request
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
                    'type'       => 'required|integer|in:' . implode(',', array_keys(Adjustment::$types)),
                    'file'       => 'required|string',
                    'unique_key' => 'required|string|unique:batch_adjustments',
                ];
                break;
        }
    }
}
