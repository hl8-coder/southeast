<?php
namespace App\GamePlatforms;

use App\GamePlatforms\Tools\PPTool;
use App\Models\GamePlatformUser;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;

# 注意，pp的会员第三方前缀是xx_,而不是xx
class PPPlatform extends BaseGamePlatform
{
    /**
     * @var PPTool
     */
    protected $tool;

    protected $dataTypes = ['RNG', 'R2'];

    /**
     * configuration call key
     */
    const TIMEPOINT_KEY = 'pp_last_timepoint';

    public function getLoginRequest(GamePlatformUser $platformUser)
    {
        $device = UserRepository::isPc($this->data['device']) ? 'WEB' : 'MOBILE';

        $this->request['url']       = $this->platform->request_url . '/game/start/';
        $data['externalPlayerId']   = $platformUser->name;
        $data['secureLogin']        = $this->account['secured_login'];
        $data['gameId']             = $this->data['code'];
        $data['language']           = $this->tool->getPlatformLanguage($platformUser->user->language);
        $data['platform']           = $device;
        
        if ($device == 'MOBILE') {
            $lobbyURL = env('APP_URL');
            $lobbyURL = str_replace('http://', '', $lobbyURL);
            $lobbyURL = str_replace('https://', '', $lobbyURL);
            $data['lobbyURL'] = $lobbyURL;
        }

        return $this->setRequest('login', $data);
    }

    # 注册 start
    public function getRegisterRequest(GamePlatformUser $platformUser)
    {

        $this->request['url']       = $this->platform->request_url . '/player/account/create/';
        $data['externalPlayerId']   = $platformUser->name;
        $data['secureLogin']        = $this->account['secured_login'];
        $data['currency']           = $this->tool->getPlatformCurrency($platformUser->currency);

        return $this->setRequest('register', $data);
    }

    public function analysisRegisterResponse($response)
    {
        return $this->tool->checkResponse($response, 'register', $this->data);
    }
    # 注册 end

    public function analysisLoginResponse($response, $platformUser)
    {
        if(!empty($this->data['is_try'])) {

            $data = [
                '{game_symbol}' => $this->data['code'],
                '{language}' => $this->tool->getPlatformLanguage($platformUser->user->language),
                '{currency_symbol}' => $this->tool->getPlatformCurrency($platformUser->currency),
                '{secureLogin}' => $this->account['secured_login'],
            ];

            $url = str_replace( array_keys($data), array_values($data), $this->platform->launcher_request_url);
            return $url;
        }

        return $this->tool->checkResponse($response, 'login', $this->data);
    }

    # 查询余额 start
    public function getBalanceRequest(GamePlatformUser $platformUser)
    {
        $this->request['url']           = $this->platform->request_url . '/balance/current/';
        $data['externalPlayerId']       = $platformUser->name;
        $data['secureLogin']            = $this->account['secured_login'];

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
        $amount = $detail->isIncome() ? $detail->amount : -1 * $detail->amount;
        $this->request['url']           = $this->platform->request_url . '/balance/transfer/';
        $data['externalPlayerId']       = $platformUser->name;
        $data['secureLogin']            = $this->account['secured_login'];
        $data['externalTransactionId']  = $this->tool->getTransferOrderNo($detail->order_no);
        $data['amount']                 = $amount;

        $request = $this->setRequest('transfer', $data);

        $this->tool->requestLog("transfer", $request);

        return $request;
    }

    public function analysisTransferResponse($response)
    {
        return $this->tool->checkResponse($response, 'transfer', $this->data);
    }
    # 转账 end

    # 检查订单 start
    public function getCheckRequest(GamePlatformUser $platformUser)
    {
        $detail  = $this->data['detail'];

        $this->request['url']           = $this->platform->request_url . '/balance/transfer/status/';
        $data['secureLogin']            = $this->account['secured_login'];
        $data['externalTransactionId']  = $this->tool->getTransferOrderNo($detail->order_no);

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
        foreach ($this->dataTypes as $type) {
            try {
                $result = $this->singlePull($type);
            } catch (\Exception $e) {
                Log::info('PP 拉取' . $type . '数据失败，失败原因：' . $e->getMessage());
                continue;
            }
            $data = array_merge($data, $result);
        }
        return $this->tool->insertBetDetails($data);
    }

    public function singlePull($type)
    {
        #  retrieve timepoint lsat request
//        $lastTimePoint = $this->tool->getLastTimePoint($type);

        $schedule = $this->data['schedule'];
        $timePoint = $schedule->start_at->subMinutes($this->platform->offset);
        $params = [
            'login'     => $this->account['secured_login'],
            'password'  => $this->account['secured_password'],
            'dataType'  => $type,
            'timepoint' => $timePoint->timestamp . '000',
        ];

//        if (!empty($lastTimePoint)) {
//            $params = array_merge($params, ['timepoint' => $lastTimePoint]);
//
//        }
        $this->request['url'] = $this->platform->report_request_url . '/gamerounds/finished/?' . http_build_query($params);
        $this->request['method'] = 'GET';

        $request = $this->setRequest('pull', $this->request);

        $response = $this->call($request);

        return $this->analysisResponse($response, $type);
    }

    public function analysisResponse($response, $type)
    {
        $parseResponse = $this->tool->checkResponse($response, 'pull', $this->data);

        // data process
        $lines = explode(PHP_EOL, $parseResponse);

        // getting timepoint from result
//        $timepoint = $lines[0];
//        $temp = explode('=', $timepoint);
//        $timepoint = array_unique($temp);
        // expected the last array;
        //update the config timepoint
//        $this->tool->setLastTimePoint($type, end($timepoint));


        if (isset($lines[1]) && !empty($lines[1]) ) {
            $header = str_getcsv($lines[1]);
        }

        $records = [];
        if (isset($lines[2]) && !empty($lines[2]) ) {
            // remove header and timepoint
            array_splice($lines, 0, 2);
            $csv = array_map('str_getcsv', $lines);
            foreach ($csv as $row) {
                if (!empty($row[0]) )
                    $records[] = array_combine($header, $row);
            }
        }

        return $records;
    }

    public function insertBetDetails($items)
    {
        $pullResult = $this->tool->insertBetDetails($items);
        return $pullResult;
    }

    # 踢出会员 start
    public function getKickOutRequest(GamePlatformUser $platformUser)
    {
        $this->request['url']       = $this->platform->request_url . '/game/session/terminate/';
        $data['secureLogin']        = $this->account['secured_login'];
        $data['externalPlayerId']   = $platformUser->name;
        return $this->setRequest('kick_out', $data);
    }

    public function analysisKickOutResponse($response)
    {
        return $this->tool->checkResponse($response, 'kick_out', $this->data);
    }

    # 踢出会员 end

    public function applyHash(&$args)
    {
        ksort($args);
        $param =  http_build_query($args) . $this->account['secure_key'];
        $args = array_merge( $args , [ 'hash' => md5($param)]);
    }
    
    public function setRequest($method, $data)
    {
        $this->tool->requestLog($method, $data);

        if ($method == 'pull') {
            return $this->request;
        }

        $this->applyHash($data);
        $this->request['data'] = $data;

        return $this->request;
    }

}
