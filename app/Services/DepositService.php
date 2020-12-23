<?php

namespace App\Services;

use App\Models\Language;
use Carbon\Carbon;
use App\Models\User;
use GuzzleHttp\Client;
use App\Models\Deposit;
use App\Models\UserBankAccount;
use App\Models\PaymentPlatform;
use App\Models\CompanyBankAccount;
use App\Models\TurnoverRequirement;
use Illuminate\Support\Facades\Log;
use App\Repositories\ImageRepository;

class DepositService
{
    protected $platform;
    protected $platformClass;
    protected $platformType;
    protected $user;
    protected $data;
    protected $relationBank;
    # 公司银行卡
    protected $companyBankAccount;
    # 会员银行卡
    protected $userBankAccount;

    protected $result = [
        'request_type'              => '',
        'request_url'               => '',
        'request_data'              => '',
        'request_qrcode_url'        => '',
        'request_qrcode_img_url'    => '',
        'request_qrcode_img_base64' => '',
        'request_error'             => '',
        'deposit'                   => '',
    ];

    public function __construct($platformId)
    {
        $this->platform = PaymentPlatform::find($platformId);
        $codes          = explode('-', $this->platform->code);
        $class          = 'App\\Payments\\' . ucwords(strtolower($codes[0]));
        if (class_exists($class)) {
            $this->platformClass = new $class;
            $this->platformType  = $codes[1];
        }

    }

    /**
     * 发起充值
     *
     * @return result
     */
    public function deposit(User $user, $data)
    {
        if ($this->platform->is_need_type_amount && (float)$data["amount"] <= 0) {
            error_response(422, __('deposit.AMOUNT_INVALID'));
        }
        # 确认币别是否支持
        if (strrpos(strtolower($this->platform->currencies), strtolower($user->currency)) === false) {
            error_response(422, __('deposit.CURRENCY_INVALID'));
        }

        $this->user = $user;
        $this->data = $data;
        switch ($this->platform->payment_type) {
            # 银行卡
            case PaymentPlatform::PAYMENT_TYPE_BANKCARD:
                $result = $this->depositByCompanyBandCard();
                break;
            # Mpay
            case PaymentPlatform::PAYMENT_TYPE_MPAY:
                $result = $this->depositByMpay();
                break;
            # LinePay
            case PaymentPlatform::PAYMENT_TYPE_LINEPAY:
                $result = $this->depositByLinePay();
                break;

            # 第三方
            default:

                # 这里修改返回页面地址
                if (isset($this->data['redirect'])) {
                    $localLanguage = app()->getLocale();
                    $languageFront = Language::$frontLanguageMap[$localLanguage];
                    $this->data['redirect'] = $this->data['redirect'] . '/' . $languageFront . '/user/history';
                }

                $result = $this->depositOther();
                break;
        }

        return $result;
    }

    /**
     * 充值 - 公司银行卡
     *
     * @return void
     */
    public function depositByCompanyBandCard()
    {
        # 检查公司银行卡
        $companyBankAccountId = $this->data['company_bank_account_id'];
        if (empty($companyBankAccountId)) {
            error_response(422, __('deposit.COMPANY_BANK_CARD_INVALID'));
        }
        $this->companyBankAccount = CompanyBankAccount::find($companyBankAccountId);
        if (!$this->companyBankAccount->isEnable()) {
            error_response(422, __('deposit.COMPANY_BANK_CARD_INVALID'));
        }

        if (!empty($this->data['user_bank_account_id'])) {
            # 检查会员银行卡
            $userBankAccountId = $this->data['user_bank_account_id'];
            if (empty($userBankAccountId)) {
                error_response(422, __('deposit.MEMBER_BANK_CARD_INVALID'));
            }
            $this->userBankAccount = UserBankAccount::where("user_id", $this->user->id)->find($userBankAccountId);
            if (!$this->userBankAccount->isActive()) {
                error_response(422, __('deposit.MEMBER_BANK_CARD_INVALID'));
            }
        }

        $this->result['deposit'] = $this->getDeposit();

        return $this->result;
    }

