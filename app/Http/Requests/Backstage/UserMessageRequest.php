<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\User;
use App\Models\UserMessage;

class UserMessageRequest extends Request
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
                    'user_name'     => 'nullable|string',
                    'excel'         => 'nullable|file|mimes:xlsx',
                    'category'      => 'required|int|in:' . implode(',', array_keys(UserMessage::$categories)),
                    'content'       => 'required|string|max:500',
                    'member_status' => 'required|int|in:' . implode(',', array_keys(User::$statuses)),
                ];
                break;
        }
    }
}
