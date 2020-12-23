<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

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
            case 'destroy':
                return [
                    'notification_ids'   => 'required|array',
                    'notification_ids.*' => 'required|integer|exists:notifications,id',
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

    public function attributes()
    {
        return [
            'notification_ids'   => __('request/api/notification.notification_ids'),
            'notification_ids.*' => __('request/api/notification.notification_ids_children'),
        ];
    }
}