    /**
     * 充值 - Mpay
     *
     * @return void
     */
    public function depositByMpay()
    {
        $this->result['deposit'] = $this->getDeposit();

        return $this->result;
    }

    /**
     * 充值 - 第三方
     *
     * @return void
     */
    public function depositOther()
    {
        # 获取充值数据
        $deposit = $this->getDeposit();

        # 获取请求数据
        $requestData = $this->getRequest($deposit);
        Log::stack(['deposit_log'])->info('request data:' . json_encode($requestData));
        # 请求api地址
        $url = $this->platform->request_url;

        // if ($this->platform->relay_load_url) {
        //     $httpType = strpos($url, 'https') === false ? "http" : "https" ;
        //     $url = str_replace('http://', '', $url);
        //     $url = str_replace('https://', '', $url);
        //     $url = base64_encode($url);
        //     $url = $this->platform->relay_load_url . '/' . $httpType . "/". $url;
        // }

        $sResuestType = $this->platformClass->aReuqstType[$this->platformType]["request_type"];

        $this->result['request_type'] = $sResuestType;
        $this->result['request_url']  = $url;
        $this->result['request_data'] = $requestData;
        $this->result['deposit']      = $deposit;

        if (in_array($sResuestType, [PaymentPlatform::REQUEST_TYPE_FROM, PaymentPlatform::REQUEST_TYPE_GET])) {
            $returnData = $this->result;
            $this->platformClass->modifyReturnDataForFront($requestData, $returnData);
            return $returnData;
        } else {
            return $this->doRequest($this->result, $sResuestType);
        }
    }

    /**
     * 充值 - Mpay
     *
     * @return void
     */
    public function depositByLinePay()
    {
        if (!(isset($this->data['linepay_id'])) ||  empty($this->data['linepay_id'])) {
            error_response(422, __('deposit.LINEPAY_ID_REQUIRED'));
        }

        if (!(isset($this->data['deposit_date'])) ||  empty($this->data['deposit_date'])) {
            error_response(422, __('deposit.LINEPAY_DEPOSIT_DATE_REQUIRED'));
        }


        $this->result['deposit'] = $this->getDeposit();

        return $this->result;
    }

