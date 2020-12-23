<?php

namespace App\Jobs;

use App\Models\MailboxTemplate;
use Illuminate\Bus\Queueable;
use App\Services\SendEmailService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 邮箱类型 MailboxTemplate::$types
     * @var int
     */
    public $type;

    /**
     * 收件人地址
     * @var string
     */
    public $email;

    /**
     * 币别 用于区分发件邮箱账号
     * @var string
     */
    public $currency;

    /**
     * 语言 选择不同邮件模板
     * @var string
     */
    public $language;

    /**
     * 邮件内容
     * @var string
     */
    public $text;

    /**
     * 用户名
     * @var string
     */
    public $name;

    /**
     * 是否是代理
     * @var boolean
     */
    public $isAffiliate;

    /**
     * 邮件附件
     * @var string
     */
    public $file;

    /**
     * 全名
     * @var string
     */
    private $fullName;

    /**
     * 代理号
     * @var string
     */
    private $affCode;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($type, $email, $currency, $isAffiliate, $language, $text = '', $name = '', $file = '', $fullName = '', $affCode = '')
    {
        $this->type        = $type;
        $this->email       = $email;
        $this->currency    = $currency;
        $this->language    = $language;
        $this->text        = $text;
        $this->name        = $name;
        $this->file        = $file;
        $this->fullName    = $fullName;
        $this->affCode     = $affCode;
        $this->isAffiliate = $isAffiliate;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $service = new SendEmailService($this->type, $this->email, $this->currency, $this->isAffiliate, $this->language, $this->text, $this->name, $this->file, $this->fullName, $this->affCode);

        $service->sendEmail();
    }
}
