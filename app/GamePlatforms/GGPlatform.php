<?php


namespace App\GamePlatforms;

use App\GamePlatforms\Tools\GGTool;
use App\Models\GamePlatformUser;

class GGPlatform extends BaseGamePlatform
{

    /**
     * @var GGTool
     */
    protected $tool;

    private $methodCode = [
        'login'        => 'fw',
        'register'     => 'ca',
        'balance'      => 'gb',
        'transfer'     => 'tc',
        'check'        => 'qx',

        // 'game_url'     => 'fw',
        'change_pwd'   => 'up',
        'reset_pwd'    => 'rp',
        'kick_out'     => 'ty',
        'pull'         => 'br3',

    ];

    # 注册
    public function getRegisterRequest(GamePlatformUser $platformUser)
    {
        return $this->setRequest($platformUser, 'register');
    }

    public function analysisRegisterResponse($response)
    {
        return $this->tool->checkResponse($response, 'register', $this->data);
    }

    # 登陆
    public function getLoginRequest(GamePlatformUser $platformUser)
    {
        return $this->setRequest($platformUser, 'login');
    }

    public function analysisLoginResponse($response, $platformUser)
    {
        return $this->tool->checkResponse($response, 'login');
    }

    # 查余额
    public function getBalanceRequest(GamePlatformUser $platformUser)
    {
        return $this->setRequest($platformUser, 'balance');
    }

    public function analysisBalanceResponse($response)
    {
        return $this->tool->checkResponse($response, 'balance');
    }

    # 转账，继承父类，复写
    public function getTransferRequest(GamePlatformUser $platformUser)
    {
        return $this->setRequest($platformUser, 'transfer');
    }

    public function analysisTransferResponse($response)
    {
        return $this->tool->checkResponse($response, 'transfer', ['detail' => $this->data['detail']]);
    }


    # 确认转账
    public function getCheckRequest(GamePlatformUser $platformUser)
    {
        return $this->setRequest($platformUser, 'check');
    }

    public function analysisCheckResponse($response)
    {
        return $this->tool->checkResponse($response, 'check', ['detail' => $this->data['detail']]);
    }

    # 拉取报表
    public function getPullRequest()
    {
        return $this->setRequest(null, 'pull');
    }

    public function analysisPullResponse($response)
    {
        $originBetDetails = $this->tool->checkResponse($response, 'pull', ['schedule' => $this->data]);

        return $this->tool->insertBetDetails($originBetDetails);
    }
    # 获取游戏列表 N/A

    # 踢出会员 start
    public function getKickOutRequest(GamePlatformUser $platformUser)
    {
        return $this->setRequest($platformUser, 'kick_out');
    }

    public function analysisKickOutResponse($response)
    {
        return $this->tool->checkResponse($response, 'kick_out', $this->data);
    }

    # 踢出会员 end

    # help function
    private function setRequest(GamePlatformUser $platformUser = null, $methodName = 'login')
    {
        $this->request['url'] = $this->platform->request_url;
        $account              = $this->platform->account;

        $request['cagent'] = $account['agent_name'];
        $request['method'] = $this->getMethodCode($methodName);


        if (in_array($methodName, ['login', 'balance', 'transfer', 'check', 'register'])) {
            $request['loginname'] = $platformUser->name;
            $request['password']  = $platformUser->password;
            $request['cur']       = $this->getCurrency($platformUser->currency); // 确认哪些方法需要币别 注册，查余额，转账

            if ('register' == $methodName) {
                $try               = isset($this->data['is_try']) ? $this->data['is_try'] : false;
                $request['actype'] = $try ? 0 : 1;
            }

            if ('transfer' == $methodName) {
                $detail            = $this->data['detail'];
                $request['type']   = $detail->isIncome() ? 'IN' : 'OUT';
                $request['credit'] = $detail->amount;
                $request['billno'] = $this->tool->getTransferOrderNo($detail->order_no);
            }
            if (in_array($methodName, ['check', 'transfer'])) {
                $detail            = $this->data['detail'];
                $request['billno'] = $this->tool->getTransferOrderNo($detail->order_no);
                if ('transfer' == $methodName) {
                    $request['type']   = $detail->isIncome() ? 'IN' : 'OUT';
                    $request['credit'] = $detail->amount;
                }
            }

            if ('login' == $methodName) {
                $request['sid']       = $account['agent_name'] . time() . random_int(1000, 9999);
                $request['lang']      = $this->user->language;
                $request['gametype']  = $this->data['code'];
                $request['ip']        = $this->data['ip'];
                $request['returnUrl'] = config('app.url');
                $request['isapp']     = 0; //'1=不全屏，0=全屏(默认)';
                $request['iframe']    = 1; // 此处设置是针对第三方是否使用iframe,如果我方使用iframe,同时第三方也使用iframe会出现跨域问题，所以这里设置第三方不开启iframe
                $request['ishttps']   = app()->isLocal() ? 0 : 1; //'是否要返回htts地址，1返回，默认不用https';
            }

        }

        if ('pull' == $methodName) {
            $this->request['url'] = $this->platform->report_request_url;
            $schedule             = $this->data['schedule'];
            $request['startdate'] = convert_time($schedule->start_at);
            $request['enddate']   = convert_time($schedule->end_at);
        }

        if ('kick_out' == $methodName) {
            $request['loginname'] = $platformUser->name;
            $request['password']  = $platformUser->password;
        }

        $this->request['data']['params']     = $this->encrypt($request, $account['des_key']);
        $this->request['data']['key']        = $this->sign($this->request['data']['params'], $account['md5_key']);
        $this->request['headers']['GGaming'] = 'WEB_GG_GI_' . $account['agent_name'];
        $this->request['method']             = 'get';
        $this->request['data_type']          = 'query';
        $this->tool->requestLog($methodName, ['request' => $this->request, 'params_source' => $request]);
        return $this->request;
    }

    private function sign($params, $md5Key)
    {
        return md5($params . $md5Key);
    }

    private function getMethodCode($methodName)
    {
        $methodCodes = $this->methodCode;
        return isset($methodCodes[$methodName]) ? $methodCodes[$methodName] : 'login';
    }

    private function encrypt($request, $descKey)
    {
        $array = [];
        foreach ($request as $key => $value) {
            $array[] = $key . '=' . $value;
        }
        $str = implode('/\\\\/', $array);
        return \openssl_encrypt($str, 'des-ecb', $descKey);
    }

    private function getCurrency($localCurrency)
    {
        $currencies = $this->tool->currency;
        return isset($currencies[$localCurrency]) ? $currencies[$localCurrency] : '';
    }

}