    /**
     * 建立充值资料
     *
     * @return void
     */
    protected function store()
    {
        $deposit = new Deposit([
            'user_id'             => $this->user->id,
            'user_ip'             => $this->data['user_ip'],
            'currency'            => $this->user->currency,
            'language'            => $this->user->language,
            'payment_platform_id' => $this->platform->id,
            'amount'              => $this->data['amount'],
        ]);

        // 此系统手续费用人工设定，所以乎略判断
        if (false) {
            # 计算手续费

            # 手续费
            $fee = 0;
            # 报销费(公司承担手续用)
            $reimbursement_fee = 0;

            # 判断手续费要由会员承担或公司承坦
            if ($this->platform->is_fee) {
                $fee = round($this->data['amount'] * $this->platform->fee_rebate, 4);
                # 计算是否超过最大手续费
                $fee = $this->platform->max_fee && $fee > $this->platform->max_fee ? $this->platform->max_fee : $fee;
            } else {
                $reimbursement_fee = round($this->data['amount'] * $this->platform->fee_rebate, 4);
            }

            $deposit->bank_fee          = $fee;
            $deposit->reimbursement_fee = $reimbursement_fee;
            $deposit->arrival_amount    = $this->data['amount'] - $fee;
        } else {
            # 实际收款金额
            $deposit->arrival_amount = $this->data['amount'];
        }

        # 流水关闭要求，目前固定为1倍流水
        $deposit->turnover_closed_value = $deposit->arrival_amount;
        $deposit->is_turnover_closed    = false;

        # 支付类型
        $deposit->payment_type = $this->data['payment_type'];
        $deposit->deposit_at   = Carbon::now();

        # 依不同支付类型写入相关栏位
        switch ($this->data["payment_type"]) {
            # 银行卡充值
            case PaymentPlatform::PAYMENT_TYPE_BANKCARD:
                $deposit->fund_in_account           = $this->companyBankAccount->code;
                $deposit->company_bank_account_id   = $this->companyBankAccount->id;
                $deposit->company_bank_code         = $this->companyBankAccount->code;
                $deposit->company_bank_branch       = $this->companyBankAccount->branch;
                $deposit->company_bank_account_name = $this->companyBankAccount->account_name;
                $deposit->company_bank_account_no   = $this->companyBankAccount->account_no;
                $deposit->online_banking_channel    = $this->data["online_banking_channel"];
                if (isset($this->data["receipts"])) {
                    $deposit->receipts = $this->data["receipts"];
                }
                $deposit->deposit_date = $this->data["deposit_date"];
                $deposit->deposit_at   = new Carbon($deposit->deposit_date);

                if (!empty($this->data['reference_id'])) {
                    $deposit->reference_id = $this->data["reference_id"];
                }

                switch ($this->data["online_banking_channel"]) {
                    # Rule 1: ATM/Internet Banking/Mobile Banking
                    case PaymentPlatform::ONLINE_BANKING_CHANNEL_ATM:
                    case PaymentPlatform::ONLINE_BANKING_CHANNEL_INTERNET_BANKING:
                    case PaymentPlatform::ONLINE_BANKING_CHANNEL_MOBILE_BANKING:
                        $deposit->user_bank_account_id   = $this->userBankAccount->id;
                        $deposit->user_bank_id           = $this->userBankAccount->bank_id;
                        $deposit->user_bank_account_name = $this->userBankAccount->account_name;
                        $deposit->user_bank_account_no   = $this->userBankAccount->account_no;
                        break;
                    # Rule 2: Over the Counter/Cash Deposit/Maunal
                    case PaymentPlatform::ONLINE_BANKING_CHANNEL_OVER_THE_COUNTER:
                    case PaymentPlatform::ONLINE_BANKING_CHANNEL_CASH_DEPOSIT:
                        $deposit->user_bank_id           = $this->data['user_bank_id'];
                        $deposit->user_bank_account_name = $this->data['user_bank_account_name'];
                        break;
                }

                break;

            # Quickpay
            case PaymentPlatform::PAYMENT_TYPE_QUICKPAY:
                $deposit->payment_bank_code = $this->data['bank_code'];
                break;

            # Mpay
            case PaymentPlatform::PAYMENT_TYPE_MPAY:
                $deposit->user_mpay_number  = $this->data['user_mpay_number'];
                $deposit->mpay_trading_code = $this->data['mpay_trading_code'];
                $deposit->fund_in_account   = $this->platform->related_no;
                break;

            case PaymentPlatform::PAYMENT_TYPE_SCRATCH_CARD:
                $deposit->card_type     = $this->data['card_type'];
                $deposit->pin_number    = $this->data['pin_number'];
                $deposit->serial_number = $this->data['serial_number'];
                break;

            case PaymentPlatform::PAYMENT_TYPE_LINEPAY:
                if (isset($this->data["receipts"])) {
                    $deposit->receipts = $this->data["receipts"];
                }
                $deposit->linepay_id      = $this->data["linepay_id"];
                $deposit->deposit_date    = $this->data["deposit_date"];
                $deposit->deposit_at      = new Carbon($deposit->deposit_date);
                $deposit->fund_in_account = $this->platform->related_no;

                break;

            default:
                # code...
                break;
        }

        $deposit->save();

        # 关联图片
        if (isset($this->data["receipts"])) {
            $receipts_array = explode(',', $this->data["receipts"]);
            ImageRepository::updatePatch($this->user, $receipts_array, $deposit);
        }

        return $deposit;
    }


    /**
     * 取得充值相关资讯
     *
     * @return void
     */
    protected function getDeposit()
    {
        $deposit = $this->store();

        $configs = $this->platform->configs;

        # 格式化时间
        if (!empty($configs['created_at_format'])) {
            $createdAtFormat             = str_replace('_', '', $configs['created_at_format']);
            $deposit->deposit_created_at = $deposit->created_at->format($createdAtFormat);
        } else {
            $deposit->deposit_created_at = $deposit->created_at->toDateTimeString();
        }

        # 回调地址
        $deposit->callback_url = config('app.api_callback_url') . "/api/backstage/deposits/call_back/" . $this->platform->code;

        # 判断是否有中转地址
        if ($this->platform->relay_load_url) {
            $url                   = $deposit->callback_url;
            $httpType              = strpos($url, 'https') === false ? "http" : "https";
            $url                   = str_replace('http://', '', $url);
            $url                   = str_replace('https://', '', $url);
            $url                   = $url . "/deposit/callback/" . $this->platform->code;
            $url                   = base64_encode($url);
            $deposit->callback_url = $this->platform->relay_load_url . "/curl/" . $httpType . '/' . $url;
        }

        return $deposit;
    }

