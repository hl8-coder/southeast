<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;

class DatabaseChannel extends \Illuminate\Notifications\Channels\DatabaseChannel
{
    /**
     * Build an array payload for the DatabaseNotification Model.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return array
     */
    protected function buildPayload($notifiable, Notification $notification)
    {
        $class = get_class($notification);
        switch ($class)
        {
            case 'App\Notifications\NotificationMsg':
                $notification->notificationMessageNum();
                return [
                    'identifier'              => $notification->id,
                    'type'                    => get_class($notification),
                    'data'                    => $this->getData($notifiable, $notification),
                    'admin_name'              => $notification->getAdminName(),
                    'notification_message_id' => $notification->getNotificationMessageId(),
                    'read_at'                 => null,
                ];
                break;
            default:
                return [
                    'identifier'              => $notification->id,
                    'type'                    => get_class($notification),
                    'data'                    => $this->getData($notifiable, $notification),
                    'admin_name'              => $notification->getAdminName(),
                    'read_at'                 => null,
                ];
                break;
        }
    }
}