<?php

namespace App\Mail;

use App\Models\MailboxTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreatedEmail extends ConfigurableMailable
{
    use Queueable, SerializesModels;

    /**
     * 邮件标题
     * @var string
     */
    public $title;

    /**
     * 邮件内容
     * @var string
     */
    public $body;

    /**
     * 邮件附件
     * @var string
     */
    public $file;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($title, $body, $file = '')
    {
        $this->title = $title;
        $this->body  = $body;
        $this->file  = $file;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->file) {
            return $this->view('emails.email')
                ->with([
                    'body'  => $this->body,
                    'title' => $this->title
                ])
                ->attach($this->file)
                ->subject($this->title);
        }
        return $this->view('emails.email')
            ->with([
                'body'  => $this->body,
                'title' => $this->title
            ])
            ->subject($this->title);
    }
}