    /**
     * 取得请求资料
     *
     * @return void
     */
    protected function getRequest(Deposit $deposit)
    {
        # 初始资料
        $requestData = $this->platformClass->aRequestField[$this->platformType];

        # 带入前端字段参数到请求资料
        if ($mappingFields = $this->platformClass->aMappingField[$this->platformType]) {
            # 替换参数
            foreach ($mappingFields as $key => $field) {

                # 请求参数
                if (isset($requestData[$field]) && isset($this->platform->$key)) {
                    $requestData[$field] = $this->platform->$key;
                }

                # 请求参数
                if (isset($requestData[$field]) && isset($this->data[$key])) {
                    $requestData[$field] = $this->data[$key];
                }

                # 充值参数
                if (isset($requestData[$field]) && isset($deposit[$key])) {
                    $requestData[$field] = $deposit[$key];
                }
            }
        }

        # 签名前调整
        $this->platformClass->modifyParamBeforeSign($deposit, $requestData, $this->platformType);
        # 取得请求签署字串并带入订单资讯到请求资料
        $this->platformClass->encryptSign($this->platformType, $deposit, $requestData, $this->platform->customer_key);

        # 签名后的参数调整
        $this->platformClass->modifyParamAfterSign($requestData, $this->platformType);

        return $requestData;
    }

    protected function doRequest($requestData, $sResuestType)
    {

        if (isset($requestData['request_data']['headers'])) {
            $headers = $requestData['request_data']['headers'];
        } else {
            // 定义头部
            $headers = $this->platformClass->getHeader($this->platformType);

            if (empty($headers)) {
                $headers['User-Agent'] = '';
            }
        }

        $url = $this->platform->request_url;

        //   if($this->platform->relay_load_url) {
        //     $url = $this->platform->request_url;
        //     $httpType = strpos($url, 'https') === false ? "http" : "https" ;
        //     $url = str_replace('http://', '', $url);
        //     $url = str_replace('https://', '', $url);
        //     $url = base64_encode($url);
        //     if($this->platform->request_method == PaymentPlatform::REQUEST_METHOD_GET) {
        //         $url = $this->platform->relay_load_url . '/' . $httpType . "/" . $url;
        //     }
        //     else {
        //         $url = $this->platform->relay_load_url . "/curl/" . $httpType . "/" . $url;
        //     }
        // }

        $http = new Client();

        try {
            switch ($this->platformClass->aReuqstType[$this->platformType]["request_method"]) {
                case PaymentPlatform::REQUEST_METHOD_GET:
                    $response = $http->get($url . "?" . http_build_query($requestData["request_data"]));
                    break;
                case PaymentPlatform::REQUEST_METHOD_POST:
                    $response = $http->post($url, [
                        'headers'     => $headers,
                        'form_params' => $requestData['request_data'],
                        'verify'      => false,
                    ]);
                    break;
                case PaymentPlatform::REQUEST_METHOD_JSON_POST:
                    $response = $http->post($url, [
                        'headers' => $headers,
                        'json'    => $requestData['request_data'],
                    ]);
                    break;
                case PaymentPlatform::REQUEST_METHOD_BODY_POST:
                    $response = $http->post($url, [
                        'headers' => $headers,
                        'body'    => $requestData['request_data']['data'],
                    ]);

                    break;
                default:
                    # code...
                    break;
            }

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
        } catch (\Exception $e) {
            $requestData['request_url']   = '';
            $requestData['request_data']  = '';
            $requestData['request_error'] = $e->getMessage();
            return $requestData;
        }

        $body = (string)$response->getBody();
        Log::stack(['deposit_log'])->info($this->platform->code . ' response data:' . $body);
        $result = $this->analyticalBody($body, $requestData, $sResuestType);

        return $result;
    }

