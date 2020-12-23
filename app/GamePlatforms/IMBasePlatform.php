<?php

namespace App\GamePlatforms;

use App\GamePlatforms\Tools\IMBaseTool;
use App\Repositories\UserRepository;
use App\Repositories\GamePlatformUserRepository;
use App\Models\GamePlatformUser;

class IMBasePlatform extends BaseGamePlatform
{
    /**
     * @var IMBaseTool
     */
    protected $tool;

    protected $prefixes = ['vnd', 'thb'];

    public function getPlatformUser($isRemoteRegister = true)
    {
        $platformUser = GamePlatformUserRepository::findByUserAndPlatform($this->user->id, $this->platform->code);

        # 先注册本地游戏平台会员
        if (!$platformUser) {
            $platformUser = GamePlatformUserRepository::userRegisterPlatform($this->user, $this->platform);
        }

        # 如果需要远程注册调用注册方法
        if (!$platformUser->isRemoteRegistered() && $isRemoteRegister) {
            $platformUser = $this->register($platformUser);
        }

        # 如果已经注册了，('IMSports','IMESports')在这里都转化为IMSports
        if (in_array($this->platform->code, ['IMSports','IMESports'])) {
            $platformUser = GamePlatformUserRepository::findByUserAndPlatform($this->user->id, 'IMSports');
        }

        return $platformUser;
    }

    # 注册 start
    public function getRegisterRequest(GamePlatformUser $platformUser)
    {
        $this->request['url']   = $this->platform->request_url . '/Player/Register';
        $data['PlayerId']       = $platformUser->name;
        $data['Currency']       = $this->tool->getPlatformCurrency($platformUser->currency);
        $data['Password']       = $platformUser->password;
        $prefix                 = strtolower($platformUser->currency);
        return $this->setRequest('register', $data, $prefix);
    }
    public function analysisRegisterResponse($response)
    {
        return $this->tool->checkResponse($response, 'register', $this->data);
    }

    # 注册 end

    # 登录 start
    public function getLoginRequest(GamePlatformUser $platformUser)
    {
        $device   = $this->data['device'];
        $data                   = [];
        $this->request['url']   = $this->platform->request_url .  (UserRepository::isPc($device) ? '/Game/NewLaunchGame' : '/Game/NewLaunchMobileGame');
        $data['PlayerId']       = $platformUser->name;
        $data['GameCode']       = $this->data['code'];
        $data['Language']       = $this->tool->getPlatformLanguage($platformUser->user->language);
        $data['ProductWallet']  = $this->productWallet;
        $data['IpAddress']      = $this->data['ip'];
        if('PT' == $this->platform->code) {
            $data['Language']       = 'EN';
            if ($this->user->isTestUser()) {
                $this->request['url']   = $this->platform->request_url .  (UserRepository::isPc($device) ? '/Game/LaunchFreeGame' : '/Game/LaunchFreeMobileGame');
            }
        }
        $prefix                 = strtolower($platformUser->currency);
        return $this->setRequest('login', $data, $prefix);
    }
    public function analysisLoginResponse($response, $platformUser)
    {
        return $this->tool->checkResponse($response, 'login', $this->data);
    }


    # 查询余额 start
    public function getBalanceRequest(GamePlatformUser $platformUser)
    {
        $this->request['url']   = $this->platform->request_url . '/Player/GetBalance';
        $data['PlayerId']       = $platformUser->name;
        $data['ProductWallet']  = $this->productWallet;
        $prefix                 = strtolower($platformUser->currency);
        return $this->setRequest('balance', $data, $prefix);
    }
    public function analysisBalanceResponse($response)
    {
        return $this->tool->checkResponse($response, 'balance', $this->data, $this->productWallet);
    }

    # 查询余额 end

