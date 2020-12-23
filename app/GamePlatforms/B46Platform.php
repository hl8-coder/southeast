<?php

namespace App\GamePlatforms;

use App\GamePlatforms\Tools\B46Tool;
use App\Models\GamePlatformUser;
use Illuminate\Support\Facades\Log;

class B46Platform extends BaseGamePlatform
{
    /**
     * @var B46Tool
     */
    protected $tool;

    protected $prefixes = ['vnd_', 'thb_'];

    protected $dateFilters = ['wager_date', 'update_date'];

    # 注册 start
    public function getRegisterRequest(GamePlatformUser $platformUser)
    {
        $this->request['url'] = $this->platform->request_url . '/player/create';
        $data['loginId']      = $this->tool->formatUserName($platformUser->name);
        $this->request['url'] = $this->request['url'] . '?' . http_build_query($data);
        $prefix               = strtolower($platformUser->currency) . '_';
        return $this->setRequest('register', $data, $prefix);
    }

    public function analysisRegisterResponse($response)
    {
        return $this->tool->checkResponse($response, 'register', $this->data);
    }

    # 注册 end

    public function login()
    {
        if ($this->user->isTestUser()) {
            $sports = $this->tool->getSports($this->data['code']);
            if (!empty($sports)) {
                return $this->account['try_url'] . '/' . $this->tool->getPlatformLanguage($this->data['language']) . '/sports/' . $sports;
            } else {
                return $this->account['try_url'] . '/' . $this->tool->getPlatformLanguage($this->data['language']) . '/sports';
            }
        } else {
            return parent::login();
        }
    }

    # 登录 start
    public function getLoginRequest(GamePlatformUser $platformUser)
    {
        $data                 = [];
        $this->request['url'] = $this->platform->request_url . '/player/loginV2';
        $sports               = $this->tool->getSports($this->data['code']);
        if (!empty($sports)) {
            $data['sport'] = $sports;
        }
        $data['loginId']      =  $platformUser->platform_user_id;
        $data['locale']       = $this->tool->getPlatformLanguage($platformUser->user->language);
        $this->request['url'] = $this->request['url'] . '?' . http_build_query($data);
        $prefix               = strtolower($platformUser->currency) . '_';
        return $this->setRequest('login', $data, $prefix);
    }

    public function analysisLoginResponse($response, $platformUser)
    {
        return $this->tool->checkResponse($response, 'login', $this->data);
    }


    # 查询余额 start
    public function getBalanceRequest(GamePlatformUser $platformUser)
    {
        $this->request['url'] = $this->platform->request_url . '/player/info';
        $data['userCode']     = $platformUser->platform_user_id;
        $this->request['url'] = $this->request['url'] . '?' . http_build_query($data);

        $prefix                  = strtolower($platformUser->currency) . '_';
        $this->request['method'] = 'GET';
        return $this->setRequest('balance', $data, $prefix);
    }

    public function analysisBalanceResponse($response)
    {
        return $this->tool->checkResponse($response, 'balance', $this->data);
    }

    # 查询余额 end

    # 转账 start
    public function getTransferRequest(GamePlatformUser $platformUser)
    {
        $detail                = $this->data['detail'];
        $this->request['url']  = $this->platform->request_url . ($detail->isIncome() ? '/player/deposit' : '/player/withdraw');
        $data['userCode']      = $platformUser->platform_user_id;
        $data['amount']        = $detail->amount;
        $data['transactionId'] = $this->tool->getTransferOrderNo($detail->order_no, true);
        $this->request['url']  = $this->request['url'] . '?' . http_build_query($data);
        $prefix                = strtolower($platformUser->currency) . '_';
        return $this->setRequest('transfer', $data, $prefix);
    }

    public function analysisTransferResponse($response)
    {
        return $this->tool->checkResponse($response, 'transfer', $this->data);
    }

    # 转账 end

    # 检查订单 start
    public function getCheckRequest(GamePlatformUser $platformUser)
    {
        $this->request['url']  = $this->platform->request_url . '/player/depositwithdraw/status';
        $detail                = $this->data['detail'];
        $data['transactionId'] = $this->tool->getTransferOrderNo($detail->order_no, true);
        $this->request['url']  = $this->request['url'] . '?' . http_build_query($data);
        $prefix                = strtolower($platformUser->currency) . '_';
        return $this->setRequest('check', $data, $prefix);
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
            foreach ($this->dateFilters as $dateFilter) {
                try {
                    $result = $this->singlePull($prefix, $dateFilter);
                } catch (\Exception $e) {
                    Log::info('B46 拉取' . $prefix . '数据失败，失败原因：' . $e->getMessage());
                    continue;
                }
                $data[] = $result;
            }
        }
        return $this->tool->insertBetDetails($data);
    }

    public function singlePull($prefix, $dateFilter = 'update_date')
    {
        $this->request['url']    = $this->platform->report_request_url . '/report/all-wagers';
        $schedule                = $this->data['schedule'];
        $data['timestamp']       = milli_time();
        $data['dateFrom']        = $this->tool->transferGMT8($schedule->start_at, false)->subMinutes($this->platform->offset)->toDateTimeString();
        $data['dateTo']          = $this->tool->transferGMT8($schedule->end_at, false)->toDateTimeString();
        $data['settle']          = -1;
        $data['filterBy']        = $dateFilter;
        $this->request['url']    = $this->request['url'] . '?' . http_build_query($data);
        $this->request['method'] = 'GET';
        $request                 = $this->setRequest('pull', $data, $prefix);
        $response                = $this->call($request);
        return $this->tool->checkResponse($response, 'pull', $this->data);
    }

    # 拉取报表 end

    public function setRequest($method, $data, $prefix)
    {
        $this->request['headers']['userCode'] = $this->account[$prefix . 'agent_code'];
        $this->request['headers']['token']    = $this->tool->generateToken($this->account[$prefix . 'agent_code'], $this->account[$prefix . 'agent_key'], $this->account[$prefix . 'secret_key']);
        $request = $this->request;
        $this->tool->requestLog($method, $request);
        return $request;
    }
}
