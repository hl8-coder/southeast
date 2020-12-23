<?php


namespace App\Services;


use App\Mail\CreatedEmail;
use App\Models\MailboxTemplate;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailService
{
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
     * 是否是代理
     * @var boolean
     */
    public $isAffiliate;

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
     * 发件邮箱
     * @var string
     */
    private $username;

    /**
     * 邮件标题
     * @var string
     */
    private $title;

    /**
     * 邮件内容
     * @var string
     */
    private $body;

    /**
     * 邮件附件
     * @var string
     */
    private $file;

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
     * 发件密码
     * @var string
     */
    private $password;

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

        $this->getTitleAndBody();
        $this->setUsernameAndPassword($currency);
    }

    public function sendEmail()
    {
        try {
            Mail::to(['address' => $this->email])->send(new CreatedEmail($this->title, $this->body, $this->file));
        } catch (\Exception $e) {
            Log::stack(['send_email'])->info('system error, message: ' . $e->getMessage() . ', trace message: ' . $e->getTraceAsString());
        }
    }

    public function setUsernameAndPassword($currency)
    {
        $emailConfig = config('multiple_mail')[$currency];
        if ($this->isAffiliate) {
            $emailConfig = $emailConfig['Affiliate'];
        } else {
            $emailConfig = $emailConfig['Member'];
            switch ($this->type) {
                case 1:
                case 4:
                case 5:
                    $emailConfig = $emailConfig['welcome'];
                    break;
                case 2:
                case 3:
                    $emailConfig = $emailConfig['verify'];
                    break;
                default:
                    $emailConfig = $emailConfig['welcome'];
                    break;
            }
        }

        $this->username = $emailConfig['username'];
        $this->password = $emailConfig['password'];

        Config::set('mail.from', array('address' => $this->username, 'name' => $this->username));
        Config::set('mail.username', $this->username);
        Config::set('mail.password', $this->password);
    }

    public function getTitleAndBody()
    {
        $tem       = MailboxTemplate::where([
            [
                'type', $this->type
            ],
            [
                'is_affiliate', $this->isAffiliate
            ]
        ])->first();
        $languages = $tem->languages;
        $title     = '';
        $body      = '';
        foreach ($languages as $value) {
            if ($value['language'] == $this->language) {
                $title         = $value['title'];
                $this->subject = $title;
                $body          = $value['body'];
            }
        }
        if (strstr($body, '{$code}') != false) {
            $body = str_replace('{$code}', $this->text, $body);
        }
        if (strstr($body, '{$name}') != false) {
            $body = str_replace('{$name}', $this->name, $body);
        }
        if (strstr($body, '{$affCode}') != false) {
            $body = str_replace('{$affCode}', $this->affCode, $body);
        }
        if (strstr($body, '{$fullName}') != false) {
            $body = str_replace('{$fullName}', $this->fullName, $body);
        }

        $this->title = $title;
        $this->body  = $body;
    }
}