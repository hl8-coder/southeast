<?php
namespace App\GamePlatforms;

use App\GamePlatforms\Tools\SPTool;
use App\Models\GamePlatformTransferDetail;
use App\Models\GamePlatformUser;
use App\Repositories\GamePlatformTransferDetailRepository;
use App\Repositories\UserRepository;

class SPPlatform extends BaseGamePlatform
{
    /**
     * @var SPTool
     */
    protected  $tool;
    
    # 注册 start
    public function getRegisterRequest(GamePlatformUser $platformUser)
    {
        $this->request['url']    = $this->platform->request_url;

        $data['method']          = 'RegUserInfo';
        $data['Key']             = $this->account['secret_key'];
        $data['Time']            = now()->format('YmdHis');
        $data['Username']        = $platformUser->name;
        $data['CurrencyType']    = $this->tool->getPlatformCurrency($platformUser->currency);

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
        $data = [];
        $this->request['url']    = $this->platform->request_url;
        if (empty($this->data['is_try'])) {
            $data['method']          = 'LoginRequest';
            $data['Key']             = $this->account['secret_key'];
            $data['Time']            = now()->format('YmdHis');
            $data['Username']        = $platformUser->name;
            $data['GameCode']        = $this->data['code'];
            $data['CurrencyType']    = $this->tool->getPlatformCurrency($platformUser->currency);
            $data['Lang']            = $this->tool->getPlatformLanguage($platformUser->user->language);
            $data['Mobile']          = UserRepository::isPc($this->data['device']) ? 0 : 1;
        } else {
            $data = $this->getFunLoginRequest($platformUser);
        }

        return $this->setRequest('login', $data);
    }

    public function analysisLoginResponse($response, $platformUser)
    {
        $url = $this->tool->checkResponse($response, 'login', $this->data);

        return $url;
    }

    # 试玩登录
    public function getFunLoginRequest(GamePlatformUser $platformUser)
    {
        $data = [];
        $data['method']          = 'LoginRequestForFun';
        $data['Key']             = $this->account['secret_key'];
        $data['Time']            = now()->format('YmdHis');
        $data['Amount']          = 1000;
        $data['GameCode']        = $this->data['code'];
        $data['CurrencyType']    = $this->tool->getPlatformCurrency($platformUser->currency);
        $data['Lang']            = $this->tool->getPlatformLanguage($platformUser->user->language);
        $data['Mobile']          = UserRepository::isPc($this->data['device']) ? 0 : 1;

        return $data;
    }
    # 登录 end

    # 查询余额 start
    public function getBalanceRequest(GamePlatformUser $platformUser)
    {
        $this->request['url'] = $this->platform->request_url;
        $data['method']       = 'GetUserStatus';
        $data['Key']          = $this->account['secret_key'];
        $data['Time']         = now()->format('YmdHis');
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
        $detail  = $this->data['detail'];
        $this->request['url'] = $this->platform->request_url;

        $data['Key']             = $this->account['secret_key'];
        $data['Time']            = now()->format('YmdHis');
        $data['Username']        = $platformUser->name;

        $orderId = $detail->isIncome() ? 'IN' . $data['Time'] . $platformUser->name : 'OUT' . $data['Time'] . $platformUser->name;
        # 检查platformOrderId是否重复

        if (GamePlatformTransferDetail::query()->where('platform_order_no', $orderId)->exists()) {
            error_response('422', __('gamePlatform.TRANSFERS_EXCEEDED_THE_LIMIT'));
        }

        if ($detail->isIncome()) {
            $data['method']          = 'CreditBalance';
            $data['OrderId']         = $orderId;
            $data['CreditAmount']    = $detail->conversion_amount;
        } else {
            $data['method']          = 'DebitBalance';
            $data['OrderId']         = $orderId;
            $data['DebitAmount']     = $detail->conversion_amount;
        }
        GamePlatformTransferDetailRepository::setPlatformOrderNo($detail, $data['OrderId']);

        return $this->setRequest('transfer', $data);
    }

    public function analysisTransferResponse($response)
    {
        return $this->tool->checkResponse($response, 'transfer', $this->data);
    }

    # 转账 end

    # 确认账单 start
    public function getCheckRequest(GamePlatformUser $platformUser) {
        $this->request['url'] = $this->platform->report_request_url;
        $detail = $this->data['detail'];
        $data['method']      = 'CheckOrderId';
        $data['Key']         = $this->account['secret_key'];
        $data['Time']        = now()->format('YmdHis');
        $data['OrderId']     = $detail->platform_order_no;

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
        $this->request['url'] = $this->platform->report_request_url;
        $schedule = $this->data['schedule'];
        $data['method']       = 'GetAllBetDetailsForTimeInterval';
        $data['Key']          = $this->account['secret_key'];
        $data['Time']         = now()->format('YmdHis');
        $data['FromTime']     = $schedule->start_at->subMinutes($this->platform->offset)->toDateTimeString();
        $data['ToTime']       = $schedule->end_at->toDateTimeString();

        return $this->setRequest('pull', $data);
    }

    public function analysisPullResponse($response)
    {
        $originBetDetails = $this->tool->checkResponse($response, 'pull', $this->data);

        return $this->tool->insertBetDetails($originBetDetails);
    }
    # 拉取报表 end

    # 踢出会员 start
    public function getKickOutRequest(GamePlatformUser $platformUser)
    {
        $this->request['url'] = $this->platform->request_url;
        $data['method']       = 'KickUser';
        $data['Key']          = $this->account['secret_key'];
        $data['Time']         = now()->format('YmdHis');
        $data['Username']     = $platformUser->name;
        return $this->setRequest('kick_out', $data);
    }

    public function analysisKickOutResponse($response)
    {
        return $this->tool->checkResponse($response, 'kick_out', $this->data);
    }

    # 踢出会员 end

    public function setRequest($method, $data)
    {
        $this->tool->requestLog($method, $data);

        $this->request['data']['q'] = $this->tool->encrypt($data, $this->account);
        $this->request['data']['s'] = $this->tool->buildMd5($data, $this->account);

        $data['q'] = $this->request['data']['q'];
        $data['s'] = $this->request['data']['s'];

        $this->tool->requestLog($method, $data);

        return $this->request;
    }
}