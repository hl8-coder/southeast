<?php
namespace App\GamePlatforms;

use App\GamePlatforms\Tools\GPITool;
use App\Models\Game;
use App\Models\GamePlatformProduct;
use App\Models\GamePlatformUser;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GPIPlatform extends BaseGamePlatform
{
    /**
     * @var GPITool
     */
    protected $tool;

    # 注册 start
    public function getRegisterRequest(GamePlatformUser $platformUser)
    {
        $this->request['url']    = $this->platform->request_url . '/createuser';
        $data['cust_id']        = $platformUser->name;
        $data['cust_name']      = $platformUser->name;
        $data['currency']       = $this->tool->getPlatformCurrency($platformUser->currency);
        $data['test_user']      = app()->isLocal() ? true : false;

        return $this->setRequest('register', $data);
    }

    public function analysisRegisterResponse($response)
    {
        return $this->tool->checkResponse($response, 'register', $this->data);
    }
    # 注册 end

    # 登录 start
    public function login()
    {
        $url = '';
        $platformUser = $this->getPlatformUser();
        $device   = $this->data['device'];
        $data     = [];
        if ($game = Game::findByPlatformAndCode($this->platform->code, $this->data['code'])) {
            $gameLoginCode = $this->tool->getLoginCode($this->data['code']);
            $data['token'] = $this->tool->getToken($platformUser);
            $data['op']    = $this->account['merch_id'];
            $data['lang']  = $this->tool->getPlatformLanguage($platformUser->user->language);

            # 这里是第三方属于真人，但是平台拆分成老虎机
            if (in_array($gameLoginCode, [1001, 1031, 1061, 1091, 1081])) {
                $game->type = GamePlatformProduct::TYPE_LIVE;
            }

            # 这里第三方属于lottery,但是平台拆分成games
            if (in_array($gameLoginCode, [
                'ladder',
                'rockpaperscissors',
                'thor',
                'taixiu',
                'luckyderby',
                'xocdia',
                'thaihilo',
                'fishprawncrab',
                'fishprawncrabgame',
                'moneyblast',
                'super98baccarat',
            ])) {
                $game->type = GamePlatformProduct::TYPE_LOTTERY;
            }

            switch ($game->type) {
                case GamePlatformProduct::TYPE_LIVE: # 真人
                    $url = $this->account['live_url'];
                    if ('GPI_Live' != $gameLoginCode) {
                        $data['tb'] = $gameLoginCode;
                    }

                    if (UserRepository::isPc($device)) {
                        $url .= '';
                    } else {
                        $url .= '/html5/mobile';
                        $data['homeURL'] = ''; # h5需要做
                    }
                    break;
                case GamePlatformProduct::TYPE_SLOT:
                    $data['fun'] = $this->isTry() ? 1 : 0;
                    if (UserRepository::isPc($device)) {
                        $url .= $this->account['slot_url'];
                    } else {
                        $url .= $this->account['slot_mobile_url'];
                    }
                    $url = $url . '/' . $gameLoginCode . '/';
                    break;
                case GamePlatformProduct::TYPE_LOTTERY:
                    if ($this->isTry()) {
                        $data['mode'] = 'Try';
                    }

                    $data['ticket'] = $data['token'];
                    $data['game']   = $gameLoginCode;
                    $data['vendor'] = $data['op'];
                    unset($data['op'], $data['token']);
                    $url = $this->account['lottery_url'] . '/Login';
                    break;
                case GamePlatformProduct::TYPE_P2P:
                    $data['fun'] = $this->isTry() ? 1 : 0;
                    $domain = !empty($this->data['domain']) ? $this->data['domain'] : 'http://47.89.25.81:3385';
                    $data['fundHistoryURL'] = $domain . '/user/history';
                    $data['fundTransferURL'] = $domain . '/user/transfer';
                    $data['depositURL'] = $domain . '/user/deposit';
                    $data['loginURL'] = $domain . '/p2p';
                    if(!empty($this->data['code'])) {
                        $url = $this->account['p2p_url'] .'/'. $this->data['code'];
                    }else {
                        $url = $this->account['p2p_url'] . '/html5Lobby';
                    }
                    break;
            }
        }

        $url = $url . '?' . http_build_query($data);

        $this->tool->requestLog('login', $url);

        return $url;
    }

    public function loginCallBack()
    {
        $status = -2;

        $this->tool->requestLog('login call back', $this->data);

        $tokens = explode('-', $this->data['ticket']);

        $platformUser = null;
        if (2 == count($tokens) && $platformUser = GamePlatformUser::find($tokens[0])) {
            # 验证tokenGPI
            $token = $this->tool->getToken($platformUser);
            if ($token == $this->data['ticket']) {
                $status = 0;
            }
        }

        $result = '<?xml version="1.0" encoding="UTF-8"?>';
        $result .= '<resp>';
        $result .= '<error_code>' . $status . '</error_code>';
        $result .= '<error_msg>' . (0 == $status ? '' : $this->tool->getError($status)) . '</error_msg>';

        if (0 == $status) {
            $result .= '<cust_id>' . $platformUser->name . '</cust_id>';
            $result .= '<cust_name>' . $platformUser->name . '</cust_name>';
            $result .= '<currency_code>' . $this->tool->getPlatformCurrency($platformUser->currency) . '</currency_code>';
            $result .= '<language>' . $this->tool->getPlatformLanguage($platformUser->user->language) . '</language>';
            $result .= '<country>' . $this->tool->getCountry($platformUser->currency) . '</country>';
            $result .= '<ip>' . '' . '</ip>';
            $result .= '<date_of_birth>' . Carbon::parse($platformUser->user->info->birth_at)->format('d-m-Y') . '</date_of_birth>';
            $result .= '<test_cust>' . ($platformUser->user->isTestUser() ? 'true' : 'false') . '</test_cust>';
        }

        $result .= '</resp>';

        return $result;
    }
    # 登录 end

    # 查询余额 start
    public function getBalanceRequest(GamePlatformUser $platformUser)
    {
        $this->request['url'] = $this->platform->request_url . '/getbalance';

        $data['cust_id']        = $platformUser->name;
        $data['currency']       = $this->tool->getPlatformCurrency($platformUser->currency);
        $data['test_cust']      = app()->isLocal() ? true : false;

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
            $this->request['url'] = $this->platform->request_url . '/credit';
        } else {
            $this->request['url'] = $this->platform->request_url . '/debit';
        }
        $data['cust_id']        = $platformUser->name;
        $data['currency']       = $this->tool->getPlatformCurrency($platformUser->currency);
        $data['amount']         = $detail->conversion_amount;
        $data['trx_id']         = $this->tool->getTransferOrderNo($detail->order_no);
        $data['test_cust']      = app()->isLocal() ? true : false;

        return $this->setRequest('transfer', $data);
    }

    public function analysisTransferResponse($response)
    {
        return $this->tool->checkResponse($response, 'transfer', $this->data);
    }
    # 转账 end

    # 检查订单 start
    public function getCheckRequest(GamePlatformUser $platformUser)
    {
        $this->request['url'] = $this->platform->request_url . '/check';
        $detail = $this->data['detail'];
        $data['trx_id']      = $this->tool->getTransferOrderNo($detail->order_no);

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
        $pullCount = [
            'origin_total'    => 0,
            'transfer_total'  => 0,
        ];

        foreach (['casino', 'slots', 'lottery', 'p2p'] as $product) {
            
            if('slots' == $product) {
                sleep(16);
            }

            try {
                $result = $this->singlePull($product);
            } catch (\Exception $e) {
                Log::stack(['gpi'])->info($product . ' pull error:' . $e->getMessage());
                error_response(422, $product . ' pull error:' . $e->getMessage());
            }
            $totalPage = $result['@attributes']['total_page'];
            $pageNum = $result['@attributes']['page_num'];
            if (1 == $totalPage) {
                $pullCount = $this->insertBetDetails($result, $pullCount);
            } else {

                if (1 == $pageNum) {
                    $pullCount = $this->insertBetDetails($result, $pullCount);
                }

                for ($i = 2; $i <= $totalPage; $i++) {
                    try {
                        if('slots' == $product) {
                            sleep(16);
                        }
                        $result = $this->singlePull($product, $i);
                        $pullCount = $this->insertBetDetails($result, $pullCount);
                    } catch (\Exception $e) {
                        Log::stack(['gpi'])->info($product . ' pull error:' . $e->getMessage());
                        error_response(422, $product . ' pull error:' . $e->getMessage());
                    }
                }
            }
        }

        return $pullCount;
    }

    public function singlePull($product, $page=1, $pageSize=500)
    {
        if ('p2p' == $product) {
            $this->request['url'] = $this->account['p2p_report_url'];
            $data['wallet_identity'] = $this->account['wallet_identity'];
        } else {
            $this->request['url'] = $this->platform->report_request_url;
            $data['product']  = $product;
        }
        $schedule = $this->data['schedule'];
        $data['date_from']    = $schedule->start_at->subMinutes($this->platform->offset)->toDateTimeString();
        $data['date_to']      = $schedule->end_at->toDateTimeString();
        $data['page_size']    = $pageSize;
        $data['page_num']     = $page;


        $request = $this->setRequest('pull', $data);

        $response = $this->call($request);

        return $this->tool->checkResponse($response, 'pull', $this->data);
    }

    public function singleProductPull($startAt, $endAt, $product, $page=1, $pageSize=500)
    {
        if('slots' == $product) {
            sleep(16);
        }

        $this->request['url'] = $this->platform->report_request_url;
        $data['date_from']    = $startAt;
        $data['date_to']      = $endAt;
        $data['page_size']    = $pageSize;
        $data['page_num']     = $page;
        $data['product']      = $product;

        $request = $this->setRequest('pull', $data);

        $response = $this->call($request);

        return $this->tool->checkResponse($response, 'pull', $this->data);
    }

    public function insertBetDetails($items, $pullCount)
    {
        $pullResult = $this->tool->insertBetDetails($items);
        $pullCount['origin_total']   += $pullResult['origin_total'];
        $pullCount['transfer_total'] += $pullResult['transfer_total'];

        return $pullCount;
    }
    # 拉取报表 end

    # 踢出会员 start
    public function getKickOutRequest(GamePlatformUser $platformUser)
    {
        if (app()->isLocal()) {
            $url = 'http://casino.bet8uat.com';
        } else {
            $url = 'http://csnbo.gpiops.com';
        }

        $this->request['url']   = $url . '/csnbo/api/kickoff.html';
        $data['cust_id']        = $platformUser->name;

        return $this->setRequest('kick_out', $data);
    }

    public function analysisKickOutResponse($response)
    {
        return $this->tool->checkResponse($response, 'kick_out', $this->data);
    }

    # 踢出会员 end


    public function setRequest($method, $data)
    {
        $data['merch_id']       = $this->account['merch_id'];
        $data['merch_pwd']      = $this->account['merch_pwd'];

        $this->request['data']      = $data;
        $this->request['data_type'] = 'query';
        $this->request['method']    = 'get';
        $this->tool->requestLog($method, $this->request);

        return $this->request;
    }
}
