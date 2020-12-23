<?php


namespace App\Services;


use App\Models\NotificationMessage;

class NotificationService
{
    public function notificationMessageStore($sendAdmin, $data, $total, $failureNum)
    {
        $saveData = [
            'category'        => $data['category'],
            'message'         => $data['message'],
            'failureNum'      => $failureNum,
            'totalNum'        => $total,
            'sent_admin_id'   => $sendAdmin->id,
            'sent_admin_name' => $sendAdmin->name,
        ];

        $notificationMessage = new NotificationMessage($saveData);

        $notificationMessage->save();

        return $notificationMessage;
    }
}