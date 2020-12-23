<?php
namespace App\GamePlatforms;

use App\GamePlatforms\Tools\MGSTool;
use App\Models\Game;
use App\Models\GamePlatformProduct;
use App\Models\GamePlatformUser;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;

class MGSPlatform extends BaseGamePlatform
{
    /**
     * @var MGSTool
     */
    protected $tool;

    protected $prefixes = ['vnd_', 'thb_'];


    # 注册 start
    public function getRegisterRequest(GamePlatformUser $platformUser)
    {
        $prefix = $this->getPrefix($platformUser);
        $this->request['url']  = $this->platform->request_url . '/agents/:agent_code/players';
        $data['playerId']      = $platformUser->name;
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
        $prefix = $this->getPrefix($platformUser);
        $platformUser = $this->getPlatformUser();
        $device   = $this->data['device'];
        $data     = [];
        if ($game = Game::findByPlatformAndCode($this->platform->code, $this->data['code'])) {

            $this->request['url']    = $this->platform->request_url . '/agents/:agent_code/players/'.$platformUser->name.'/sessions';
            $data['langCode']   = $this->tool->getPlatformLanguage($platformUser->user->language);
            $data['platform']   = 'Desktop';

            if (!UserRepository::isPc($device)) {
                $data['launchType'] = 'HTML5';
                $data['platform'] = 'Mobile';
            }

            if($game->type == GamePlatformProduct::TYPE_LIVE){
                #Wanted to launch lobby here
                $data['contentCode'] = 'SMG_titaniumLiveGames_MP_Baccarat';
            } else {
                $data['contentCode'] = $game->code;
            }

            if ('tournament' == $game->code) {
                $data['contentCode'] = 1;
                $data['contentType'] = 'Tournament';
            }

            return $this->setRequest('login', $data, $prefix);

        }

    }

    public function analysisLoginResponse($response, $platformUser)
    {
        return $this->tool->checkResponse($response, 'login', $this->data);
    }
    # 登录 end

    # 查询余额 start
    public function getBalanceRequest(GamePlatformUser $platformUser)
    {
        $prefix = $this->getPrefix($platformUser);
        $this->request['url'] = $this->platform->request_url . '/agents/:agent_code/players/'. $platformUser->name .'?properties=balance';
        $this->request['method'] = 'GET';
        return $this->setRequest('balance', [], $prefix);
    }

    public function analysisBalanceResponse($response)
    {
        return $this->tool->checkResponse($response, 'balance', $this->data);
    }
    # 查询余额 end

    # 转账 start
    public function getTransferRequest(GamePlatformUser $platformUser)
    {
        $prefix = $this->getPrefix($platformUser);
        $detail  = $this->data['detail'];
        $this->request['url']           = $this->platform->request_url . '/agents/:agent_code/WalletTransactions';
        if('VND' == strtoupper($platformUser->currency)) {
            $data['amount']             = floatval($detail->amount) * 1000;
        }else{
            $data['amount']             = $detail->amount;
        }
        $data['externalTransactionId']  = $this->tool->getTransferOrderNo($detail->order_no);
        $data['playerId']               = $platformUser->name;
        $data['type']                   = $detail->isIncome() ? 'Deposit': 'Withdraw';
        $this->request['method'] = 'POST';
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
        $prefix = $this->getPrefix($platformUser);
        $detail  = $this->data['detail'];
        $this->request['url'] = $this->platform->request_url . '/agents/:agent_code/WalletTransactions/'. $detail['platform_order_no'];
        $this->request['method'] = 'GET';
        return $this->setRequest('check',[], $prefix);
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
                Log::info('MGS 拉取' . $prefix . '数据失败，失败原因：' . $e->getMessage());
                continue;
            }
            $data[$prefix] = $result;
        }
        return $this->tool->insertBetDetails($data);
    }

    public function singlePull($prefix)
    {
        $this->request['url']       = $this->platform->report_request_url . '/agents/:agent_code/bets';
        $this->request['method']    = 'GET';
        $lastUID = $this->tool->getLastUID($prefix);

        if(!empty($lastUID)) {
            $data['startingAfter'] = $lastUID;
        }
        $data['limit']          = 2000;
        $request = $this->setRequest('pull', $data, $prefix);
        $response = $this->call($request);
        return $this->tool->checkResponse($response, 'pull', ['schedule' => $this->data]);
    }
    # 拉取报表 end

    public function getBetDetails($bet, $prefix)
    {
        $this->request['url']       = $this->platform->report_request_url .'/agents/:agent_code/players/'.$bet['playerId'].'/betVisualizers';
        $this->request['method']    = 'POST';
        $data['betUid']             = $bet['betUID'];
        $data['langCode']           = 'en';
        $data['utcOffset']           = '8';
        $this->request = $this->setRequest('get_bet_details', $data, $prefix);
        try {
            $response = $this->call($this->request);
            return $this->tool->checkResponse( $response, 'get_bet_details', $data);
        } catch (\Exception $e) {
            Log::stack([strtolower($this->platform->code)])->info($e->getMessage());
            return false;
        }
    }

    /**
     * token
     * @return string
     */
    public function getToken($prefix)
    {
        $account    = $this->account;
        $url        = $account['token_url'];
        $data       = ['client_id' => $this->getPrefixAgentCode($prefix), 'client_secret' =>$this->getPrefixSecretKey($prefix), 'grant_type' => 'client_credentials'];
        $response   = call_api($url, $data, [],'POST');
        $result     = get_response_body($response, 'json');
        return $result['access_token'];
    }

    public function setRequest($method, $data, $prefix)
    {
        $this->request['headers']['Authorization'] = 'Bearer ' . $this->getToken($prefix);
        $this->request['url']       = str_replace(':agent_code', $this->getPrefixAgentCode($prefix), $this->request['url']);
        $this->request['data']      = $data;

        $this->tool->requestLog($method, $this->request);

        return $this->request;
    }

    public function getPrefixAgentCode($prefix)
    {
        return $this->account[$prefix . 'agent_code'];
    }

    public function getPrefixSecretKey($prefix)
    {
        return $this->account[$prefix . 'secret_key'];
    }

}
