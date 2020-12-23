<?php
namespace App\GamePlatforms\Tools;

use App\Models\Game;
use App\Models\GameBetDetail;
use App\Repositories\GamePlatformTransferDetailRepository;
use Carbon\Carbon;

class ISBTool extends Tool
{
    protected $currencies = [
        'VND' => 'VND',
        'THB' => 'THB',
    ];

    protected $languages = [
        'vi-VN' => 'VI',
        'en-US' => 'EN',
        'th'    => 'TH',
        'zh-CN' => 'CHS',
    ];

    protected $errors = [
        'R_01'  => 'Request',
        'R_02'  => 'Request',
        'R_03'  => 'Request',
        'R_04'  => 'Request',
        'R_05'  => 'Request',
        'R_06'  => 'Request',
        'AP_01' => 'addPlayer',
        'AP_02' => 'addPlayer',
        'AP_03' => 'addPlayer',
        'DF_01' => 'depositFunds',
        'WF_01' => 'withdrawFunds',
        'WF_02' => 'withdrawFunds',
        'WF_03' => 'withdrawFunds',
        'FE_01' => 'wagerFeed',
        'FE_02' => 'wagerFeed',
    ];

    public function hashMac($data, $account)
    {
        return hash_hmac('SHA256', json_encode($data), $account['secret_key']);
    }

    public function isBetType($type)
    {
        return 'BET' == $type;
    }

    public function isFreeBetType($type)
    {
        return 'FREE_ROUND_BET' == $type;
    }

    public function isWinType($type)
    {
        return 'WIN' == $type || 'FREE_ROUND_WIN' == $type;
    }

    public function isCancelType($type)
    {
        return 'CANCEL' == $type;
    }

    public function checkResponse($response, $method, $data)
    {
        $result = get_response_body($response, 'json');
        $statusCode = $response->getStatusCode();

        $this->responseLog($method, $statusCode, $result);

        if ($statusCode >= 300) {
            error_response(500, 'request error.');
        } else {
            if ('success' == $result['status']) {
                if ('register' == $method) {
                    return '';
                } elseif ('login' == $method) {
                    return $result['token'];
                } elseif ('balance' == $method) {
                    return $this->turnYuan($result['balance']);
                } elseif ('transfer' == $method) { # 如果转账成功，发起检查订单状态
                    return GamePlatformTransferDetailRepository::setSuccess($data['detail']);
                } elseif ('check' == $method) {
                    if (isset($result['transactions'][0]) && 'success' == $result['transactions'][0]['transactionstatus']) {
                        return GamePlatformTransferDetailRepository::setSuccess($data['detail']);
                    } else {
                        return GamePlatformTransferDetailRepository::setFail($data['detail'], $result['message']);
                    }
                } elseif ('pull' == $method) {
                    return $result;
                } elseif ('kick_out' == $method) {
                    return true;
                } else {
                    error_response(422, 'error');
                }
            } else {
                if ('transfer' == $method || 'check' == $method) {
                    return GamePlatformTransferDetailRepository::setFail($data['detail'], $result['message']);
                }
                error_response(422, $result['message']);
            }
        }
    }

    public function dealReport($originData, &$data)
    {
        foreach ($originData as $player) {
            foreach ($player['sessions'] as $session) {
                foreach ($session['rounds'] as $round) {

                    if ('ACTIVE' == $round['status']) {
                        continue;
                    }

                    $key = $player['playerid'] . $session['sessionid'] . $round['roundid'];

                    if (!isset($data[$key])) {
                        $data[$key] = [];
                        $data[$key]['playerid']      = $player['playerid'];
                        $data[$key]['sessionid']     = $session['sessionid'];
                        $data[$key]['gameid']        = $session['gameid'];
                        $data[$key]['roundid']       = $round['roundid'];

                        # 初始transaction数据
                        $data[$key]['type']          = '';
                        $data[$key]['bet']           = 0;
                        $data[$key]['bet_at']        = 0;
                        $data[$key]['after_balance'] = 0;
                        $data[$key]['prize']         = 0;
                        $data[$key]['payout_at']     = '';
                        $data[$key]['jpc']           = 0;
                        $data[$key]['jpw']           = 0;
                        $data[$key]['jpw_jpc']       = 0;
                        $data[$key]['status']        = GameBetDetail::PLATFORM_STATUS_BET_SUCCESS;
                        $this->dealTransactions($round['transactions'], $data[$key]);
                    } else {
                        $this->dealTransactions($round['transactions'], $data[$key]);
                    }
                }
            }
        }
    }

    /**
     * 合并transactions为一条
     *
     * @param $transactions
     * @param $data
     */
    public function dealTransactions($transactions, &$data)
    {
        foreach ($transactions as $transaction) {
            if ($this->isBetType($transaction['type'])) {
                $data['type']          = $transaction['type'];
                $data['bet']           = $transaction['amount'];
                $data['bet_at']        = $transaction['time'];
                $data['after_balance'] = $transaction['balance'];
                $data['prize']         = 0;
                $data['payout_at']     = '';
                $data['jpc']           = $transaction['jpc'];
                $data['status']        = GameBetDetail::PLATFORM_STATUS_BET_SUCCESS;
            } elseif ($this->isWinType($transaction['type'])) {
                $data['prize']         += $transaction['amount'];
                $data['jpw']           += $transaction['jpw'];
                $data['jpw_jpc']       += $transaction['jpw_jpc'];
                $data['after_balance'] = $transaction['balance'];
                $data['payout_at']     = $transaction['time'];
            } else {
                $data['status']        = GameBetDetail::PLATFORM_STATUS_BET_SUCCESS;
            }
        }
    }

