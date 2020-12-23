<?php
namespace App\GamePlatforms;

use App\GamePlatforms\Tools\N2Tool;
use App\Models\GamePlatformTransferDetail;
use App\Models\GamePlatformUser;
use App\Repositories\GamePlatformUserRepository;
use App\Repositories\UserRepository;

class N2Platform extends BaseGamePlatform
{
    /**
     * @var N2Tool
     */
    protected $tool;

    /**
     * 注册
     * 1、获取请求参数
     * 2、发起请求
     * 3、解析结果
     * 4、返回第三方注册id并更新本地第三方平台会员id
     *
     * @return GamePlatformUser
     */
    public function register(GamePlatformUser $platformUser)
    {
        $platformUserId = '';

        $platformUser->updatePlatformUserId($platformUserId);

        return $platformUser;
    }
    # 注册 end

    # 登录 start
    public function login()
    {
        # 获取已注册会员
        $platformUser = $this->getPlatformUser();
        # 获取已注册会员
        $url = UserRepository::isMobile($this->data['device'])
            ? $this->account['mobile_domain'] . '/SingleLogin'
            : $this->account['desktop_domain'] . '/SingleLogin';

        $data['merchantcode']   = $this->account['merchant_code'];
        $data['userid']         = $platformUser->name;
        $data['uuid']           = $this->tool->getUuid($platformUser);
        $data['lang']           = $this->tool->getPlatformLanguage($platformUser->user->language);
        $data['gm']             = 2;

        if ('N2_Live' != $this->data['code']) {
            $data['gc'] = $this->data['code'];
        }

        $url = $url . '?' . http_build_query($data);

        $this->tool->requestLog('login', $url);

        return $url;
    }

    public function loginCallBack()
    {
        $status = 0;
        $this->tool->requestLog('login call back', $this->data);

        $data = $this->tool->analysisLoginCallBackXml($this->data);

        if (!$platformUser = GamePlatformUserRepository::findByNameAndPlatform($this->platform->code, $data['userid'])) {
            $status = 101;
        } else {
            $localUuid = $this->tool->getUuid($platformUser);
            if ('userverf' != $data['action'] || $data['uuid'] != $localUuid) {
                $status = 001;
            }
        }

        $response = '<?xml version="1.0" encoding="utf-16"?>';
        $response .= '<message>';
        $response .= '<status>Success</status>';
        $response .= '<result action="userverf">';
        $response .= '<element id="' . $this->tool->getRequestId('L') . '">';
        $response .= '<properties name="userid">' . $platformUser->name . '</properties>';
        $response .= '<properties name="username">' . $platformUser->name . '</properties>';
        $response .= '<properties name="uuid">' . $data['uuid'] . '</properties>';
        $response .= '<properties name="vendorid">' . $this->account['vendor_id'] . '</properties>';
        $response .= '<properties name="merchantpasscode">' . $this->account['merchant_passcode'] . '</properties>';
        $response .= '<properties name="clientip">' . $data['clientip'] . '</properties>';
        $response .= '<properties name="currencyid">' . $this->getCurrency($platformUser) . '</properties>';
        $response .= '<properties name="acode"></properties>';
        $response .= '<properties name="errdesc"></properties>';
        $response .= '<properties name="status">' . $status . '</properties>';
        $response .= '</element>';
        $response .= '</result>';
        $response .= '</message>';

        $this->tool->requestLog('login call back response', $response);

        return $response;
    }
    # 登录 end

