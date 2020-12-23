<?php
namespace App\GamePlatforms;

use App\GamePlatforms\Tools\ISBTool;
use App\Models\GamePlatformUser;

class ISBPlatform extends BaseGamePlatform
{
    /**
     * @var ISBTool
     */
    protected  $tool;

    # 注册 start
    public function getRegisterRequest(GamePlatformUser $platformUser)
    {
        $data['playerid']       = $platformUser->name;
        $data['username']       = $platformUser->name;
        $data['currency']       = $this->tool->getPlatformCurrency($platformUser->currency);
        $data['action']['command'] = 'addPlayer';
        $data['action']['parameters']['real'] = true;

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
        $data['playerid']       = $platformUser->name;
        $data['username']       = $platformUser->name;
        $data['currency']       = $this->tool->getPlatformCurrency($platformUser->currency);
        $data['action']['command'] = 'getPlayerToken';

        return $this->setRequest('login', $data);
    }

    public function analysisLoginResponse($response, $platformUser)
    {
        $token = $this->tool->checkResponse($response, 'login', $this->data);

        $loginData['lang']              = $this->tool->getPlatformLanguage($platformUser->user->language);
        $loginData['cur']               = $this->tool->getPlatformCurrency($platformUser->user->currency);
        $loginData['mode']              = 1;
        $loginData['user']              = $platformUser->name;
        $loginData['uid']               = $platformUser->name;
        $loginData['token']             = $token;
        $loginData['allowFullScreen']   = 'false';
        $loginData['mode']              = !empty($this->data['is_try']) ? 0 : 1;
        return $this->platform->launcher_request_url . '/' . $this->data['code'] . '?' . http_build_query($loginData);
    }
    # 登录 end

    # 查询余额 start
    public function getBalanceRequest(GamePlatformUser $platformUser)
    {
        $data['playerid'] = $platformUser->name;
        $data['username'] = $platformUser->name;
        $data['currency'] = $this->tool->getPlatformCurrency($platformUser->currency);
        $data['action']['command'] = 'getPlayerBalance';

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
        $detail  = $this->data['detail'];

        $data['playerid'] = $platformUser->name;
        $data['username'] = $platformUser->name;
        $data['currency'] = $this->tool->getPlatformCurrency($platformUser->currency);
        $data['action']['parameters']['amount']         = $this->tool->turnPoint($detail->conversion_amount);
        $data['action']['parameters']['transactionid']  = $this->tool->getTransferOrderNo($detail->order_no);

        if ($detail->isIncome()) {
            $data['action']['command'] = 'depositFunds';
        } else {
            $data['action']['command'] = 'withdrawFunds';
        }

        return $this->setRequest('transfer', $data);
    }

    public function analysisTransferResponse($response)
    {
        return $this->tool->checkResponse($response, 'transfer', $this->data);
    }
    # 转账 end

    # 确认账单 start
    public function getCheckRequest(GamePlatformUser $platformUser)
    {
        $detail = $this->data['detail'];
        $data['playerid'] = $platformUser->name;
        $data['username'] = $platformUser->name;
        $data['currency'] = $this->tool->getPlatformCurrency($platformUser->currency);
        $data['action']['command']                       = 'checkTransfer';
        $data['action']['parameters']['transactions'][]  = $this->tool->getTransferOrderNo($detail->order_no);

        return $this->setRequest('check', $data);
    }

    public function analysisCheckResponse($response)
    {
        return $this->tool->checkResponse($response, 'check', $this->data);
    }
    # 确认账单 end

    # 拉取报表 start
    public function pull()
    {
        $result = $this->singlePull();

        $data = [];
        if (1 == $result['pages']) {
            $data[] = $result['report'];
        } else {
            for ($i=1; $i <= $result['pages']; $i++) {
                $result = $this->singlePull($i);
                $data[] = $result['report'];
            }
        }

        return $this->tool->insertBetDetails($data);
    }

    public function singlePull($page=1)
    {
        $schedule = $this->data['schedule'];
        $data['action']['command']                 = 'wagersFeed';
        $data['action']['parameters']['page']      = $page;
        $data['action']['parameters']['datetime']  = $schedule->end_at->subHours(8)->toDateTimeString();

        $request = $this->setRequest('pull', $data);

        $response = $this->call($request);

        return $this->tool->checkResponse($response, 'pull', $this->data);
    }
    # 拉取报表 end

    # 踢出会员 start
    public function getKickOutRequest(GamePlatformUser $platformUser)
    {
        $data['action']['command'] = 'killPlayerSessions';
        $data['action']['parameters']['players'] = [
            [
                'playerid' => $platformUser->name,
                'operator' => '0',
            ],
        ];
        return $this->setRequest('kick_out', $data);
    }

    public function analysisKickOutResponse($response)
    {
        return $this->tool->checkResponse($response, 'kick_out', $this->data);
    }

    # 踢出会员 end

    public function setRequest($method, $data)
    {
        $this->request['url']       = $this->platform->request_url . 'hash=' . $this->tool->hashMac($data, $this->account);
        $this->request['data']      = $data;
        $this->request['data_type'] = 'json';
        $this->tool->requestLog($method, $this->request);

        return $this->request;
    }
}
