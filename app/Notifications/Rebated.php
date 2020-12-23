<?php

namespace App\Notifications;

use App\Channels\DatabaseChannel;
use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class Rebated extends Notification
{
    use Queueable;

    protected $amount;
    protected $admin;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($amount, Admin $admin=null)
    {
        $this->amount = $amount;
        $this->admin  = $admin;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
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
            'amount' => $this->amount,
        ];
    }

    public function getAdminName()
    {
        return is_null($this->admin) ? null : $this->admin->name;
    }
}
