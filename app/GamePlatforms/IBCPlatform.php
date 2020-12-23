<?php
namespace App\GamePlatforms;

use App\GamePlatforms\Tools\IBCTool;
use App\Models\GamePlatformUser;
use Illuminate\Support\Facades\Log;

class IBCPlatform extends BaseGamePlatform
{
    /**
     * @var IBCTool
     */
    protected $tool;

    # 注册 start
    public function getRegisterRequest(GamePlatformUser $platformUser)
    {
        $this->request['url']       = $this->platform->request_url . '/CreateMember';
        $data['vendor_member_id']   = $platformUser->name;
        $data['operatorId']         = $this->account['operator_id'];
        $data['firstname']          = '';
        $data['lastname']           = '';
        $data['username']           = $platformUser->name;
        $data['oddstype']           = $this->tool->getOddsType($platformUser->user->odds);
        $data['currency']           = app()->isLocal() ? 20 : $this->tool->getPlatformCurrency($platformUser->user->currency);
        $data['maxtransfer']        = $this->account['max_transfer'];
        $data['mintransfer']        = $this->account['min_transfer'];

        return $this->setRequest('register', $data);
    }

    public function analysisRegisterResponse($response)
    {
        return $this->tool->checkResponse($response, 'register', $this->data);
    }
    # 注册 end

    /**
     *
     * 登录
     * 1、获取第三方会员，如果未注册调用注册接口
     * 2、返回游戏链接
     *
     * @return mixed
     */
    public function login()
    {
        if ($this->user->isTestUser()) {
            $data = [
                'lang'          => $this->tool->getPlatformLanguage($this->data['language']),
                'skincolor'     => isset($this->account['skincolor']) ? $this->account['skincolor'] : 'bl002',
            ];

            if ($this->isPC()) {
                $data = $this->tool->getLoginAct($this->data['code'], $data);
                $url = $this->account['try_pc_url'];
            } else {
                $data = $this->tool->getLoginTypes($this->data['code'], $data);
                $url = $this->account['try_mobile_url'];
            }

            return $url = $url . '?' . http_build_query($data);
        } else {
            return parent::login();
        }
    }

    # 登录 start
    public function getLoginRequest(GamePlatformUser $platformUser)
    {
        $this->request['url']   = $this->platform->request_url . '/LogIn';
        $data['vendor_member_id']   = $platformUser->name;

        return $this->setRequest('login', $data);
    }

    public function analysisLoginResponse($response, $platformUser)
    {
        $result = $this->tool->checkResponse($response, 'login', $this->data);

        $data = [
            'token'         => $result,
            'lang'          => $this->tool->getPlatformLanguage($platformUser->user->language),
            'skincolor'     => isset($this->account['skincolor']) ? $this->account['skincolor'] : 'bl002',
        ];

        if ($this->isPC()) {
            $data = $this->tool->getLoginAct($this->data['code'], $data);
            $url = $this->platform->launcher_request_url;
        } else {
            $data = $this->tool->getLoginTypes($this->data['code'], $data);
            $url = $this->account['mobile_url'];
        }

        # 拼接返回地址
        $url = $url . '?' . http_build_query($data);

        return $url;
    }
    # 登录 end

    # 查询余额 start
    public function getBalanceRequest(GamePlatformUser $platformUser)
    {
        $this->request['url'] = $this->platform->request_url . '/CheckUserBalance';
        $data['vendor_member_ids']  = $platformUser->name;
        $data['wallet_id']          = 1;

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
        $this->request['url'] = $this->platform->request_url . '/FundTransfer';

        $data['vendor_member_id']  = $platformUser->name;
        $data['vendor_trans_id']   = $this->tool->getTransferOrderNo($detail->order_no);
        $data['amount']            = $detail->conversion_amount;
        $data['currency']          = app()->isLocal() ? 20 : $this->tool->getPlatformCurrency($platformUser->user->currency);
        $data['direction']         = $detail->isIncome() ? 1 : 0;
        $data['wallet_id']         = 1;

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
        $this->request['url'] = $this->platform->report_request_url . '/CheckFundTransfer';

        $detail = $this->data['detail'];
        $data['vendor_trans_id'] = $this->tool->getTransferOrderNo($detail->order_no);
        $data['wallet_id']       = 1;

        return $this->setRequest('check', $data);
    }

    public function analysisCheckResponse($response)
    {
        return $this->tool->checkResponse($response, 'check', $this->data);
    }
    # 确认账单 end

    # 拉取报表 start
    public function getPullRequest()
    {
        $this->request['url'] = $this->platform->request_url . '/GetBetDetail';
        $data['version_key']  = $this->tool->getVersionKey();

        return $this->setRequest('pull', $data);
    }

    public function analysisPullResponse($response)
    {
        $originBetDetails = $this->tool->checkResponse($response, 'pull', $this->data);

        return $this->tool->insertBetDetails($originBetDetails);
    }
    # 拉取报表 end

    # 更新会员odds start
    public function updateOdds()
    {
        $platformUser = $this->getPlatformUser();
        $this->request['url'] = $this->platform->request_url . '/UpdateMember';
        $data['vendor_member_id']   = $platformUser->name;
        $data['firstname']          = '';
        $data['lastname']           = '';
        $data['oddstype']           = $this->tool->getOddsType($platformUser->user->odds);
        $data['maxtransfer']        = $this->account['max_transfer'];
        $data['mintransfer']        = $this->account['min_transfer'];
        $request = $this->setRequest('update', $data);

        $response = $this->call($request);

        return $this->tool->checkResponse($response, 'update', $this->data);
    }
    # 更新会员资料 end

    public function setRequest($method, $data)
    {
        $this->tool->requestLog($method, $data);

        $data['vendor_id']     = $this->account['vendor_id'];
        $this->request['data'] = $data;
        return $this->request;
    }

    # 获取联盟名称
    public function getLeagueName($leagueId)
    {
        $name = '';

        $this->request['url']  = $this->platform->request_url . '/GetLeagueName';
        $data['league_id']     = $leagueId;
        $this->request = $this->setRequest('get_league_name', $data);

        try {
            $response = $this->call($this->request);

            $name = $this->tool->checkResponse($response, 'get_league_name', $this->data);

        } catch (\Exception $e) {
            Log::stack([strtolower($this->platform->code)])->info($e->getMessage());
            return $name;
        }

        return $name;
    }

    # 获取队伍名称
    public function getTeamName($teamId, $betType)
    {
        $name = '';

        $this->request['url']  = $this->platform->request_url . '/GetTeamName';
        $data['team_id']       = $teamId;
        $data['bet_type']      = $betType;
        $this->request = $this->setRequest('get_team_name', $data);

        try {
            $response = $this->call($this->request);

            $name = $this->tool->checkResponse($response, 'get_team_name', $this->data);

        } catch (\Exception $e) {
            Log::stack([strtolower($this->platform->code)])->info($e->getMessage());
            return $name;
        }

        return $name;
    }
}