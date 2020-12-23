<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class FraudForceRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $data = [];
        switch ($this->getRequestMethod()) {
            case 'login':
                $data['blackbox']    = 'required';
                break;
        }
        return $data;
    }
}
