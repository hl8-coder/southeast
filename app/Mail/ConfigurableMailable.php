<?php


namespace App\Mail;

use Illuminate\Container\Container;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Config;
use Swift_Mailer;
use Swift_SmtpTransport;

class ConfigurableMailable extends Mailable
{
    public function send(Mailer $mailer)
    {
        $host       = Config::get('mail.host');
        $port       = Config::get('mail.port');
        $security   = Config::get('mail.encryption');
        $username   = Config::get('mail.username');
        $password   = Config::get('mail.password');
        $stream     = Config::get('mail.stream');
        $this->from = [Config::get('mail.from')];
        $transport  = new Swift_SmtpTransport($host, $port, $security);
        $transport->setUsername($username);
        $transport->setPassword($password);
        $transport->setStreamOptions($stream);
        $mailer->setSwiftMailer(new Swift_Mailer($transport));
        Container::getInstance()->call([$this, 'build']);
        $mailer->send($this->buildView(), $this->buildViewData(), function ($message) {
            $this->buildFrom($message)
                ->buildRecipients($message)
                ->buildSubject($message)
                ->runCallbacks($message)
                ->buildAttachments($message);
        });
    }
}