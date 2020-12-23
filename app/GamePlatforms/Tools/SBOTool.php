<?php
namespace App\GamePlatforms\Tools;

use App\Models\User;
use Carbon\Carbon;
use App\Models\Game;
use App\Models\GameBetDetail;
use App\Repositories\GamePlatformTransferDetailRepository;
use Illuminate\Support\Facades\Log;

class SBOTool extends Tool
{
    public $platformClass;

    protected $languages = [
        'zh-CN' => 'zh-cN',
        'vi-VN' => 'vi-vn',
        'en-US' => 'en',
        'th'    => 'th-th',
    ];

    protected $odds = [
        User::ODDS_CHINA      => 'HK',
        User::ODDS_INDONESIAN => 'ID',
        User::ODDS_AMERICAN   => 'EU',
        User::ODDS_DECIMAL    => 'MY',
        User::ODDS_MALAY      => 'MY',
    ];

    # 第三方状态mapping
    protected  $platformMappingStatuses = [
        'half won'              => GameBetDetail::PLATFORM_STATUS_BET_SUCCESS,
        'half lose'             => GameBetDetail::PLATFORM_STATUS_BET_SUCCESS,
        'won'                   => GameBetDetail::PLATFORM_STATUS_BET_SUCCESS,
        'lose'                  => GameBetDetail::PLATFORM_STATUS_BET_SUCCESS,
        'draw'                  => GameBetDetail::PLATFORM_STATUS_BET_SUCCESS,
        'done'                  => GameBetDetail::PLATFORM_STATUS_BET_SUCCESS,
        'refund'                => GameBetDetail::PLATFORM_STATUS_BET_SUCCESS,
        'void'                  => GameBetDetail::PLATFORM_STATUS_CANCEL,
        'cancelled'             => GameBetDetail::PLATFORM_STATUS_CANCEL,
        'void(suspended match)' => GameBetDetail::PLATFORM_STATUS_CANCEL,
        'running'               => GameBetDetail::PLATFORM_STATUS_WAITING,
        'waiting'               => GameBetDetail::PLATFORM_STATUS_WAITING,
        'waiting rejected'      => GameBetDetail::PLATFORM_STATUS_BET_FAIL,
        'reject'                => GameBetDetail::PLATFORM_STATUS_BET_FAIL,
    ];


