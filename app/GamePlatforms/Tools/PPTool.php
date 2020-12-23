<?php
namespace App\GamePlatforms\Tools;

use App\Models\ChangingConfig;
use App\Models\Config;
use App\Models\Game;
use App\Models\GameBetDetail;
use App\Repositories\GamePlatformTransferDetailRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;

class PPTool extends Tool
{
    public $platformClass;

    protected $currencies = [
        'VND' => 'VND2',
        'THB' => 'THB',
    ];

    protected $languages = [
        'zh-CN' => 'zh',
        'vi-VN' => 'vi',
        'en-US' => 'en',
        'th'    => 'th',
    ];

    const GAME_TYPE = [
        'B' => 'Player Bet',
        'W' => 'Player Won',
        'L' => 'Cancel Bet',
        'R' => 'Refund Transaction',
        'J' => 'Jackpot Won',
        'P' => 'Win in promotional campaign',
    ];

    const GAME_STATUS = [
        'I' => 'In Progress',
        'C' => 'Completed',
    ];
    const GAME_TYPE_ROUND = [
        'R' => 'Game Round',
        'F' => 'Free Spin',
    ];

    public function checkResponse($response, $method, $data)
    {
        $result = get_response_body($response);
        $statusCode = $response->getStatusCode();
        $this->responseLog($method, $statusCode, $result);

        # 如果返回的不是json，使用原字符串
        if (!$resultArr = json_decode($result, true)) {
            $resultArr = $result;
        }

        if ($statusCode >= 300) {
            echo 'Error response: ' . $statusCode;
            error_response(500, 'request error.');
        } else {
            if ('pull' != $method) {
                if (isset($resultArr['error']) && $resultArr['error'] == 0) {
                    switch($method) {
                        case 'register':
                            return '';
                            break;
                        case 'login':
                            return $resultArr['gameURL'];
                            break;
                        case 'balance':
                            return $resultArr['balance'];
                            break;
                        case 'transfer':
                            return GamePlatformTransferDetailRepository::setWaiting($data['detail']);
                        case 'check':
                            return $this->setTransferStatus($resultArr, $data);
                            break;
                        case 'kick_out':
                            return true;
                            break;
                        default:
                            error_response(422, 'unknown error.');
                    }
                } else {
                    error_response(422, 'error code: ' . $resultArr['error']);

                }
            } else {
                return $resultArr;
            }
        }
    }

    public function transferBetDetail($originBetDetails)
    {
        $betDetails = [];

        $now = now();

        $gameCodes = $this->getUniqueColumn($originBetDetails, 'gameID');
        $games = Game::getByCodes($this->platform->code, $gameCodes);
        $userNames = $this->getUniqueColumn($originBetDetails, 'extPlayerID');
        $users = $this->getUsers($userNames, '_');

        foreach ($originBetDetails as $record) {

            if (!$game = $games->where('code', $record['gameID'])->first()) {
                continue;
            }

            if (!isset($users[$record['extPlayerID']])) {
                continue;
            }

            $user = $users[$record['extPlayerID']];

            $startDate = !empty($record['startDate']) ? Carbon::parse($record['startDate'])->addHours(8) : null;
            $endDate   = !empty($record['endDate']) ? Carbon::parse($record['endDate'])->addHours(8) : null;

            $betDetails[] = [
                'platform_code'         => $this->platform->code,
                'product_code'          => $game->product_code,
                'platform_currency'     => $record['currency'],
                'order_id'              => $record['playSessionID'],
                'game_code'             => $game->code,
                'game_type'             => $game->type,
                'game_name'             => $game->getEnName(),
                'user_id'               => $user->id,
                'user_name'             => $user->name,
                'issue'                 => $record['playSessionID'],
                'stake'                 => $record['bet'],
                'bet'                   => $record['bet'],
                'prize'                 => $record['win'],
                'profit'                => $record['win'] - $record['bet'],
                'bet_at'                => $startDate,
                'payout_at'             => $endDate,
                'finished_at'           => $endDate,
                'user_currency'         => $user->currency,
                'user_stake'            => $record['bet'],
                'user_bet'              => $record['bet'],
                'user_prize'            => $record['win'],
                'user_profit'           => $record['win'] - $record['bet'],
                'platform_profit'       => $record['bet'] - $record['win'],
                'after_balance'         => 0,
                'platform_status'       => 'C' == $record['status'] ? GameBetDetail::PLATFORM_STATUS_BET_SUCCESS : GameBetDetail::PLATFORM_STATUS_WAITING,
                'available_bet'         => $record['bet'],
                'available_profit'      => $record['bet'] - $record['win'],
                'bet_info'              => self::GAME_STATUS[$record['status']] . ' - '
                                           . self::GAME_TYPE_ROUND[$record['type']],
                'created_at'            => $now,
                'updated_at'            => $now,
            ];
        }

        if (!empty($betDetails)) {
            # 添加总的投注明细表
            batch_insert('game_bet_details', $betDetails, true);
        }

        return [
            'origin_total'   => count($originBetDetails),
            'transfer_total' => count($betDetails),
        ];
    }


    private function setTransferStatus($result, $data)
    {

        if (isset($result['transactionId']) && 'Success' == $result['status']) {
            GamePlatformTransferDetailRepository::setPlatformOrderNo($data['detail'], $result['transactionId']);
            return GamePlatformTransferDetailRepository::setSuccess($data['detail']);
        } else {
            return GamePlatformTransferDetailRepository::setFail($data['detail']);
        }
    }

    public function getLastTimePoint($type)
    {
        return ChangingConfig::findValue('pp_' . strtolower($type) . '_last_timepoint', '');
    }

    public function setLastTimePoint($type, $lastTimePoint)
    {
        return ChangingConfig::setValue('pp_' . strtolower($type) . '_last_timepoint', $lastTimePoint);
    }

    /**
     * 获取会员平台名称
     *
     * @param $gamePlatformUserName
     * @return UserRepository|\Illuminate\Database\Eloquent\Model|null|object
     */
    public function getUser($gamePlatformUserName)
    {
        $operationId = Config::findValue('operation_id');

        if (false === strpos($gamePlatformUserName, $operationId)) {
            return null;
        }

        $name = substr($gamePlatformUserName, strlen($operationId));

        return UserRepository::findByName($name);
    }

}
