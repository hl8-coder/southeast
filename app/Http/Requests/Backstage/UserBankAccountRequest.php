<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\UserBankAccount;

class UserBankAccountRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'PATCH':
                return [
                    'status' => 'required|integer|in:' . implode(',', array_keys(UserBankAccount::$statuses)),
                ];
                break;
        }
    }
}