    public function analyticalBody($body, $result, $sResuestType)
    {
        $result['request_url']  = '';
        $result['request_data'] = '';

        $url   = '';
        $error = '';

        $this->platformClass->analyticalBody($this->platformType, $body, $result, $url, $error);

        switch ($sResuestType) {
            case PaymentPlatform::REQUEST_TYPE_QRCODE_BASE64:
                $result['request_qrcode_img_base64'] = $url;
                break;
            case PaymentPlatform::REQUEST_TYPE_QRCODE_IMG_URL:
                $result['request_qrcode_img_url'] = $url;
                break;
            case PaymentPlatform::REQUEST_TYPE_QRCODE_URL:
                $result['request_qrcode_url'] = $url;
                break;
            case PaymentPlatform::REQUEST_TYPE_REDIRECT:
                $result['request_url'] = $url;
                break;
            case PaymentPlatform::REQUEST_TYPE_MESSAGE:
                $data    = json_decode($body, true);
                $deposit = Deposit::find($result["deposit"]->id);
                $this->platformClass->updateDepositByCallback($this->platformType, $deposit, $data);
                break;
            default:
                # code...
                break;
        }

        if ($error) {
            $result["request_error"] = $error;
        }

        return $result;
    }

    /**
     * 充值回调
     *
     * @param  $data
     * @return mixed
     * @throws
     */
    public function callBack($data)
    {
        # 解析回调内容
        $signData = $this->platformClass->analyticalCallbackData($this->platformType, $data);

        # 验证签名
        if (!$this->platformClass->checkCallBackSign($this->platformType, $signData, $this->platform->customer_key)) {
            error_response('401', __('deposit.SIGNATURE_ERROR'));
        }


        # 获取回调相关信息
        $result = $this->platformClass->getCallbackDepositResult($this->platformType, $data);

        if ($deposit = Deposit::findByOrderNo($result['order_no'])) {
            # 防止一直被打，同时检测订单是否是处于待更新状态
            if ($deposit->status == Deposit::STATUS_RECHARGE_SUCCESS || $deposit->status == Deposit::STATUS_RECHARGE_FAIL) {
                exit(0);
            } else if ($result['is_success']) {
                # 上分金额 transaction.amount => deposit->arrive_amount
                # deposit.arrived_amount 是上分的参数
                # pg_account_transactions or bank_account_transactions 两个表的数据不参与上分逻辑
                # 上分逻辑关键参数始终在 deposit withdraw 和 adjustment 上

                # 修改 deposit 里面 reimbursement_fee【补贴手续费】 和 bank_fee【渠道手续费】
                $this->platformClass->modifyDepositFeeBeforeCalculationByCallback($this->platformType, $deposit, $data);

                # success：
                # 1、更新订单状态，关闭订单，记录回调与接受上分时间
                # 2、增加 deposit 订单进入统计平台，目前仅对 pg 自动回调支付生效，help2pay 和 paytrust 的手续费会在这里被计算并更新
                # 3、如果是会员第一次充值，则更新会员 第一次充值 时间
                if (!$deposit->success($data)) { # 防止一直被打
                    exit(0);
                } else {
                    # 回调时根据回传的金额更新deposit数据,如真实付款金额
                    # 这里更新的数据，部分已经不能再参与统计
                    # Doicard5s 该函数内部错乱调用了外部逻辑并触发deposit再次更新和统计，涉及支付面比较广，待后续必须时修复 TODO
                    # 同上，部分支付方式，例如充值卡，订单状态修改、统计流水、上分、支付通道统计 等都在该函数内执行，导致该函数功能多样化，不宜改动
                    $this->platformClass->updateDepositByCallback($this->platformType, $deposit, $data, 'callback');

                    # 创建流水要求
                    TurnoverRequirement::add($deposit, $deposit->is_turnover_closed);
                    echo $this->platformClass->noticeVendor($deposit, $data);
                    return $deposit;
                }
            } else {
                $deposit->fail($data, $result['remarks']);
            }
        }

        return null;
    }
}
