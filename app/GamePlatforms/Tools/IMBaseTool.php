<?php
namespace App\GamePlatforms\Tools;

use Carbon\Carbon;
use App\Models\Game;
use App\Models\GameBetDetail;
use App\Models\User;
use App\Repositories\GamePlatformTransferDetailRepository;
use Illuminate\Support\Facades\Log;

class IMBaseTool extends Tool
{

    protected $oddsTypes = [
        'HK'        => 1,
        'INDO'      => 2,
        'EURO'      => 4,
        'MALAY'     => 5,
    ];

    protected $currencies = [
        'VND' => 'VND',
        'THB' => 'THB',
    ];

    protected $languages = [
        'zh-CN' => 'ZH-CN',
        'vi-VN' => 'VI',
        'en-US' => 'EN',
        'th'    => 'TH',
    ];

    public function transferBetDetail($data, $insertIndividually=false)
    {
        $betDetails  = [];
        $now = now();
        $totalRecords = ['VND' => 0, 'THB' => 0];
        
        foreach ($data as  $prefix => $reports) {

            $gameCodes = $this->getUniqueColumn($reports, 'GameId');
            $games = Game::getByCodes($this->platform->code, $gameCodes);
            $userNames = $this->getUniqueColumn($reports, 'PlayerId');
            $users = $this->getUsers($userNames);

            foreach ($reports as $record) {
                if (!$game = $games->where('code', $record['GameId'])->first()) {
                    continue;
                }
                if (!isset($users[$record['PlayerId']])) {
                    continue;
                }
                $user = $users[$record['PlayerId']];

                $totalRecords[$user->currency] = $totalRecords[$user->currency]  + 1;
                $availables = $this->getAvailableBetAndProfit($record);
                $price =  isset($record['PayoutAmount']) ? $record['PayoutAmount'] :  $record['StakeAmount'] + $record['WinLoss'];
                $betDetails[] = [
                    'platform_code'     => $this->platform->code,
                    'product_code'      => $game->product_code,
                    'order_id'          => $record['BetId'],
                    'game_code'         => $game->code,
                    'game_type'         => $game->type,
                    'game_name'         => $game->getEnName(),
                    'user_id'           => $user->id,
                    'user_name'         => $user->name,
                    'issue'             => '',
                    'bet_at'            => $this->formatDateTime($record['WagerCreationDateTime']),
                    'payout_at'         => $this->formatDateTime($this->getPayoutDate($record)),
                    'odds'              => $record['DetailItems'][0]['Odds'] . "\n" . $this->getLocalOddsType($record['OddsType']),
                    'platform_currency' => $record['Currency'],
                    'stake'             => $record['StakeAmount'],
                    'bet'               => $availables['bet'],
                    'profit'            => $record['WinLoss'],
                    'prize'             => $price,
                    'user_currency'     => $user->currency,
                    'user_stake'        => $record['StakeAmount'],
                    'user_bet'          => $availables['bet'],
                    'user_profit'       => $record['WinLoss'],
                    'user_prize'        => $price,
                    'platform_profit'   => -1 * $record['WinLoss'],
                    'platform_status'   => $this->getPlatformStatus($record),
                    'available_bet'     => $availables['bet'],
                    'available_profit'  => -1 * $availables['profit'],
                    'bet_info'          => $this->getBetInfo($record),
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];

                if(true == $insertIndividually) {
                    batch_insert('game_bet_details', $betDetails, true);
                    $betDetails  = [];
                }

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

    /**
     * 解析回复
     *
     * @param $response
     * @param $method
     * @return mixed|\SimpleXMLElement|string
     * @throws
     */
    public function checkResponse($response, $method, $data, $wallet=false)
    {
        $result = get_response_body($response, 'json');
        $statusCode = $response->getStatusCode();
        $this->responseLog($method, $statusCode, $result);
        if ($statusCode >= 300) {
            error_response(500, 'request error.');
        } else {
            switch ($result['Code']) {

                case "0":
                    if ('register' == $method) {
                        return '';
                    } elseif ('login' == $method) {
                        return $result['GameUrl'];
                    } elseif ('balance' == $method) {
                        if(!empty($result['Currency']) && 'vnd' == strtolower($result['Currency']) && '102' == $wallet) {
                            return (float)$result['Balance'] / 1000;
                        }
                        return (float)$result['Balance'];
                    } elseif ('pull' == $method) {
                        return $result;
                    } elseif ('transfer' == $method || 'check' == $method && 'Approved' == $result['Status']) {
                        return GamePlatformTransferDetailRepository::setSuccess($data['detail']);
                    }
                    break;
                case 501:
                    if ('transfer' == $method || 'check' == $method) {
                        return GamePlatformTransferDetailRepository::setFail($data['detail'], $this->getError($result['Code']));
                    }
                    error_response(500, 'request error. Unauthorized access.');
                    break;
                #processing
                case 558:
                    if ('pull' == $method) {
                        return [];
                    }
                    break;
                case 517:
                case 520:
                case 998:
                    if ('check' == $method) {
                        return GamePlatformTransferDetailRepository::setWaiting($data['detail'], $this->getError($result['Code']));
                    }
                    if ('transfer' == $method) {
                        return GamePlatformTransferDetailRepository::setFail($data['detail'], $this->getError($result['Code']));
                    }
                    break;
                default:
                    if ('transfer' == $method || 'check' == $method) {
                        return GamePlatformTransferDetailRepository::setFail($data['detail'], $this->getError($result['Code']));
                    }
                    error_response($result['Code'], $this->getError($result['Code']));
                    break;
            }
        }
    }

    # 获取投注信息
    public function getBetInfo($record)
    {
        $betInfo = '';
        if(isset($record['DetailItems'])) {
            $betItems = $record['DetailItems'];
            foreach($betItems as $betItem) {
                $betInfo = $this->getSingleBetInfo($betItem, $betInfo);
            }
        }
        return $betInfo;
    }

    public function getSingleBetInfo($dataItem, $betInfo)
    {
        $betInfo .= isset($dataItem['SportsName'])? $dataItem['SportsName']: '' . ' - ' . $dataItem['BetTypeDesc'] . "\n";
        $betInfo .= $dataItem["Selection"]. "\n";
        if ( !empty($dataItem['Handicap']) ) {
            $betInfo .= '(' . $dataItem['Handicap']  . ')';
        }
        $betInfo .= "\n";
        //Where did the user bet/bet team
        if (!empty($dataItem['HomeTeamFTScore']) && !empty($dataItem['AwayTeamFTScore'])) {
            $betInfo .= '[' . $dataItem['HomeTeamFTScore'] . '-' . $dataItem['AwayTeamFTScore'] . ']';
        }
        if (!empty($dataItem['Odds'])) {
            $betInfo .= "\n@ " . $dataItem['Odds'];
        }
        $betInfo .= "\n";
        if(!empty( $dataItem['HomeTeamName'])) {
            $betInfo .= $dataItem['HomeTeamName'] . "\n";
        }
        if(!empty( $dataItem['AwayTeamName'])) {
            $betInfo .= $dataItem['AwayTeamName'] . "\n";
        }
        if (!empty($dataItem['EventDateTime'])) {
            $betInfo .= $this->formatDateTime($dataItem['EventDateTime']) . "\n";
        }
        if (!empty($dataItem['CompetitionName'])) {
            $betInfo .=$dataItem['CompetitionName'] . "\n";
        }
        $betInfo .= "\n";

        return $betInfo;

    }

    # 获取有效投注
    public function getAvailableBetAndProfit($record)
    {
        $result = [
            'bet'    => 0,
            'profit' => 0,
        ];

        # we are getting only settled bets but just to make sure
        if ("1" != $record['IsSettled']) {
            return $result;
        }
        $oddsData =  $record['DetailItems'][0];
        $odds     = $oddsData['Odds'];
        $oddsType = $record['OddsType'];
        switch ($oddsType) {
            case 'AMERICAN': # 美国盘 -100 ~ -204
                if ($odds >= -204 && $odds <= -100) {
                    return $result;
                }
                break;
            case 'EURO': # 欧洲盘  1.1 ~ 1.49
                if ($odds >= 1.1 && $odds <= 1.49) {
                    return $result;
                }
                break;
            case 'HK': # 香港盘  0.1 ~ 0.49
                if ($odds >= 0.1 && $odds <= 0.49) {
                    return $result;
                }
                break;

            case 'INDO': # 印尼盘  -10 ~ -2.04
                if ($odds >= -10 && $odds <= -2.04) {
                    return $result;
                }
                break;
            case 'MALAY': # 马来盘  0.1 ~ 0.49
                if ($odds >= 0.1 && $odds <= 0.49) {
                    return $result;
                }
                break;
        }

        $result['bet']    =  isset($record['MemberExposure'])? $record['MemberExposure'] : $record['StakeAmount'];
        $result['profit'] = $record['WinLoss'];

        return $result;
    }

    public function getPlatformStatus($record)
    {
        if(1 == $record['IsSettled']){
           return  GameBetDetail::PLATFORM_STATUS_BET_SUCCESS;
        }
        if(1 == $record['IsCancelled']){
            return GameBetDetail::PLATFORM_STATUS_CANCEL;
        }
        return GameBetDetail::PLATFORM_STATUS_WAITING;

    }

    public function formatDateTime($dateTime){
        return str_replace(' +08:00', '', $dateTime);
    }

    public function getLocalOddsType($type)
    {
        $localOddsType = isset($this->oddsTypes[$type]) ? $this->oddsTypes[$type] : 3;
        return User::$odds[$localOddsType];
    }

    public function getPayoutDate($record)
    {
        if(isset($record['SettlementDateTime']) && !empty($record['SettlementDateTime'])) {
            return $record['SettlementDateTime'];
        }

        if(!empty($record['LastUpdatedDate'])) {
            return $record['LastUpdatedDate'];
        }

        $detailItems = $record['DetailItems'][0];
        return Carbon::parse($detailItems['EventDateTime'])->toDateTimeString();
    }

}