    # 查询余额 start
    public function getBalanceRequest(GamePlatformUser $platformUser)
    {
        $this->request['url'] = $this->platform->request_url . '/transaction/CheckClient';
        $data = '<?xml version="1.0" encoding="utf-16"?>';
        $data .= '<request action="ccheckclient">';
        $data .= '<element id="' . $this->tool->getRequestId('C') . '">';
        $data .= '<properties name="userid">' . $platformUser->name . '</properties>';
        $data .= '<properties name="vendorid">' . $this->account['vendor_id'] . '</properties>';
        $data .= '<properties name="merchantpasscode">' . $this->account['merchant_passcode']. '</properties>';
        $data .= '<properties name="currencyid">' . $this->getCurrency($platformUser) . '</properties>';
        $data .= '</element>';
        $data .= '</request>';

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

        $data = '<?xml version="1.0" encoding="utf-16"?>';
        if ($detail->isIncome()) {
            $this->request['url'] = $this->platform->request_url . '/transaction/PlayerDeposit';
            $data .= '<request action="cdeposit">';
            $data .= '<element id="' . $this->tool->getRequestId('D') . '">';
            $data .= '<properties name="acode"></properties>';
        } else {
            $this->request['url'] = $this->platform->request_url . '/transaction/PlayerWithdrawal';
            $data .= '<request action="cwithdrawal">';
            $data .= '<element id="' . $this->tool->getRequestId('W') . '">';
        }

        $data .= '<properties name="userid">' . $platformUser->name . '</properties>';
        $data .= '<properties name="vendorid">' . $this->account['vendor_id'] . '</properties>';
        $data .= '<properties name="merchantpasscode">' . $this->account['merchant_passcode']. '</properties>';
        $data .= '<properties name="amount">' . $detail->amount . '</properties>';
        $data .= '<properties name="currencyid">' . $this->getCurrency($platformUser) . '</properties>';
        $data .= '<properties name="refno">' . $this->tool->getTransferOrderNo($detail->order_no) . '</properties>';
        $data .= '</element>';
        $data .= '</request>';

        return $this->setRequest('transfer', $data);
    }

    public function analysisTransferResponse($response)
    {
        $detail = $this->tool->checkResponse($response, 'transfer', $this->data);
        # 充值向第三方发送确认信息
        if ($detail->isIncome() && $detail->isWait()) {
            $this->responseDepositConfirm($detail);
        }
        return $detail;
    }

    # 确认存款成功
    public function responseDepositConfirm(GamePlatformTransferDetail $detail)
    {
        $data  = '<?xml version="1.0" encoding="utf-16"?>';
        $data .= '<request action="cdeposit-confirm">';
        $data .= '<element id="' . $detail->bet_order_id . '">';
        $data .= '<properties name="acode"></properties>';
        $data .= '<properties name="status">0</properties>';
        $data .= '<properties name="paymentid">' . $detail->platform_order_no . '</properties>';
        $data .= '<properties name="vendorid">' . $this->account['vendor_id'] . '</properties>';
        $data .= '<properties name="merchantpasscode">' . $this->account['merchant_passcode']. '</properties>';
        $data .= '</element>';
        $data .= '</request>';
        $request = $this->setRequest('deposit_confirm', $data);
        $response = $this->call($request);
        return $this->tool->checkResponse($response, 'deposit_confirm', ['detail' => $detail]);
    }
    # 转账 end

    # 检查方法
    public function check()
    {
        return $this->data['detail'];
    }


    # 拉取报表 start
    public function getPullRequest()
    {
        $this->request['url'] = $this->platform->report_request_url . '/Trading/GameInfo';
        $schedule = $this->data['schedule'];
        $data  = '<?xml version="1.0" encoding="utf-16"?>';
        $data .= '<request action="gameinfo">';
        $data .= '<element>';
        $data .= '<properties name="vendorid">' . $this->account['vendor_id'] . '</properties>';
        $data .= '<properties name="merchantpasscode">' . $this->account['merchant_passcode']. '</properties>';
        $data .= '<properties name="startdate">' . $schedule->start_at->subMinutes($this->platform->offset)->toDateTimeString() . '</properties>';
        $data .= '<properties name="enddate">' . $schedule->end_at->toDateTimeString() . '</properties>';
        $data .= '<properties name="timezone">480</properties>';
        $data .= '</element>';
        $data .= '</request>';

        return $this->setRequest('pull', $data);
    }

    public function analysisPullResponse($response)
    {
        $result = $this->tool->checkResponse($response, 'pull', $this->data);

        return $this->tool->insertBetDetails($result);
    }
    # 拉取报表 end

    # 获取游戏列表 start

    # 获取游戏列表 end

    public function setRequest($method, $data)
    {
        $this->tool->requestLog($method, $data);

        $this->request['data']      = $data;
        $this->request['data_type'] = 'body';
        $this->request['timeout']   = 30000;
        $this->request['headers']   = ['Content-Type' => 'application/xml'];
        return $this->request;
    }

    public function getCurrency(GamePlatformUser $platformUser)
    {
        if (in_array($platformUser->user_name, ['egtestn201', 'egtestn202', 'egtestn203', 'hl8testn201', 'hl8testn202'])) {
            return '1111';
        }

        return $this->tool->getPlatformCurrency($platformUser->user->currency);
    }
}