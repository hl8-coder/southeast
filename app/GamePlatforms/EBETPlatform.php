<?php
namespace App\GamePlatforms;

use App\GamePlatforms\Tools\EBETTool;
use App\Models\GamePlatformUser;
use App\Repositories\GamePlatformUserRepository;
use Illuminate\Support\Facades\Log;

class EBETPlatform extends BaseGamePlatform
{
    /**
     * @var EBETTool
     */
    protected $tool;

    protected $prefixes = ['vnd_', 'thb_'];

    # 注册 start
    public function getRegisterRequest(GamePlatformUser $platformUser)
    {
        $prefix = $this->getPrefix($platformUser);
        $this->request['url'] = $this->getPrefixUrl($prefix) . '/syncuser';
        $data['username']     = $platformUser->name;
        $data['lang']         = $this->tool->getPlatformLanguage($platformUser->user->language);
        $data['signature']    = $this->tool->sign($data['username'], $prefix);
        $data['currency']     = $platformUser->currency;

        return $this->setRequest('register', $data, $prefix);
    }

    public function analysisRegisterResponse($response)
    {
        return $this->tool->checkResponse($response, 'register', $this->data);
    }
    # 注册 end

    # 登录 start
    public function login()
    {
        # 获取已注册会员
        $platformUser = $this->getPlatformUser();
        $prefix = $this->getPrefix($platformUser);

        $accessToken = $this->tool->getAccessToken($platformUser);

        $data['username']       = $platformUser->name;
        $data['accessToken']    = $accessToken;
        $data['language']       = $this->tool->getPlatformLanguage($platformUser->user->language);

        if ('EBET_Live' != $this->data['code']) {
            $data['dgt'] = $this->data['code'];
        }

        # 判断地址中有无出现?
        $prefixLauncherRequestUrl = $this->getPreLauncherRequestUrl($prefix);
        if (false === strpos($prefixLauncherRequestUrl, '?')) {
            $url = $prefixLauncherRequestUrl . '?' . http_build_query($data);
        } else {
            $url = $prefixLauncherRequestUrl . '&' . http_build_query($data);
        }

        $this->tool->requestLog('login', $url);

        return $url;
    }

    public function loginCallBack()
    {
        $status = 401;

        $data = json_decode($this->data, true);

        $this->tool->requestLog('login call back', $data);

        $gamePlatformUser = GamePlatformUserRepository::findByNameAndPlatform($this->platform->code, $data['username']);
        $prefix = $this->getPrefix($gamePlatformUser);
        $channelId = $this->getPrefixChannelId($prefix);

        if ('RegisterOrLoginReq' == $data['cmd']
            && 4 == $data['eventType']
            && $channelId == $data['channelId']
            && strtolower($gamePlatformUser->name) == $data['username']
        ) {
            # 验证accessToken
            $accessToken = $this->tool->getAccessToken($gamePlatformUser);
            if ($accessToken == $data['accessToken'] && $this->tool->checkSign($data['timestamp'] . $data['accessToken'], $data['signature'], $prefix)) {
                $status = 200;
            }
        }
        return [
            'status'        => $status,
            'subchannelId'  => 0,
            'accessToken'   => $data['accessToken'],
            'username'      => $data['username'],
            'nickname'      => $data['username'],
        ];
    }
    # 登录 end

    # 查询余额 start
    public function getBalanceRequest(GamePlatformUser $platformUser)
    {
        $prefix = $this->getPrefix($platformUser);

        $this->request['url'] = $this->getPrefixUrl($prefix) . '/getusermoney';
        $data['username']     = $platformUser->name;
        $data['signature']    = $this->tool->sign($data['username'], $prefix);
        $data['currency']     = $platformUser->currency;

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
        $prefix = $this->getPrefix($platformUser);
        $detail  = $this->data['detail'];
        $this->request['url'] = $this->getPrefixUrl($prefix) . '/recharge';

        $data['username']      = $platformUser->name;
        $data['money']         = $detail->isIncome() ? $detail->amount : -1 * $detail->amount;
        $data['rechargeReqId'] = $this->tool->getTransferOrderNo($detail->order_no);
        $data['timestamp']     = milli_time();
        $data['signature']     = $this->tool->sign([$data['username'], $data['timestamp']], $prefix);
        $data['typeId']        = $this->platform->message['wallet_type'];
        $data['currency']      = $platformUser->currency;

        return $this->setRequest('transfer', $data, $prefix);
    }