    public function transferBetDetail($data)
    {
        $betDetails  = [];
        $originTotal = 0;
        $now = now();

        $playerIds = [];
        $gameIds = [];
        foreach ($data as $reports) {
            foreach ($reports as $report) {
                $playerIds[] = $report['playerid'];
                foreach ($report['sessions'] as $session) {
                    $gameIds[] = $session['gameid'];
                }
            }
        }
        $playerIds = array_unique($playerIds);
        $gameIds = array_unique($gameIds);
        $games = Game::getByCodes($this->platform->code, $gameIds);
        $users = $this->getUsers($playerIds);

        foreach ($data as $reports) {
            foreach ($reports as $report) {

                if (!isset($users[$report['playerid']])) {
                    continue;
                }

                $user = $users[$report['playerid']];

                foreach ($report['sessions'] as $session) {
                    if (!$game = $games->where('code', $session['gameid'])->first()) {
                        continue;
                    }

                    foreach ($session['rounds'] as $round) {

                        $originTotal += count($round['transactions']);

                        foreach ($round['transactions'] as $transaction) {

                            $stake              = 0;
                            $betAt              = null;
                            $prize              = 0;
                            $jpc                = 0;
                            $bet                = 0;
                            $payoutAt           = null;
                            $jpw                = 0;
                            $jpwJpc             = 0;
                            $availableBet       = 0;
                            $availableProfit    = 0;
                            $status             = GameBetDetail::PLATFORM_STATUS_BET_SUCCESS;

                            if ($this->isBetType($transaction['type'])) {
                                $stake          = $transaction['amount'];
                                $betAt          = !empty($transaction['time']) ? Carbon::parse($transaction['time'])->addHours(8)->toDateTimeString() : null;
                                $payoutAt       = $betAt;
                                $jpc            = $transaction['jpc'];
                                $bet            = $stake;
                                $availableBet   = $bet;
                                $availableProfit= $bet;
                            } elseif ($this->isFreeBetType($transaction['type'])) {
                                $stake          = $transaction['amount'];
                                $betAt          = !empty($transaction['time']) ? Carbon::parse($transaction['time'])->addHours(8)->toDateTimeString() : null;
                                $payoutAt       = $betAt;
                                $jpc            = $transaction['jpc'];
                                $bet            = 0;
                                $availableBet   = $bet;
                            } elseif ($this->isWinType($transaction['type'])) {
                                $prize          = $transaction['amount'];
                                $payoutAt       = !empty($transaction['time']) ? Carbon::parse($transaction['time'])->addHours(8)->toDateTimeString() : null;
                                $betAt          = $payoutAt;
                                $jpw            = $transaction['jpw'];
                                $jpwJpc         = $transaction['jpw_jpc'];
                                $availableProfit= -1 * $prize;
                            } elseif ($this->isCancelType($transaction['type'])) {
                                $status         = GameBetDetail::PLATFORM_STATUS_CANCEL;
                            }

                            $temp = [
                                'platform_code'         => $this->platform->code,
                                'product_code'          => $game->product->code,
                                'platform_currency'     => $report['currency'],
                                'order_id'              => $transaction['transactionid'],
                                'game_code'             => $game->code,
                                'game_type'             => $game->type,
                                'game_name'             => $game->getEnName(),
                                'user_id'               => $user->id,
                                'user_name'             => $user->name,
                                'issue'                 => $round['roundid'],
                                'stake'                 => $this->turnYuan($stake),
                                'user_stake'            => $this->turnYuan($stake),
                                'bet'                   => $this->turnYuan($bet),
                                'user_bet'              => $this->turnYuan($bet),
                                'prize'                 => $this->turnYuan($prize),
                                'user_prize'            => $this->turnYuan($prize),
                                'after_balance'         => $this->turnYuan($transaction['balance']),
                                'jpc'                   => $this->turnYuan($jpc),
                                'jpw'                   => $this->turnYuan($jpw),
                                'jpw_jpc'               => $this->turnYuan($jpwJpc),
                                'bet_at'                => $betAt,
                                'payout_at'             => $payoutAt,
                                'user_currency'         => $user->currency,
                                'platform_status'       => $status,
                                'available_bet'         => $this->turnYuan($availableBet),
                                'available_profit'      => $this->turnYuan($availableProfit),
                                'created_at'            => $now,
                                'updated_at'            => $now,
                            ];

                            $temp['profit']          = $temp['prize'] - $temp['stake'];
                            $temp['user_profit']     = $temp['profit'];
                            $temp['platform_profit'] = -1 * $temp['user_profit'];

                            $betDetails[] = $temp;
                        }
                    }
                }
            }

        }

        if (!empty($betDetails)) {
            # 添加总的投注明细表
            batch_insert('game_bet_details', $betDetails, true);
        }

        return [
            'origin_total'   => $originTotal,
            'transfer_total' => count($betDetails),
        ];
    }


    public function turnYuan($amount)
    {
        return $amount / 100;
    }

    public function turnPoint($amount)
    {
        return $amount * 100;
    }
}