    # 转账 start
    public function getTransferRequest(GamePlatformUser $platformUser)
    {
        $detail                = $this->data['detail'];
        $this->request['url']  = $this->platform->request_url . '/Transaction/PerformTransfer';
        $data['PlayerId']      = $platformUser->name;
        $data['ProductWallet'] = $this->productWallet;
        $data['transactionId'] = $this->tool->getTransferOrderNo($detail->order_no, true);
        if('102' == $this->productWallet && 'vnd' == strtolower($platformUser->currency)) {
            $data['amount']        = $detail->isIncome() ? $detail->amount*1000 : (-1 * $detail->amount*1000);
        }else{
            $data['amount']        = $detail->isIncome() ? $detail->amount : (-1 * $detail->amount);
        }
        $prefix                = strtolower($platformUser->currency);
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
        $this->request['url']  = $this->platform->request_url . '/Transaction/CheckTransferStatus';
        $detail                = $this->data['detail'];
        $data['PlayerId']      = $platformUser->name;
        $data['transactionId'] = $this->tool->getTransferOrderNo($detail->order_no, true);
        $data['ProductWallet'] = $this->productWallet;
        $prefix                = strtolower($platformUser->currency);
        return $this->setRequest('check', $data, $prefix);
    }
    public function analysisCheckResponse($response)
    {
        return $this->tool->checkResponse($response, 'check', $this->data, $this->productWallet);
    }
    # 检查订单 end

    # 拉取报表 start
    public function pull()
    {
        $data = [];
        foreach ($this->prefixes as $prefix) {
            $result = $this->singlePull($prefix, $this->productWallet, 1);
            if(isset($result['Result'])) {
                if (1 == $result['Pagination']['TotalPage']) {
                    $data[$prefix] = $result['Result'];
                } else {
                    $tempData = [];
                    for ($i = 1; $i <= $result['pages']; $i++) {
                        $result = $this->singlePull($prefix, $this->productWallet, $i);
                        if (isset($result['Result'])) {
                            $tempData[] = array_merge($tempData, $result['Result']);
                        }
                    }
                    $data[$prefix] = $tempData;
                }
            }
        }
        try {
            $this->pullOldSportsData();
        }catch (\Exception $e){
        }
        return $this->tool->insertBetDetails($data);
    }

    public function pullOldSportsData()
    {
        $data = [];
        foreach ($this->prefixes as $prefix) {
            if('102' != $this->productWallet) {
                $result = $this->singlePull($prefix, $this->productWallet, 1, true);
                if (isset($result['Result'])) {
                    if (1 == $result['Pagination']['TotalPage']) {
                        $data[$prefix] = $result['Result'];
                    } else {
                        $tempData = [];
                        for ($i = 1; $i <= $result['pages']; $i++) {
                            $result = $this->singlePull($prefix, $this->productWallet ,$i, true);
                            if (isset($result['Result'])) {
                                $tempData[] = array_merge($tempData, $result['Result']);
                            }
                        }
                        $data[$prefix] = $tempData;
                    }
                }
            }
        }
        return $this->tool->insertBetDetails($data, true);
    }

    public function singlePull($prefix, $productWallet, $page = 1, $oldData=false)
    {
        $this->request['url']    = $this->platform->report_request_url . '/Report/GetBetLog';
        $schedule                = $this->data['schedule'];
        $data['ProductWallet']   = $productWallet;
        if(true == $oldData) {
            $data['DateFilterType'] = 2;
            $data['BetStatus']      = 1;
            $data['StartDate']      = $schedule->start_at->subDays(5)->format('Y-m-d H.i.s');
        }else {
            $data['DateFilterType'] = 1;
            $data['BetStatus']      = 0;
            $data['StartDate']      = $schedule->start_at->subMinutes($this->platform->offset)->format('Y-m-d H.i.s');
        }
        $data['EndDate']         = $schedule->end_at->format('Y-m-d H.i.s');
        $data['PageSize']        = 1000;
        $data['page']            = $page;
        $data['Currency']        = strtoupper($prefix);
        $data['Language']        = 'EN';
        $request                 = $this->setRequest('pull', $data, $prefix);
        $response                = $this->call($request);
        return $this->tool->checkResponse($response, 'pull', $this->data);
    }
    # 拉取报表 end

    public function setRequest($method, $data, $prefix)
    {
        $data['MerchantCode']   = $this->account[$prefix . '_merchant_code'];
        $this->request['data']  = $data;
        $this->request['headers']   = ['Content-Type' => 'application/json'];
        $this->request['data_type'] = 'json';
        $this->tool->requestLog($method, $this->request);
        return $this->request;
    }
}