    public function analysisTransferResponse($response)
    {
        return $this->tool->checkResponse($response, 'transfer', $this->data);
    }
    # 转账 end

    # 确认账单 start
    public function getCheckRequest(GamePlatformUser $platformUser)
    {
        $prefix = $this->getPrefix($platformUser);
        $this->request['url'] = $this->getPrefixUrl($prefix) . '/rechargestatus';

        $detail = $this->data['detail'];
        $data['rechargeReqId'] = $this->tool->getTransferOrderNo($detail->order_no);
        $data['signature']     = $this->tool->sign($data['rechargeReqId'], $prefix);
        $data['currency']      = $platformUser->currency;

        return $this->setRequest('check', $data, $prefix);
    }

    public function analysisCheckResponse($response)
    {
        return $this->tool->checkResponse($response, 'check', $this->data);
    }
    # 确认账单 end

    # 拉取报表 start
    public function pull()
    {
        $data = [];
        foreach ($this->prefixes as $prefix) {
            try {
                $result = $this->singlePull($prefix);
            } catch (\Exception $e) {
                Log::info('EBET 拉取' . $prefix . '数据失败，失败原因：' . $e->getMessage());
                continue;
            }
            $data[$prefix] = $result;
        }
        return $this->tool->insertBetDetails($data);

    }

    public function singlePull($prefix)
    {
        $this->request['url'] = $this->getPrefixReportUrl($prefix) . '/userbethistory';
        $schedule = $this->data['schedule'];
        $data['timestamp']     = milli_time();
        $data['signature']     = $this->tool->sign($data['timestamp'], $prefix);
        $data['startTimeStr']  = $schedule->start_at->subMinutes($this->platform->offset)->toDateTimeString();
        $data['endTimeStr']    = $schedule->end_at->toDateTimeString();
        $data['pageSize']      = 5000;
        $request               = $this->setRequest('pull', $data, $prefix);
        $response              = $this->call($request);
        return $this->tool->checkResponse($response, 'pull', $this->data);
    }

    # 拉取报表 end

    # 踢出会员 start
    public function getKickOutRequest(GamePlatformUser $platformUser)
    {
        $prefix = $this->getPrefix($platformUser);
        $this->request['url'] = $this->getPrefixUrl($prefix) . '/logout';
        $data['username']     = $platformUser->name;
        $data['timestamp']     = milli_time();
        $data['signature']    = $this->tool->sign([$data['username'], $this->getPrefixChannelId($prefix), $data['timestamp']], $prefix);
        $data['currency']     = $platformUser->currency;

        return $this->setRequest('kick_out', $data, $prefix);
    }

    public function analysisKickOutResponse($response)
    {
        return $this->tool->checkResponse($response, 'kick_out', $this->data);
    }

    # 踢出会员 end

    public function setRequest($method, $data, $prefix)
    {
        $data['channelId']          = $this->getPrefixChannelId($prefix);
        $this->request['data']      = $data;
        $this->request['data_type'] = 'json';
        $this->tool->requestLog($method, $this->request);
        return $this->request;
    }

    public function getPrefixUrl($prefix)
    {
        return $this->account[$prefix . 'request_url'];
    }

    public function getPrefixReportUrl($prefix)
    {
        return $this->account[$prefix . 'report_request_url'];
    }

    public function getPreLauncherRequestUrl($prefix)
    {
        return $this->account[$prefix . 'launcher_request_url'];
    }

    public function getPrefixChannelId($prefix)
    {
        return  $this->account[$prefix .'channel_id'];
    }
}