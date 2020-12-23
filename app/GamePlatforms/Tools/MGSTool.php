<?php
namespace App\GamePlatforms\Tools;

use Carbon\Carbon;
use App\Models\ChangingConfig;
use App\Models\Game;
use App\Models\GameBetDetail;
use App\Repositories\GamePlatformTransferDetailRepository;
use Illuminate\Support\Facades\Log;

class MGSTool extends Tool
{
    public $platformClass;

    protected $currencies = [
        'VND' => 'VND',
        'THB' => 'THB',
    ];

    protected $languages = [
        'zh-CN' => 'zh-cn',
        'vi-VN' => 'en-us',
        'en-US' => 'en-us',
        'th'    => 'th-th',
    ];


    public function transferBetDetail($data)
    {
        $betDetails = [];
        $now = now();
        $lastBetUIDs = [];
        $class               = "App\\GamePlatforms\\" . strtoupper($this->platform->code) . 'Platform';
        $this->platformClass = new $class([null, $this->platform]);
        $originalCount = 0;

        foreach ($data as $prefix => $originBetDetails) {
            $originalCount += count($originBetDetails);
            $gameCodes = $this->getUniqueColumn($originBetDetails, 'gameCode');
            $games = Game::getByCodes($this->platform->code, $gameCodes);
            $userNames = $this->getUniqueColumn($originBetDetails, 'playerId');
            $users = $this->getUsers($userNames);

            foreach ($originBetDetails as $key => $record) {

                $lastBetUIDs[$prefix] = $record['betUID'];

                if (!$game = $games->where('code', $record['gameCode'])->first()) {
                    continue;
                }

                if (!isset($users[$record['playerId']])) {
                    continue;
                }

                $user = $users[$record['playerId']];

                if ('VND' == strtoupper($user->currency)) {
                    $record['betAmount'] = floatval($record['betAmount']) / 1000;
                    $record['payoutAmount'] = floatval($record['payoutAmount']) / 1000;
                }

                $availables = $this->getAvailableBetAndProfit($record);
                $profit = floatval($record['payoutAmount']) -  floatval($record['betAmount']);

                $betDetails[] = [
                    'platform_code'     => $this->platform->code,
                    'product_code'      => $game->product_code,
                    'platform_currency' => $user->currency,
                    'order_id'          => $record['betUID'],
                    'game_code'         => $game->code,
                    'game_type'         => $game->type,
                    'game_name'         => $game->getEnName(),
                    'user_id'           => $user->id,
                    'user_name'         => $user->name,
                    'stake'             => $record['betAmount'],
                    'bet'               => $availables['bet'],
                    'profit'            => $profit,
                    'prize'             => $record['payoutAmount'],
                    'bet_at'            => $this->parseUTCPLus8($record['gameStartTimeUTC']),
                    'payout_at'         => $this->parseUTCPLus8($record['gameEndTimeUTC']),
                    'user_currency'     => $user->currency,
                    'user_stake'        => $record['betAmount'],
                    'user_bet'          => $availables['bet'],
                    'user_prize'        =>  $record['payoutAmount'],
                    'user_profit'       => $profit,
                    'platform_profit'   => -1 * $profit,
                    'platform_status'   => 'Closed' == $record['betStatus'] ? GameBetDetail::PLATFORM_STATUS_BET_SUCCESS : GameBetDetail::PLATFORM_STATUS_CANCEL,
                    'available_bet'     => $availables['bet'],
                    'available_profit'  => -1 * $availables['profit'],
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

        $this->updateChangingConfig($lastBetUIDs);


        return [
            'origin_total'  => $originalCount,
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
            switch($method) {
                case 'register':
                    return '';
                    break;
                case 'login':
                    return $result['url'];
                    break;
                case 'balance':
                    if (!$user = $this->getUser($result['playerId'])) {
                        return 0;
                    }
                    if('VND' == strtoupper($user->currency)) {
                        return floatval($result['balance']['total']) / 1000;
                    }else{
                        return $result['balance']['total'];
                    }
                    break;
                case 'transfer':
                    GamePlatformTransferDetailRepository::setPlatformOrderNo($data['detail'], $result['id']);
                    return $this->setTransferStatus($result, $data);
                case 'check':
                    return $this->setTransferStatus($result, $data);
                    break;
                case 'pull':
                    return $result;
                    break;
                case 'get_bet_details':
                    return $result[0]['url'];
                    break;
                default:
                    error_response(422, '未知错误');
            }

        }
    }

    public function getBetInfo($record)
    {
//        $betDetailsUrl = $this->platformClass->getBetDetails($record);
        $betDetailsUrl = '#';
        return '<a href="'.$betDetailsUrl.'" target="_blank">See Details</a>';
    }



    # 获取有效投注
    public function getAvailableBetAndProfit($record)
    {
        $result = [
            'bet'    => 0,
            'profit' => 0,
        ];
        if (in_array(strtolower($record['betStatus']), ['Cancelled)'])) {
            return $result;
        }
        $result['bet']    = $record['betAmount'];
        $result['profit'] = floatval($record['payoutAmount']) -  floatval($record['betAmount']);
        return $result;
    }

    private function updateChangingConfig($lastBetUIDs)
    {
        foreach ($lastBetUIDs as $prefix => $lastBetUID) {
            ChangingConfig::setValue('mgs_' . $prefix . 'last_bet_id', $lastBetUID);
        }
    }

    public function getLastUID($prefix)
    {
        return ChangingConfig::findValue('mgs_' . $prefix . 'last_bet_id', null);
    }

    private function setTransferStatus($result, $data)
    {
        if (isset($result['status'])) {
            if('Succeeded' == $result['status']) {
                return GamePlatformTransferDetailRepository::setSuccess($data['detail']);
            }
            if('Inprogress' == $result['status'] || 'Unconfirmed' == $result['status']) {
                return GamePlatformTransferDetailRepository::setWaiting($data['detail']);
            }
            if('Failed' == $result['status']) {
                return GamePlatformTransferDetailRepository::setFail($data['detail']);
            }
        }
    }

    public function parseUTCPLus8($timeStr)
    {
        return Carbon::parse($timeStr)->addHours(8)->format('Y-m-d H:i:s');
    }
}
