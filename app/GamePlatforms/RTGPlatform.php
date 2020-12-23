<?php
namespace App\GamePlatforms;

use App\GamePlatforms\Tools\RTGTool;
use App\Models\GamePlatformProduct;
use App\Models\GamePlatformUser;
use Illuminate\Support\Facades\Cache;

class RTGPlatform extends BaseGamePlatform
{
    /**
     * @var RTGTool
     */
    protected $tool;

    # 注册 start
    public function getRegisterRequest(GamePlatformUser $platformUser)
    {
        $this->request['url']    = $this->platform->request_url . '/player';
        $this->request['method'] = 'put';

        $data['agentId']         = $this->account['agent_id'];
        $data['username']        = $platformUser->name;
        $data['languageId']      = $this->tool->getPlatformLanguage($platformUser->user->language);
        $data['currency']        = $this->tool->getPlatformCurrency($platformUser->currency);

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
        $this->request['url']    = $this->platform->request_url . '/GameLauncher';
        $this->request['method'] = 'post';

        $data['player']['playerLogin']  = $platformUser->name;
        $data['gameId']                 = $this->data['code'];
        $data['returnUrl']              = '';
        $data['locale']                 = $this->tool->getPlatformLanguage($platformUser->user->language);
        $data['isDemo']                 = !empty($this->data['is_try']) ? true : false;

        return $this->setRequest('login', $data);
    }

    public function analysisLoginResponse($response, $platformUser)
    {
        return $this->tool->checkResponse($response, 'login', $this->data);
    }
    # 登录 end

    # 查询余额 start
    public function getBalanceRequest(GamePlatformUser $platformUser)
    {
        $this->request['url'] = $this->platform->request_url . '/wallet';

        $data['playerLogin']  = $platformUser->name;

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
        if ($detail->isIncome()) {
            $this->request['url'] = $this->platform->request_url . '/wallet/deposit/' . $detail->conversion_amount;
        } else {
            $this->request['url'] = $this->platform->request_url . '/wallet/withdraw/' . $detail->conversion_amount;
        }
        $this->request['method']  = 'post';
        $data['playerLogin']      = $platformUser->name;
        $data['trackingOne']      = $this->tool->getTransferOrderNo($detail->order_no);

        return $this->setRequest('transfer', $data);
    }

    public function analysisTransferResponse($response)
    {
        $this->data['detail'] = $this->tool->checkResponse($response, 'transfer', $this->data);

        return $this->data['detail'];
    }
    # 转账 end

    # 检查订单 start
    # RTG没有检查订单接口，直接返回交易明细
    public function check()
    {
        return $this->data['detail'];
    }
    # 检查订单 end

    # 拉取报表 start
    public function getPullRequest()
    {
        $this->request['url'] = $this->platform->report_request_url . '/report/playergame';
        $schedule = $this->data['schedule'];
        $data['params']['agentId']     = $this->account['agent_id'];
        $data['params']['fromDate']    = $this->tool->parseUTC0($schedule->start_at->subMinutes($this->platform->offset));
        $data['params']['toDate']      = $this->tool->parseUTC0($schedule->end_at);

        return $this->setRequest('pull', $data);
    }

    public function analysisPullResponse($response)
    {
        $originBetDetails = $this->tool->checkResponse($response, 'pull', $this->data);

        return $this->tool->insertBetDetails($originBetDetails);
    }
    # 拉取报表 end

    # 获取游戏列表 start
    public function getGameListRequest()
    {
        $request['url']       = $this->platform->request_url . '/gamestrings?locale=en-US';
        $request['method']    = 'get';

        return $this->setRequest('transfer', $request);
    }

    public function analysisGameListResult($result)
    {
        $games = [];
        $now = now();
        $product = GamePlatformProduct::findProductByType($this->platform->code, GamePlatformProduct::TYPE_SLOT);
        foreach ($result as $key => $game) {
            $games[] = [
                'game_platform_id' => $this->platform->id,
                'product_code'     => $product->code,
                'type'             => GamePlatformProduct::TYPE_SLOT,
                'name'             => $game['name'],
                'code'             => $game['gameId'],
                'created_at'       => $now,
                'updated_at'       => $now,
            ];
        }
        if (!empty($games)) {
            batch_insert('Games', $games, true);
        }
    }
    # 获取游戏列表 end

    public function setRequest($method, $data)
    {
        $this->tool->requestLog($method, $data);

        $this->request['data']      = $data;
        $this->request['data_type'] = 'json';
        $this->request['headers']['Authorization'] = $this->auth();
        return $this->request;
    }

    public function auth()
    {
        $key = 'rtg_token';

        if (Cache::has($key)) {
            return Cache::get($key);
        } else {
            $data['username'] = $this->account['username'];
            $data['password'] = $this->account['password'];
            $url = $this->platform->request_url . '/start/token';
            $response = call_api($url, $data, [],'get', 'query');
            $token = $this->tool->checkResponse($response, 'auth', $this->data);
            Cache::put($key, $token, now()->addMinutes(10));
            return $token;
        }
    }
}