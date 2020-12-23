<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\DatabaseNotification;
use App\Models\User;

class NotificationRequest extends Request
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
                    'name'     => 'nullable|string',
                    'names'    => 'nullable|array',
                    'category' => 'required|integer|in:' . get_validate_in_string(DatabaseNotification::$categories),
                    'message'  => 'required|string',
                    'start_at' => 'nullable|date',
                    'end_at'   => 'nullable|date',
                    'status'   => 'required|int|in:' . implode(',', array_keys(User::$statuses)),
                ];
                break;

            case 'reply':
                return [
                    'message' => 'required|string',
                ];
                break;
        }
        return [];
    }
}
