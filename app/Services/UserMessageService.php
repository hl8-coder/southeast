<?php

namespace App\Services;

use App\Models\UserMessage;
use App\Models\UserMessageDetail;

class UserMessageService
{

    /**
     * 将数据存入UserMessage
     * @param $sendAdmin
     * @param $data
     * @return UserMessage
     */
    public function userMessageStore($sendAdmin, $data)
    {
        $userMessageData = [
            'category'          => $data['category'],
            'content'           => $data['content'],
            'sent_admin_id'     => $sendAdmin->id,
            'sent_admin_name'   => $sendAdmin->name,
            'provider_code'     => config('sms.provider_code'),
            'use_type'          => UserMessage::USE_TYPE_FOR_AD,
        ];

        $userMessage = new UserMessage($userMessageData);

        $userMessage->save();

        return $userMessage;
    }

    public function userMessageDetailsStore($receiveUser, $phone, $userMessage)
    {
        $saveData = [
            'user_message_id'     => $userMessage->id,
            'receive_user_name'   => $receiveUser->name,
            'receive_user_id'     => $receiveUser->id,
            'receive_user_status' => $receiveUser->status,
            'phone'               => $phone,
            'currency'            => $receiveUser->currency,
            'use_type'            => UserMessage::USE_TYPE_FOR_AD,
            'status'              => UserMessageDetail::STATUS_DELIVERED,
        ];

        $userMessage = new UserMessageDetail($saveData);
        $userMessage->save();

        return $userMessage;
    }

    public function process(UserMessageDetail $userMessageDetail)
    {
        $smsService = new SMSService();
        $response   = $smsService->sms($userMessageDetail->phone, $userMessageDetail->userMessage->content);
        if (isset($response['ResultCode']) && $response['ResultCode'] == '0') {
            //发送成功
            $userMessageDetail->setToSent();
        } else {
            $reason = isset($response['ResultDesc']) ? $response['ResultDesc'] : 'api access timeout';
            $userMessageDetail->setToFailed($reason);
        }
    }
}