    public function transferBetDetail($originBetDetails)
    {
        $betDetails = [];
        $now = now();
        $totalRecords = ['vnd_' => 0, 'thb_' => 0];
        $game = Game::findByPlatformAndCode($this->platform->code, 'sbo_1');
        $userIds = [];
        foreach ($originBetDetails as $prefix => $gameBetDetails) {
            foreach ($gameBetDetails as $record) {
                $userIds[] = $record['username'];
            }
        }
        $users = $this->getUsers($userIds);

        foreach ($originBetDetails as $prefix => $gameBetDetails) {

            $totalRecords[$prefix] = $totalRecords[$prefix]  + count($gameBetDetails);

            foreach ($gameBetDetails as $key => $record) {

                if (!isset($users[$record['username']])) {
                    continue;
                }
                $user = $users[$record['username']];

                $winLose    = $this->getWindLoseAmount($record);
                $available  = $this->getAvailableBetAndProfit($record);
                $betDetails[] = [
                    'platform_code'     => $this->platform->code,
                    'product_code'      => $game->product_code,
                    'platform_currency' => $user->currency,
                    'order_id'          => $record['refNo'],
                    'game_code'         => $game->code,
                    'game_type'         => $game->type,
                    'game_name'         => $record['sportsType'],
                    'user_id'           => $user->id,
                    'user_name'         => $user->name,
                    'stake'             => $record['stake'],
                    'bet'               => $available['bet'],
                    'profit'            => $record['winLost'],
                    'prize'             => $record['stake'] + $record['winLost'],
                    'bet_at'            => $this->parseGMTPLus8($record['orderTime']),
                    'payout_at'         => $this->parseGMTPLus8($record['modifyDate']),
                    'odds'              => $record['odds'] . " " . $record['oddsStyle'],
                    'user_currency'     => $record['currency'],
                    'user_stake'        => $record['stake'],
                    'user_bet'          => $available['bet'],
                    'user_prize'        => $record['stake'] + $record['winLost'],
                    'user_profit'       => $record['winLost'],
                    'platform_profit'   => -1 * $record['winLost'],
                    'platform_status'   => $this->getPlatformStatus($record),
                    'available_bet'     => $available['bet'],
                    'available_profit'  => -1 * $available['profit'],
                    'bet_info'          => $this->getBetInfo($record),
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
            }
        }

        if (!empty($betDetails)) {
            # 添加总的投注明细表
            batch_insert('game_bet_details', $betDetails, true);
        }

        return [
            'origin_total'   => array_sum($totalRecords),
            'transfer_total' => count($betDetails),
        ];
    }

    public function checkResponse($response, $method, $data)
    {
        $result = get_response_body($response, 'json');
        $statusCode = $response->getStatusCode();
        $this->responseLog($method, $statusCode, $result);
        if ($statusCode >= 300) {
            error_response(500, 'request error.');
        } else {
            switch ($result['error']['id']) {
                case 0:
                    if ('register' == $method) {
                        return '';
                    } elseif ('login' == $method) {
                        return $result['url'];
                    } elseif ('balance' == $method) {
                        return format_number($result['balance'] - $result['outstanding']);
                    } elseif ('transfer' == $method) {
                        return GamePlatformTransferDetailRepository::setWaiting($data['detail']);
                    } elseif ('check' == $method) {
                        return $this->setTransferStatus($result, $data);
                    } elseif ('pull' == $method) {
                        return $result['result'];
                    } elseif ('register_agent' == $method) {
                        return true;
                    }else {
                        error_response(500, 'Unknown error.');
                    }
                    break;
                case 4601:
                    if('check' == $method) {
                        GamePlatformTransferDetailRepository::setWaitingAndAddCheckJob($data['detail']);
                    }
                default:
                    error_response(422, $result['error']['msg']);
                    break;
            }
        }
    }

    # 获取投注信息
    public function getBetInfo($record)
    {
        $betInfo = '';
        try {
            if (!empty($record['sportsType'])) {
                $sportType = $record['sportsType'];
                $betInfo .= $sportType . "\n";
            }

            foreach ($record['subBet'] as $parlay) {
                $betInfo = $this->singleBetInfo($parlay, $betInfo);
            }
            return $betInfo;
        } catch (\Exception $e) {
            Log::info($e->getMessage());
        }
        return $betInfo;
    }

    public function singleBetInfo($record, $betInfo)
    {
        $betInfo .= $record["betOption"]. "\n";
        if (!empty($record['hdp'])) {
            $betInfo .= $record['hdp'] . ' ';
        }

        $betInfo .= $record['ftScore'];

        if (!empty($record['odds'])) {
            $betInfo .= "\n@ " . $record['odds'];
        }

        $betInfo .= "\n";

        if (!empty($record['league'])) {
            $betInfo .= $record['match'] . "\n";
        }

        # 联赛
        if (!empty($record['league'])) {
            $betInfo .= $record['league'] . "\n";
        }

        # 结果
        if (!empty($record['status'])) {
            $betInfo .= $record['status'] . "\n";
        }

        $betInfo .= "\n";
        return $betInfo;
    }

    # 获取有效投注
    public function getAvailableBetAndProfit($record, $type='sports')
    {
        $result = [
            'bet'    => 0,
            'profit' => 0,
        ];

        if ('draw' == strtolower($record['status'])) {
            return $result;
        }

        if ('sports' == $type) {
            if( in_array(strtolower($record['status']), ['cancelled', 'refund', 'running', 'void', 'waiting', 'waiting rejected', 'void(suspended match)'])) {
                return $result;
            }
            $odds = $record['odds'];
            switch ($record['oddsStyle']) {
                case 'A': # 美国盘 -100 ~ -204
                    if ($odds >= -204 && $odds <= -100) {
                        return $result;
                    }
                    break;
                case 'E': # 欧洲盘  1.1 ~ 1.49
                    if ($odds >= 1.1 && $odds <= 1.49) {
                        return $result;
                    }
                    break;
                case 'H': # 香港盘  0.1 ~ 0.49
                    if ($odds >= 0.1 && $odds <= 0.49) {
                        return $result;
                    }
                    break;

                case 'I': # 印尼盘  -10 ~ -2.04
                    if ($odds >= -10 && $odds <= -2.04) {
                        return $result;
                    }
                    break;
                case 'M': # 马来盘  0.1 ~ 0.49
                    if ($odds >= 0.1 && $odds <= 0.49) {
                        return $result;
                    }
                    break;
            }
        }
        $result['bet']    = $record['stake'];
        $result['profit'] = $record['winLost'];
        return $result;
    }

    public function getPlatformStatus($record)
    {
        return $this->platformMappingStatuses[strtolower($record['status'])];
    }

    private function setTransferStatus($result, $data)
    {
        $detail  = $data['detail'];
        if(!empty($result['txnId']) && $result['txnId'] ==  $detail->order_no) {
            return GamePlatformTransferDetailRepository::setSuccess($data['detail']);
        }

        return GamePlatformTransferDetailRepository::setFail($data['detail']);
    }

    private function getWindLoseAmount($record){
        $status = strtolower($record['status']);
        if('lose'  == $status) {
            if(true  == $record['isHalfWonLose']) {
                return  floatval($record['winLost']) - floatval($record['actualStake']);
            }
            return -1 * floatval($record['actualStake']);
        }
        if('won'  == $status) {
            return  floatval($record['winLost']) - floatval($record['actualStake']);
        }
        return 0;
    }

    public function parseGMTPLus8($timeStr)
    {
        return Carbon::parse($timeStr)->addHours(12)->format('Y-m-d H:i:s');
    }

    public function parseGMTMinus4(Carbon $timeStr)
    {
        return $timeStr->subHours(12)->format('Y-m-d H:i:s');
    }

    public function getPayoutDate($record)
    {
        if(GameBetDetail::PLATFORM_STATUS_BET_SUCCESS == $this->getPlatformStatus($record)) {
            return ['modifyDate'];
        }

        return Carbon::parse($record['winLostDate'])->addDay()->toDateTimeString();
    }

    /**
     * 获取游戏平台对应的odd
     *
     * @param  string   $odd   赔率
     * @return mixed
     */
    public function getPlatformOdd($odd)
    {
        return isset($this->odds[$odd]) ? $this->odds[$odd] : $odd;
    }
}
