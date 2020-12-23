<?php


namespace App\GamePlatforms;

use App\GamePlatforms\Tools\S128Tool;
use App\Models\GamePlatformUser;
use App\Repositories\GamePlatformTransferDetailRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;

class S128Platform extends BaseGamePlatform
{

    /**
     * @var S128Tool
     */
    protected $tool;

    protected $prefixes = ['vnd_', 'thb_'];

    public $url = [
        'login'             => '/get_session_id.aspx',
        'launcher'          => '/api/auth_login.aspx?',
        'mobile_launcher'   => '/api/cash/auth?',
        'balance'           => '/get_balance.aspx',
        'check_user_exists' => '/get_balance.aspx',
        'deposit'           => '/deposit.aspx',
        'withdraw'          => '/withdraw.aspx',
        'check'             => '/check_transfer.aspx',
        'pull'              => '/get_cockfight_processed_ticket_2.aspx',
    ];

    # 注册
    public function register(GamePlatformUser $platformUser)
    {
        $platformUser->updatePlatformUserId('');
        return $platformUser;
    }

    # 登陆
    public function getLoginRequest(GamePlatformUser $platformUser)
    {
        $prefix = $this->getPrefix($platformUser);
        return $this->setRequest($prefix, $platformUser, 'login');
    }

    public function analysisLoginResponse($response, $platformUser)
    {
        $body     = $this->tool->checkResponse($response, 'login');
        $device   = $this->data['device'];
        if (UserRepository::isPc($device)) {
            $launcherUrl    = $this->platform->launcher_request_url . $this->getUrl('launcher');
        } else {
            $launcherUrl    = $this->account['mobile_launcher_request_url'] . $this->getUrl('mobile_launcher');
        }
        $data['session_id'] = $body['session_id'];
        $data['lang']       = $this->tool->getLanguage($this->user->language);
        $data['login_id']   = $platformUser->name;
        $launcherUrl        .= http_build_query($data);
        $this->tool->responseLog('login', $body['status_code'], $launcherUrl);
        return $launcherUrl;
    }

    # 查余额
    public function getBalanceRequest(GamePlatformUser $platformUser)
    {
        $prefix = $this->getPrefix($platformUser);
        return $this->setRequest($prefix, $platformUser, 'balance');
    }

    public function analysisBalanceResponse($response)
    {
        return $this->tool->checkResponse($response, 'balance');
    }

    # 转账，继承父类，复写
    public function getTransferRequest(GamePlatformUser $platformUser)
    {
        $detail = $this->data['detail'];
        $prefix = $this->getPrefix($platformUser);
        if ($detail->isIncome()) {
            return $this->setRequest($prefix, $platformUser, 'deposit');
        }
        return $this->setRequest($prefix, $platformUser, 'withdraw');
    }

    public function transfer()
    {
        $isRemoteRegister = $this->isRemoteRegister();
        # 获取账户余额

        $userExists = $this->checkUserExists();
        if ($userExists) {
            $platformUser = $this->balance($isRemoteRegister);
        } else {
            $platformUser = $this->getPlatformUser($isRemoteRegister);
        }

        $detail = $this->data['detail'];

        # 更新转账前余额
        if ($detail->isIncome()) {
            GamePlatformTransferDetailRepository::setToBeforeBalance($detail, $platformUser->balance);
        } else {
            GamePlatformTransferDetailRepository::setFromBeforeBalance($detail, $platformUser->balance);
            if ($detail->amount > $platformUser->balance) {
                error_response(415, 'balance not enough', 415);
            }
        }

        $request = $this->getTransferRequest($platformUser);

        $response = $this->call($request);

        $detail = $this->analysisTransferResponse($response);

        # 查看状态是否需要检查订单状态
        if ($detail->isNeedCheck()) {
            # 这步中需要将最新的交易明细放入data[detail]中，为检查订单做准备
            $this->data['detail'] = $detail;
            $detail               = $this->check();
        }

        return $detail;
    }

    public function analysisTransferResponse($response)
    {
        $detail = $this->data['detail'];
        if ($detail->isIncome()) {
            return $this->tool->checkResponse($response, 'deposit', ['detail' => $detail]);
        }
        return $this->tool->checkResponse($response, 'withdraw', ['detail' => $detail]);
    }


    # 确认转账
    public function getCheckRequest(GamePlatformUser $platformUser)
    {
        $prefix = $this->getPrefix($platformUser);
        return $this->setRequest($prefix, $platformUser, 'check');
    }

    public function analysisCheckResponse($response)
    {
        return $this->tool->checkResponse($response, 'check', ['detail' => $this->data['detail']]);
    }

    # 拉取报表
    public function pull()
    {
        $data = [];
        foreach ($this->prefixes as $prefix) {
            try {
                $result = $this->singlePull($prefix);
            } catch (\Exception $e) {
                Log::info('S128 拉取' . $prefix . '数据失败，失败原因：' . $e->getMessage());
                continue;
            }
            $data[] = $result;
        }
        return $this->tool->insertBetDetails($data);
    }

    public function singlePull($prefix)
    {
        $request = $this->setRequest($prefix, null, 'pull');
        $response = $this->call($request);
        return $this->tool->checkResponse($response, 'pull', ['schedule' => $this->data]);
    }
    # 获取游戏列表 N/A

    # help function
    private function setRequest($prefix, GamePlatformUser $platformUser = null, $methodName = 'login')
    {
        $request = [
            'url'       => $this->platform->request_url . $this->getUrl($methodName),
            'data'      => [
                'api_key'    => $this->account[$prefix . 'api_key'],
                'agent_code' => $this->account[$prefix . 'agent_code'],
            ],
            'headers'   => [],
            'method'    => 'post',
            'data_type' => 'form_params',
            'timeout'   => 3000,
        ];

        if (isset($this->data['detail'])) {
            $detail                    = $this->data['detail'];
            $request['data']['amount'] = $detail->amount;
            $request['data']['ref_no'] = $this->tool->getTransferOrderNo($detail->order_no);
        }
        if ($platformUser) {
            $request['data']['login_id'] = $platformUser->name;
            $request['data']['name']     = $platformUser->name;
            $request['data']['odds']     = $this->tool->getOddsType($this->user->odds);
        }
        if ($methodName == 'pull') {
            $schedule                          = $this->data['schedule'];
            $request['data']['start_datetime'] = $schedule->start_at->toDateTimeString();
            $request['data']['end_datetime']   = $schedule->end_at->toDateTimeString();
            $request['data']                   = collect($request['data'])->only(['api_key', 'agent_code', 'start_datetime', 'end_datetime'])->toArray();
        }

        $this->tool->requestLog($methodName, $request);
        return $request;
    }


    /**
     * 是否需要先远程注册
     * @return bool
     */
    protected function isRemoteRegister()
    {
        return false;
    }

    /**
     * @return \App\Models\GamePlatformTransferDetail|bool|mixed|\SimpleXMLElement|string
     */
    protected function checkUserExists()
    {
        $isRemoteRegister = $this->isRemoteRegister();
        $platformUser     = $this->getPlatformUser($isRemoteRegister);
        $prefix = $this->getPrefix($platformUser);
        $request          = $this->setRequest($prefix, $platformUser, 'check_user_exists');
        $result           = $this->call($request);

        return $this->analysisCheckUserExistsResponse($result);
    }

    /**
     * @param $response
     * @return \App\Models\GamePlatformTransferDetail|bool|mixed|\SimpleXMLElement|string
     */
    protected function analysisCheckUserExistsResponse($response)
    {
        return $this->tool->checkResponse($response, 'check_user_exists');
    }
}


