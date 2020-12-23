<?php

namespace App\Notifications;

use App\Channels\DatabaseChannel;
use App\Models\Admin;
use App\Models\NotificationMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NotificationMsg extends Notification
{
    use Queueable;

    protected $message;

    protected $admin;

    protected $notificationMessage;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message, Admin $admin, NotificationMessage $notificationMessage)
    {
        $this->message             = $message;
        $this->admin               = $admin;
        $this->notificationMessage = $notificationMessage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [DatabaseChannel::class];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
        ];
    }

    public function getAdminName()
    {
        return is_null($this->admin) ? null : $this->admin->name;
    }

    public function getNotificationMessageId()
    {
        return is_null($this->notificationMessage) ? null : $this->notificationMessage->id;
    }

    public function notificationMessageNum()
    {
        $this->notificationMessage->increment('successNum');
    }
}
