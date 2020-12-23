<?php

namespace App\GamePlatforms;

use App\GamePlatforms\Tools\SBOTool;
use App\Models\GamePlatformUser;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;

class SBOPlatform extends BaseGamePlatform
{
    /**
     * @var SBOTool
     */
    protected $tool;

    protected $serverId = 'HLV';

    protected $prefixes = ['vnd_', 'thb_'];

    # 注册 start
    public function getRegisterRequest(GamePlatformUser $platformUser)
    {
        $this->request['url'] = $this->platform->request_url . '/web-root/restricted/player/register-player.aspx';
        $data['Username']     = $platformUser->name;
        $prefix               = strtolower($platformUser->currency) . '_';
        $data['Agent']        = $this->account[$prefix . 'agent_name'];
        return $this->setRequest('register', $data);
    }

    public function analysisRegisterResponse($response)
    {
        return $this->tool->checkResponse($response, 'register', $this->data);
    }
    # 注册 end

    # 登录 start
    public function getLoginRequest(GamePlatformUser $platformUser)
    {
        $data         = [];
        $platformUser = $this->getPlatformUser();
        $this->request['url'] = $this->platform->request_url . '/web-root/restricted/player/login.aspx';
        $data['Username']     = $platformUser->name;
        $data['Portfolio']    = 'SportsBook';
        return $this->setRequest('login', $data);
    }

    public function analysisLoginResponse($response, $platformUser)
    {
        $url =  $this->tool->checkResponse($response, 'login', $this->data);

        $param = [
            'lang'      => $this->tool->getPlatformLanguage($platformUser->user->language),
            'oddstyle'  => $this->tool->getPlatformOdd($platformUser->user->odds),
            'theme'     => 'sbo',
            'oddsmode'  => 'double',
            'device'    => UserRepository::isPc($this->data['device']) ? 'd' : 'm' ,
        ];

        $url = 'https:' . $url . '&' . http_build_query($param);
        return $url;
    }
    # 登录 end

    # 查询余额 start
    public function getBalanceRequest(GamePlatformUser $platformUser)
    {
        $this->request['url'] = $this->platform->request_url . '/web-root/restricted/player/get-player-balance.aspx';
        $data['Username']     = $platformUser->name;
        return $this->setRequest('balance', $data);
    }

    public function analysisBalanceResponse($response)
    {
        return $this->tool->checkResponse($response, 'balance', $this->data);
    }
    # 查询余额 end

    # 转账 start
    public function getTransferRequest(GamePlatformUser $platformUser)
    {
        $detail = $this->data['detail'];

        if ($detail->isIncome()) {
            $this->request['url'] = $this->platform->request_url . '/web-root/restricted/player/deposit.aspx';
        } else {
            $this->request['url'] = $this->platform->request_url . '/web-root/restricted/player/withdraw.aspx';
            $data['IsFullAmount'] = false;
        }
        $data['Amount']       = floatval($detail->amount);
        $data['TxnId']        = $detail->order_no;
        $data['Username']     = $platformUser->name;
        return $this->setRequest('transfer', $data);
    }

    public function analysisTransferResponse($response)
    {
        return $this->tool->checkResponse($response, 'transfer', $this->data);
    }
    # 转账 end

    # 检查订单 start
    public function getCheckRequest(GamePlatformUser $platformUser)
    {
        $detail               = $this->data['detail'];
        $this->request['url'] = $this->platform->request_url . '/web-root/restricted/player/check-transaction-status.aspx';
        $data['TxnId']        = $detail->order_no;
        return $this->setRequest('check', $data);
    }

    public function analysisCheckResponse($response)
    {
        return $this->tool->checkResponse($response, 'check', $this->data);
    }
    # 检查订单 end

    # 拉取报表 start

    public function pull()
    {
        $data = [];
        foreach ($this->prefixes as $prefix) {
            try {
                $result = $this->singlePull($prefix);
            } catch (\Exception $e) {
                Log::info('SBO 拉取' . $prefix . '数据失败，失败原因：' . $e->getMessage());
                continue;
            }
            $data[$prefix] = $result;
        }
        return $this->tool->insertBetDetails($data);

    }

    public function singlePull($prefix)
    {
        $this->request['url'] = $this->platform->report_request_url . '/web-root/restricted/report/get-bet-list-by-modify-date.aspx';
        $schedule = $this->data['schedule'];
        $data['Username']     = $this->account[$prefix . 'agent_name'];
        $data['Portfolio']    = 'SportsBook';
        $data['StartDate']    = $this->tool->parseGMTMinus4($schedule->start_at->subMinutes($this->platform->offset));
        $data['EndDate']      = $this->tool->parseGMTMinus4($schedule->end_at);

        $request  = $this->setRequest('pull', $data);
        $response = $this->call($request);
        return $this->tool->checkResponse($response, 'pull', $this->data);
    }

    # 拉取报表 end

    public function registerAgent($agentName, $password, $currency)
    {
        $this->request['url'] = $this->platform->report_request_url . '/web-root/restricted/agent/register-agent.aspx';
        $data['Username']       = $agentName;
        $data['Password']       = $password;
        $data['Currency']       = $currency;
        $data['Min']            = 20;
        $data['Max']            = 10000;
        $data['MaxPerMatch']    = 25000;
        $request  = $this->setRequest('register_agent', $data);
        $response = $this->call($request);
        return $this->tool->checkResponse($response, 'register_agent', $this->data);
    }

    public function analysisPullResponse($response)
    {
        $result = $this->tool->checkResponse($response, 'pull', $this->data);
        return $this->tool->insertBetDetails($result);
    }

    public function setRequest($method, $data)
    {
        $data['CompanyKey']     = $this->account['company_key'];
        $data['ServerId']       = $this->serverId;
        $this->request['data']  = $data;
        $this->request['data_type']  = 'json';
        $this->tool->requestLog($method, $this->request);
        return $this->request;
    }
}
