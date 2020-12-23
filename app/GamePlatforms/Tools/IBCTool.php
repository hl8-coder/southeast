<?php
namespace App\GamePlatforms\Tools;

use App\Models\ChangingConfig;
use App\Models\Game;
use App\Models\GameBetDetail;
use App\Models\PlatformMessage;
use App\Models\User;
use App\Repositories\GamePlatformTransferDetailRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class IBCTool extends Tool
{
    public $platformClass;

    protected $currencies = [
        'VND' => '51',
        'THB' => '4',
    ];

    protected $languages = [
        'vi-VN' => 'vn',
        'en-US' => 'en',
        'th'    => 'th',
        'zh-CN' => 'cs',
    ];

    protected $errors = [
        '1'  => 'Failed during executed',
        '2'  => 'User Name Dupliate',
        '3'  => 'Extend user balance',
        '4'  => 'Odds Type format error',
        '5'  => 'Currency format error',
        '6'  => 'Vendor_Member ID Duplicate',
        '7'  => 'MinTransfer > MaxTransfer',
        '8'  => '[Win Limit Withdraw RED Alert] Member reach limit',
        '9'  => 'Invalidate vendor_id',
        '10' => 'System is under maintenance',
        '11' => 'System is under maintenance',
    ];

    protected $oddsTypes = [
        User::ODDS_CHINA        => 2,
        User::ODDS_INDONESIAN   => 4,
        User::ODDS_AMERICAN     => 5,
        User::ODDS_DECIMAL      => 3,
        User::ODDS_MALAY        => 1,
    ];

    public function getOddsType($type)
    {
        return isset($this->oddsTypes[$type]) ? $this->oddsTypes[$type] : 1;
    }

    public function getLocalOddsType($type)
    {
        $localOddsTypeMapping = array_flip($this->oddsTypes);
        $localOddsType = isset($localOddsTypeMapping[$type]) ? $localOddsTypeMapping[$type] : 1;
        return User::$odds[$localOddsType];
    }

    public function getVersionKey()
    {
        return ChangingConfig::findValue('ibc_last_version_key', 0);
    }

    public function checkResponse($response, $method, $data)
    {
        $result = get_response_body($response, 'json');
        $statusCode = $response->getStatusCode();
        $this->responseLog($method, $statusCode, $result);

        if ($statusCode >= 300) {
            error_response(500, 'request error.');
        } else {
            switch ($result['error_code']) {
                case 0:
                    if ('register' == $method) {
                        return '';
                    } elseif ('login' == $method) {
                        return $result['Data'];
                    } elseif ('balance' == $method) {
                        switch ($result['Data'][0]['error_code']) {
                            case 0:
                                return $result['Data'][0]['balance'];
                                break;
                            case 6: # 会员尚未转账过
                                return 0;
                                break;
                            case 2: # 厂商会员识别码为空|会员不存在
                            case 7: # 取得非Sportsbook用户余额错误
                            default:
                                error_response(422, 'fail.');
                                break;
                        }
                    } elseif ('transfer' == $method || 'check' == $method) {
                        $detail = $data['detail'];
                        switch ($result['Data']['status']) {
                            case 0:
                                GamePlatformTransferDetailRepository::setPlatformOrderNo($detail, $result['Data']['trans_id']);
                                return GamePlatformTransferDetailRepository::setSuccess($detail);
                                break;
                            case 1: # 系统错误
                                return GamePlatformTransferDetailRepository::setFail($detail, $this->getError($result['error_code']));
                                break;
                            case 2: # 未知状态
                            default:
                                if ('check' == $method) {
                                    return GamePlatformTransferDetailRepository::setWaitingAndAddCheckJob($detail);
                                } else {
                                    return GamePlatformTransferDetailRepository::setFail($detail, $this->getError($result['error_code']));
                                }
                                break;
                        }
                    } elseif ('pull' == $method) {
                        $schedule = $data['schedule'];
                        # 记录last_version_key
                        $schedule->update(['remarks'=>$result['Data']['last_version_key']]);
                        # 更新last_version_key
                        ChangingConfig::setValue('ibc_last_version_key', $result['Data']['last_version_key']);

                        return $this->getPullResponseData($result['Data']);
                    } elseif ('get_league_name' == $method) {
                        if ($league = collect($result['Data']['names'])->where('lang', 'en')->first()) {
                            return $league['name'];
                        }
                        return $result['Data']['names'][0]['name'];
                    } elseif ('get_team_name' == $method) {
                        if ($team = collect($result['Data']['names'])->where('lang', 'en')->first()) {
                            return $team['name'];
                        }
                        return $result['Data']['names'][0]['name'];
                    } elseif ('update' == $method) {
                        return true;
                    }
                    break;
                case 1: # 执行失败
                case 2: # 用户名已存在
                case 4: # 赔率类型格式错误
                case 5: # 币别格式错误
                case 6: # 厂商识别码重复
                case 7: # 最小转账金额>最大转账金额
                case 9: # 厂商识别码失效
                case 10: # 系统维护中
                case 11: # 系统维护中
                    if ('transfer' == $method || 'check' == $method) {
                        return GamePlatformTransferDetailRepository::setFail($data['detail'], $this->getError($result['error_code']));
                    } elseif ('update' == $method) {
                        return false;
                    }
                    error_response(422, $this->getError($result['error_code']));
                    break;

                default:
                    error_response(422, 'error.');
                    break;
            }
        }
    }

    public function transferBetDetail($originBetDetails)
    {
        $class               = "App\\GamePlatforms\\" . strtoupper($this->platform->code) . 'Platform';
        $this->platformClass = new $class([null, $this->platform]);
        $betDetails = [];
        $now = now();
        foreach ($originBetDetails as $key => $record) {

            if (in_array($record['sport_type'], [161, 164, 202])) {
                $gameCode = $record['sport_type'];
            } else {
                $gameCode = 'ibc';
            }

            if (!$game = Game::findByPlatformAndCode($this->platform->code, $gameCode)) {
                continue;
            }

            if (!$user = $this->getUser($record['vendor_member_id'])) {
                continue;
            }

            if (!isset(static::$platformMappingStatuses[strtolower($record['ticket_status'])])) {
                continue;
            }

            $availables = $this->getAvailableBetAndProfit($record);
            # 因为winlost_datetime只有GMT-4的日期，所以妥协处理为加一天
            $betDetails[$key] = [
                'platform_code'     => $this->platform->code,
                'product_code'      => $game->product_code,
                'order_id'          => $record['trans_id'],
                'game_code'         => $game->code,
                'game_type'         => $game->type,
                'game_name'         => $this->getSportTypeName($record['sport_type']),
                'user_id'           => $user->id,
                'user_name'         => $user->name,
                'issue'             => '',
                'bet_at'            => $this->transferGMT8($record['transaction_time']),
                'payout_at'         => !empty($record['settlement_time']) ? $this->transferGMT8($record['settlement_time']) : Carbon::parse($record['winlost_datetime'])->addDay()->toDateTimeString(),
                'odds'              => $record['odds'] . "\n" . $this->getLocalOddsType($record['odds_type']),
                'platform_currency' => $record['currency'],
                'stake'             => $record['stake'],
                'bet'               => $availables['bet'],
                'profit'            => $record['winlost_amount'],
                'prize'             => $record['stake'] + $record['winlost_amount'],
                'user_currency'     => $user->currency,
                'user_stake'        => $record['stake'],
                'user_bet'          => $availables['bet'],
                'user_profit'       => $record['winlost_amount'],
                'user_prize'        => $record['stake'] + $record['winlost_amount'],
                'after_balance'     => $record['after_amount'],
                'platform_profit'   => -1 * $record['winlost_amount'],
                'platform_status'   => static::$platformMappingStatuses[$record['ticket_status']],
                'available_bet'     => $availables['bet'],
                'available_profit'  => -1 * $availables['profit'],
                'bet_info'          => $this->getBetInfo($record),
                'created_at'        => $now,
                'updated_at'        => $now,
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

    public function transferGMT8($time)
    {
        return Carbon::parse($time)->addHours(12)->toDateTimeString();
    }

    public function getSportTypeName($code)
    {
        foreach (static::$sportTypeNames as $key => $types) {
            if (in_array($code, $types)) {
                return $key;
            }
        }

        return '';
    }

    /**
     * 获取拉取接口返回数据合并
     *
     * @param $data
     * @return array
     */
    public function getPullResponseData($data)
    {
        $keys = ['BetDetails', 'BetNumberDetails', 'BetVirtualSportDetails'];

        $result = [];
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                $result = array_merge($result, $data[$key]);
            }
        }

        return $result;
    }


    /**
     * 登录act对应参数
     *
     * @var array
     */
    public static $loginActMapping = [
        '43'  => 'esports',
        '161' => 'numbergame',
        '164' => 'numbergame',
        '180' => 'virtualsports',
        '181' => 'virtualsports',
        '182' => 'virtualsports',
        '183' => 'virtualsports',
        '184' => 'virtualsports',
        '185' => 'virtualsports',
        '186' => 'virtualsports',
        '190' => 'virtualsports',
        '191' => 'virtualsports',
        '192' => 'virtualsports',
        '193' => 'virtualsports',
        '194' => 'virtualsports',
        '195' => 'virtualsports',
        '196' => 'virtualsports',
        '199' => 'virtualsports',
    ];

    /**
     * 获取PC登录act参数
     *
     * @param $gameCode
     * @param $data
     * @return array
     */
    public function getLoginAct($gameCode, $data)
    {
        if ('202' == $gameCode) {
            $data['game'] = 'keno';
        }

        if ('ibc' != $gameCode && isset(static::$loginActMapping[$gameCode])) {
            $data['act'] = static::$loginActMapping[$gameCode];
        }

        return $data;
    }

    public function getLoginTypes($gameCode, $data)
    {

        if ('ibc' != $gameCode) {
            $data['types'] = $gameCode . ',0,t';
        }

        if ('202' == $gameCode) {
            $data['types'] = 'Keno';
        }

        return $data;
    }

    # 获取有效投注
    public function getAvailableBetAndProfit($record)
    {
        $result = [
            'bet'    => 0,
            'profit' => 0,
        ];

        # 注单状态
        if (in_array(strtolower($record['ticket_status']), ['void', 'running', 'draw', 'reject', 'refund', 'waiting'])) {
            return $result;
        }

        # 游戏类型 racing不算
        if (in_array($record['sport_type'], static::$sportTypeNames['Racing'])) {
            return $result;
        }

        # 盘口
        $odds = $record['odds'];
        switch ($record['odds_type']) {
            case 1: # 马来盘  0.1 ~ 0.49
                if ($odds >= 0.1 && $odds <= 0.49) {
                    return $result;
                }
                break;

            case 2: # 香港盘  0.1 ~ 0.49
                if ($odds >= 0.1 && $odds <= 0.49) {
                    return $result;
                }
                break;

            case 3: # 欧洲盘  1.1 ~ 1.49
                if ($odds >= 1.1 && $odds <= 1.49) {
                    return $result;
                }
                break;

            case 4: # 印尼盘  -10 ~ -2.04
                if ($odds >= -10 && $odds <= -2.04) {
                    return $result;
                }
                break;

            case 5: # 美国盘 -100 ~ -204
                if ($odds >= -204 && $odds <= -100) {
                    return $result;
                }
                break;
        }

        $result['bet']    = $record['stake'];
        $result['profit'] = $record['winlost_amount'];

        return $result;
    }

    # 获取投注信息
    public function getBetInfo($record)
    {
        $betInfo = '';
        try {
            if (!empty($record['sport_type'])) {
                $betInfo = $this->singleBetInfo($record, $betInfo);
            } else {
                $parlayType = isset($record['parlay_type']) ? $record['parlay_type'] : '';
                if (isset($record['isLucky'])) {
                    $betInfo .= $parlayType . ($record['isLucky'] == 'True' ? '(Lucky)' : '') . "\n";
                }

                if (isset($record['combo_type'])) {
                    $betInfo .= $record['combo_type'] . "\n";
                }

                foreach ($record['ParlayData'] as $parlay) {
                    $betInfo = $this->singleBetInfo($parlay, $betInfo);
                }
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
        }

        return $betInfo;
    }

    public function singleBetInfo($record, $betInfo)
    {
        $betInfo .= $this->getSportType($record['sport_type']);
        if (isset($record['game_no'])) {
            $betInfo .= '/' . $this->getGameNo($record['game_no']);
        }
        $betInfo .= '/' . $this->getBetType($record['bet_type']) . "\n";
        $betInfo .= $this->getBetTeam($record['bet_type'], $record['bet_team']) . "\n";
        if (!empty($record['hdp'])) {
            $betInfo .= $record['hdp'] . ' ';
        }

        if (!empty($record['home_score']) && !empty($record['away_score'])) {
            $betInfo .= '[' . $record['home_score'] . '-' . $record['away_score'] . ']';
        }

        if (!empty($record['last_ball_no'])) {
            $betInfo .= '[' . $record['last_ball_no'] . ']';
        }

        if (!empty($record['odds'])) {
            $betInfo .= "\n@ " . $record['odds'];
        }

        $betInfo .= "\n";

        # 主队
        if (!empty($record['home_id'])) {
            $betInfo .= $this->getTeamName($record['home_id'], $record['bet_type']) . "\n";
        }

        # 客队
        if (!empty($record['away_id'])) {
            $betInfo .= $this->getTeamName($record['away_id'], $record['bet_type']) . "\n";
        }

        if (!empty($record['match_datetime'])) {
            $betInfo .= $this->transferGMT8($record['match_datetime']) . "\n";
        }

        # 联赛
        if (!empty($record['league_id'])) {
            $betInfo .= $this->getLeagueName($record['league_id']) . "\n";
        }

        // 如果 bet_type=38代表多场串关,多一个字段.
        if (!empty($record['SingleParlayData']) && $record['bet_type'] == 38) {
            foreach ($record['SingleParlayData'] as $singleData) {
                if (!empty($singleData['selection_name']) && !empty($singleData['status'])) {
                    $betInfo .= $singleData['selection_name']."-".$singleData['status'];
                }
            }
        }

        # 结果
        if (!empty($record['ticket_status'])) {
            $betInfo .= $record['ticket_status'] . "\n";
        }
        $betInfo .= "\n";

        return $betInfo;
    }

    public function getSportType($type)
    {
        return isset(static::$sportTypes[$type]) ? static::$sportTypes[$type] : $type;
    }

    public function getGameNo($gameNo)
    {
        $gameNoSubStr = substr($gameNo, 1, 3);
        return isset(static::$gameNo[$gameNoSubStr]) ? static::$gameNo[$gameNoSubStr] : $gameNo;
    }

    public function getBetType($type)
    {
        return isset(static::$betTypes[$type]) ? static::$betTypes[$type]['name'] : $type;
    }

    # 获取投注队伍
    public function getBetTeam($type, $team)
    {
        $result = $team;
        $team = strtolower($team);
        if (isset(static::$betTypes[$type])) {
            # keno的betTeam单独的处理
            if ($type >=1501 && $type <= 1524) {
                $result = isset(static::$kenoBetTeam[$team]) ? static::$kenoBetTeam[$team] : $team;
            } elseif ($type >=4601 && $type <= 4604) {
                $result = isset(static::$virtualBetTeam[$team]) ? static::$virtualBetTeam[$team] : $team;
            } else {
                if (isset(static::$betTypes[$type]['info'][$team])) {
                    $result = static::$betTypes[$type]['info'][$team];
                }
                if (isset(static::$betTypes[$type]['is_team'])) {
                    $result = $this->getTeamName($team, $type);
                }
            }
        }

        return ucfirst($result);
    }

    public function getLeagueName($leagueId)
    {
        # 先查询本地有无name
        if (PlatformMessage::isExists($this->platform->code, PlatformMessage::TYPE_LEAGUE, $leagueId)) {
            return PlatformMessage::getValue($this->platform->code, PlatformMessage::TYPE_LEAGUE, $leagueId);
        } else {
            if ($name = $this->platformClass->getLeagueName($leagueId)) {
                PlatformMessage::setValue($this->platform->code, PlatformMessage::TYPE_LEAGUE, $leagueId, $name);
            }
            return $name;
        }
    }

    public function getTeamName($teamId, $betType)
    {
        # 先查询本地有无name
        if (PlatformMessage::isExists($this->platform->code, PlatformMessage::TYPE_TEAM, $teamId)) {
            return PlatformMessage::getValue($this->platform->code, PlatformMessage::TYPE_TEAM, $teamId);
        } else {
            if ($name = $this->platformClass->getTeamName($teamId, $betType)) {
                PlatformMessage::setValue($this->platform->code, PlatformMessage::TYPE_TEAM, $teamId, $name);
            }
            return $name;
        }
    }

    # 第三方状态mapping
    public static $platformMappingStatuses = [
        'half won'  => GameBetDetail::PLATFORM_STATUS_BET_SUCCESS,
        'half lose' => GameBetDetail::PLATFORM_STATUS_BET_SUCCESS,
        'won'       => GameBetDetail::PLATFORM_STATUS_BET_SUCCESS,
        'lose'      => GameBetDetail::PLATFORM_STATUS_BET_SUCCESS,
        'void'      => GameBetDetail::PLATFORM_STATUS_CANCEL,
        'running'   => GameBetDetail::PLATFORM_STATUS_WAITING,
        'draw'      => GameBetDetail::PLATFORM_STATUS_BET_SUCCESS,
        'reject'    => GameBetDetail::PLATFORM_STATUS_BET_FAIL,
        'refund'    => GameBetDetail::PLATFORM_STATUS_BET_SUCCESS,
        'waiting'   => GameBetDetail::PLATFORM_STATUS_WAITING,
    ];

    public static $sportTypeNames = [
        'SportBooks' => [
            1, 2, 3, 4, 5, 6, 7, 8, 9,
            10, 11, 12, 13, 14, 15, 16, 17, 18, 19,
            20, 21, 22, 23, 24, 25, 26, 28, 29,
            30, 31, 32, 33, 34, 35, 36, 37, 38, 39,
            40, 41, 42, 43, 44, 45, 47, 48, 49,
            50, 51, 52, 99, 204,
        ],
        'Racing' => [
            154,
        ],
        'Number_Game' => [
            161, 164,
        ],
        'Live_Casino' => [
            162, 211,
        ],
        'Virtual_Sport' => [
            180, 181, 182, 183, 184, 185, 186,
        ],
        'KENO' => [
            202, 220,
        ],
        'RNG_Casino' => [
            251, 248,
        ],
        'RNG' => [
            208, 209, 210, 219,
        ],
        'Thirdparty_Game' => [
            212,
        ],
        'Virtual_Sports_2' => [
            190, 191, 192, 193, 194, 195, 196, 199,
        ],
    ];

    public static $sportTypes = [
        '1'  => 'Soccer',
        '2'  => 'Basketball',
        '3'  => 'Football',
        '4'  => 'Ice Hockey',
        '5'  => 'Tennis',
        '6'  => 'Volleyball',
        '7'  => 'Billiards',
        '8'  => 'Baseball',
        '9'  => 'Badminton',
        '10' => 'Golf',
        '11' => 'Motorsports',
        '12' => 'Swimming',
        '13' => 'Politics',
        '14' => 'Water Polo',
        '15' => 'Diving',
        '16' => 'Boxing',
        '17' => 'Archery',
        '18' => 'Table Tennis',
        '19' => 'Weightlifting',
        '20' => 'Canoeing',
        '21' => 'Gymnastics',
        '22' => 'Athletics',
        '23' => 'Equestrian',
        '24' => 'Handball',
        '25' => 'Darts',
        '26' => 'Rugby',
        '28' => 'Field Hockey',
        '29' => 'Winter Sport',
        '30' => 'Squash',
        '31' => 'Entertainment',
        '32' => 'Net Ball',
        '33' => 'Cycling',
        '34' => 'Fencing',
        '35' => 'Judo',
        '36' => 'M. Pentathlon',
        '37' => 'Rowing',
        '38' => 'Sailing',
        '39' => 'Shooting',
        '40' => 'Taekwondo',
        '41' => 'Triathlon',
        '42' => 'Wrestling',
        '43' => 'E Sports',
        '44' => 'Muay Thai',
        '45' => 'Beach Volleybal',
        '47' => 'Kabaddi',
        '48' => 'Sepak Takraw',
        '49' => 'Futsal',
        '50' => 'Cricket',
        '51' => 'Beach Soccer',
        '52' => 'Poker',
        '99' => 'Other Sports',
        '154' => 'HorseRacing FixedOdds',
        '161' => 'Number Game',
        '162' => 'Live Casino',
        '180' => 'Virtual Soccer',
        '181' => 'Virtual Horse Racing',
        '182' => 'Virtual Greyhound',
        '183' => 'Virtual Speedway',
        '184' => 'Virtual F1',
        '185' => 'Virtual Cycling',
        '186' => 'Virtual Tennis',
        '202' => 'RNG Keno',
        '251' => 'Casino',
        '208' => 'RNG Game',
        '209' => 'Mini Game',
        '210' => 'Mobile',
        '204' => 'Colossus Bet',
        '219' => 'Fishing World',
        '220' => 'Keno',
        '211' => 'Allbet',
        '212' => 'Macau Games',
        '190' => 'Virtual Soccer League',
        '191' => 'Virtual Soccer Nation',
        '192' => 'Virtual Soccer World Cup',
        '193' => 'Virtual Basketball',
        '194' => 'Virtual Soccer Asian Cup',
        '195' => 'Virtual Soccer English Premier',
        '196' => 'Virtual Soccer Champions Cup',
        '199' => 'Virtual Sports Parlay',
        '248' => 'Pragmatic Play',
        '164' => 'Happy 5 Number Game',
        '165' => 'Card Club',
        '245' => 'Virtual Games',
    ];

    public static $kenoBetTeam = [
        '1'  => 'Big',
        '2'  => 'Small',
        '3'  => 'Odd',
        '4'  => 'Even',
        '5'  => 'Up',
        '6'  => 'Tie',
        '7'  => 'Down',
        '11' => 'Big Odd',
        '12' => 'Big Even',
        '13' => 'Small Odd',
        '14' => 'Small Even',
        '15' => 'Gold',
        '16' => 'Wood',
        '17' => 'Water',
        '18' => 'Fire',
        '19' => 'Earth',
        '22' => 'Dragon',
        '23' => 'DT-Tie',
        '24' => 'Tiger',
        '33' => 'Index Big',
        '34' => 'Index Small',
        '38' => 'Big (Keno War / Keno War 2)',
        '39' => 'Small (Keno War / Keno War 2)',
        '40' => 'Odd (Keno War / Keno War 2)',
        '41' => 'Even (Keno War / Keno War 2)',
        '42' => 'Red (Keno War / Keno War 2)',
        '43' => 'Orange (Keno War / Keno War 2)',
        '44' => 'Yellow (Keno War / Keno War 2)',
        '45' => 'Aqua (Keno War / Keno War 2)',
        '46' => 'Green (Keno War / Keno War 2)',
        '47' => 'Blue (Keno War / Keno War 2)',
        '48' => 'Purple (Keno War / Keno War 2)',
        '49' => 'Big (Keno War / Keno War 2)',
        '50' => 'Small (Keno War / Keno War 2)',
        '51' => 'Big (Keno War / Keno War 2)',
        '52' => 'Small (Keno War / Keno War 2)',
        '53' => 'Player (Keno War / Keno War 2)',
        '54' => 'Banker (Keno War / Keno War 2)',
        '55' => 'Tie (Keno War / Keno War 2)',
    ];

    public static $virtualBetTeam = [
        '1'  => 'BANKER (Baccarat)',
        '2'  => 'PLAYER (Baccarat)',
        '3'  => 'TIE (Baccarat)',
        '4'  => 'BANKER PAIR (Baccarat)',
        '5'  => 'PLAYER PAIR (Baccarat)',
        '6'  => 'BIG (SicBo)',
        '7'  => 'SMALL (SicBo)',
        '8'  => 'ODD (SicBo)',
        '9'  => 'EVEN (SicBo)',
        '10' => 'ANY TRIPLE (SicBo)',
        '11' => 'Single (Fish) (Fish Prawn Crab)',
        '12' => 'Single (Prawn) (Fish Prawn Crab)',
        '13' => 'Single (Crab) (Fish Prawn Crab)',
        '14' => 'Single (Coin) (Fish Prawn Crab)',
        '15' => 'Single (Gourd) (Fish Prawn Crab)',
        '16' => 'Single (Rooster) (Fish Prawn Crab)',
        '17' => 'Any Double (Fish Prawn Crab)',
        '18' => 'Any Triple (Fish Prawn Crab)',
        '19' => 'ODD (Xoc Dia)',
        '20' => 'EVEN (Xoc Dia)',
        '21' => '4 WHITE (Xoc Dia)',
        '22' => '4 RED (Xoc Dia)',
        '23' => '2 WHITE 2 RED (Xoc Dia)',
        '24' => '3 WHITE 1 RED (Xoc Dia)',
        '25' => '1WHITE 3 RED (Xoc Dia)',
    ];

    public static $gameNo = [
        'B01' => 'Speed Baccarat',
        'B02' => 'Speed Baccarat 2',
        'B03' => 'Baccarat',
        'B04' => 'Baccarat 2',
        'X01' => 'Speed Xoc Dia',
        'X02' => 'Xoc Dia',
        'S01' => 'Speed Sic Bo',
        'S02' => 'Sic Bo',
        'F01' => 'Speed Fish Prawn Crab',
        'F02' => 'Fish Prawn Crab',
    ];

    public static $betTypes = [
        '1' => [
            'name' => 'Handicap',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '2' => [
            'name' => 'Odd/Even',
            'info' => [
                'h' => 'odd',
                'a' => 'even',
            ],
        ],
        '3' => [
            'name' => 'Over/Under',
            'info' => [
                'h' => 'over',
                'a' => 'under',
            ],
        ],
        '4' => [
            'name' => 'Correct Score',
            'info' => [],
        ],
        '5' => [
            'name' => 'FT.1X2',
            'info' => [
                '1' => 'home',
                'x' => 'even',
                '2' => 'away',
            ],
        ],
        '6' => [
            'name' => 'Total Goal',
            'info' => [],
        ],
        '7' => [
            'name' => '1st Handicap',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '8' => [
            'name' => '1st Over/Under',
            'info' => [
                'h' => 'over',
                'a' => 'under',
            ],
        ],
        '10' => [
            'name' => 'Outright',
            'info' => [],
            'is_team' => true,
        ],
        '11' => [
            'name' => 'Total Corners',
            'info' => [],
        ],
        '12' => [
            'name' => '1st Odds/Even',
            'info' => [
                'h' => 'odd',
                'a' => 'even',
            ],
        ],
        '13' => [
            'name' => 'Clean Sheet',
            'info' => [
                'hy' => 'home yes',
                'hn' => 'home no',
                'ay' => 'away yes',
                'an' => 'away no',
            ],
        ],
        '14' => [
            'name' => 'First Goal/Last Goal',
            'info' => [],
        ],
        '15' => [
            'name' => '1st 1X2',
            'info' => [
                '1' => 'home',
                'x' => 'even',
                '2' => 'away',
            ],
        ],
        '16' => [
            'name' => 'HT/FT',
            'info' => [
            ],
        ],
        '17' => [
            'name' => '2nd HDP',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '18' => [
            'name' => '2nd Over/Under',
            'info' => [
                'o' => 'order',
                'u' => 'under',
            ],
        ],
        '19' => [
            'name' => 'Substitutes',
            'info' => [
            ],
        ],
        '20' => [
            'name' => 'Money Line',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '21' => [
            'name' => '1st Money Line',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '22' => [
            'name' => 'Next Goal',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '23' => [
            'name' => 'Next Corner',
            'info' => [
            ],
        ],
        '24' => [
            'name' => 'Double Chance',
            'info' => [
                '1x' => 'home or draw',
                '12' => 'home or away',
                '2x' => 'away or draw',
            ],
        ],
        '25' => [
            'name' => 'Draw No Bet',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '26' => [
            'name' => 'Both/One/Neither team to score',
            'info' => [
                'o' => 'One',
                'n' => 'No Goal',
                'b' => 'Both',
            ],
        ],
        '27' => [
            'name' => 'To win to nil',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '28' => [
            'name' => '3-way handicap',
            'info' => [
                '1' => 'home',
                'x' => 'draw',
                '2' => 'away',
            ],
        ],
        '29' => [
            'name' => 'Combo Parlay',
            'info' => [
            ],
        ],
        '30' => [
            'name' => '1st Correct Score',
            'info' => [
            ],
        ],
        '31' => [
            'name' => 'Win (Horse Racing)',
            'info' => [
            ],
            'is_team' => true,
        ],
        '32' => [
            'name' => 'Place (Horse Racing)',
            'info' => [
            ],
            'is_team' => true,
        ],
        '33' => [
            'name' => 'Win/Place (Horse Racing)',
            'info' => [
            ],
            'is_team' => true,
        ],
        '38' => [
            'name' => 'Single Match Parlay',
            'info' => [
            ],
        ],
        '41' => [
            'name' => 'Win. UK Tote (Horse Racing)',
            'info' => [
            ],
            'is_team' => true,
        ],
        '42' => [
            'name' => 'Place. UK Tote (Horse Racing)',
            'info' => [
            ],
            'is_team' => true,
        ],
        '43' => [
            'name' => 'Win/Place. UK Tote (Horse Racing)',
            'info' => [
            ],
            'is_team' => true,
        ],
        '81' => [
            'name' => '1st ball O/U (Number Game)',
            'info' => [
                'h' => 'over',
                'a' => 'under',
            ],
        ],
        '82' => [
            'name' => 'Last ball O/U (Number Game)',
            'info' => [
                'h' => 'over',
                'a' => 'under',
            ],
        ],
        '83' => [
            'name' => '1st ball O/E (Number Game)',
            'info' => [
                'h' => 'over',
                'a' => 'under',
            ],
        ],
        '84' => [
            'name' => 'Last ball O/E (Number Game)',
            'info' => [
                'h' => 'odd',
                'a' => 'even',
            ],
        ],
        '85' => [
            'name' => 'Over/Under (Number Game)',
            'info' => [
                'h' => 'over',
                'a' => 'under',
            ],
        ],
        '86' => [
            'name' => 'Odd/Even (Number Game)',
            'info' => [
                'h' => 'odd',
                'a' => 'even',
            ],
        ],
        '87' => [
            'name' => 'Odd/Even (Number Game)',
            'info' => [
                'h' => 'odd',
                'a' => 'even',
            ],
        ],
        '88' => [
            'name' => 'Warrior (Number Game)',
            'info' => [
                'h' => '2nd',
                'a' => '3rd',
            ],
        ],
        '89' => [
            'name' => 'Next Combo (Number Game)',
            'info' => [
                '1:1' => 'over and odd',
                '1:2' => 'over and even',
                '2:1' => 'under and odd',
                '2:2' => 'under and even',
            ],
        ],
        '90' => [
            'name' => 'Number wheel (Number Game)',
            'info' => [
                '1-1'  => '1~5',
                '1-2'  => '6~10',
                '1-3'  => '11~15',
                '1-4'  => '16~20',
                '1-5'  => '21~25',
                '1-6'  => '26~30',
                '1-7'  => '31~35',
                '1-8'  => '36~40',
                '1-9'  => '41~45',
                '1-10' => '46~50',
                '1-11' => '51~55',
                '1-12' => '56~60',
                '1-13' => '61~65',
                '1-14' => '66~70',
                '1-15' => '71~75',
                '2-1'  => '1~15',
                '2-2'  => '16~30',
                '2-3'  => '31~45',
                '2-4'  => '46~60',
                '2-5'  => '61~75',
                '3-1'  => '1~25',
                '3-2'  => '26~50',
                '3-3'  => '51~75',
                '4-1'  => '1,6,11,16,21,26,31,36,41,46,51,56,61,66,71',
                '4-2'  => '2,7,12,17,22,27,32,37,42,47,52,57,62,67,72',
                '4-3'  => '3,8,13,18,23,28,33,38,43,48,53,58,63,68,73',
                '4-4'  => '4,9,14,19,24,29,34,39,44,49,54,59,64,69,74',
                '4-5'  => '5,10,15,20,25,30,35,40,45,50,55,60,65,70,75',
                '5-1'  => '1',
                '5-2'  => '2',
                '5-3'  => '3',
                '5-4'  => '4',
                '5-5'  => '5',
                '5-6'  => '6',
                '5-7'  => '7',
                '5-8'  => '8',
                '5-9'  => '9',
                '5-10'  => '10',
                '5-11'  => '11',
                '5-12'  => '12',
                '5-13'  => '13',
                '5-14'  => '14',
                '5-15'  => '15',
                '5-16'  => '16',
                '5-17'  => '17',
                '5-18'  => '18',
                '5-19'  => '19',
                '5-20'  => '20',
                '5-21'  => '21',
                '5-22'  => '22',
                '5-23'  => '23',
                '5-24'  => '24',
                '5-25'  => '25',
                '5-26'  => '26',
                '5-27'  => '27',
                '5-28'  => '28',
                '5-29'  => '29',
                '5-30'  => '30',
                '5-31'  => '31',
                '5-32'  => '32',
                '5-33'  => '33',
                '5-34'  => '34',
                '5-35'  => '35',
                '5-36'  => '36',
                '5-37'  => '37',
                '5-38'  => '38',
                '5-39'  => '39',
                '5-40'  => '40',
                '5-41'  => '41',
                '5-42'  => '42',
                '5-43'  => '43',
                '5-44'  => '44',
                '5-45'  => '45',
                '5-46'  => '46',
                '5-47'  => '47',
                '5-48'  => '48',
                '5-49'  => '49',
                '5-50'  => '50',
                '5-51'  => '51',
                '5-52'  => '52',
                '5-53'  => '53',
                '5-54'  => '54',
                '5-55'  => '55',
                '5-56'  => '56',
                '5-57'  => '57',
                '5-58'  => '58',
                '5-59'  => '59',
                '5-60'  => '60',
                '5-61'  => '61',
                '5-62'  => '62',
                '5-63'  => '63',
                '5-64'  => '64',
                '5-65'  => '65',
                '5-66'  => '66',
                '5-67'  => '67',
                '5-68'  => '68',
                '5-69'  => '69',
                '5-70'  => '70',
                '5-71'  => '71',
                '5-72'  => '72',
                '5-73'  => '73',
                '5-74'  => '74',
                '5-75'  => '75',
            ],
        ],
        '121' => [
            'name' => 'Home No Bet',
            'info' => [
                'x' => 'draw',
                'a' => 'away',
            ],
        ],
        '122' => [
            'name' => 'Away No Bet',
            'info' => [
                'h' => 'home',
                'x' => 'draw',
            ],
        ],
        '123' => [
            'name' => 'Draw / No Draw',
            'info' => [
                'h' => 'draw',
                'a' => 'no draw',
            ],
        ],
        '124' => [
            'name' => 'FT.1X2 HDP',
            'info' => [
                '1' => 'home',
                'x' => 'even',
                '2' => 'away',
            ],
        ],
        '125' => [
            'name' => '1st 1X2 HDP',
            'info' => [
                '1' => 'home',
                'x' => 'even',
                '2' => 'away',
            ],
        ],
        '126' => [
            'name' => '1H Total Goal',
            'info' => [
            ],
        ],
        '127' => [
            'name' => '1H First Goal/Last Goal',
            'info' => [
                '1:1' => 'Home First Goal',
                '1:2' => 'Home Last Goal',
                '2:1' => 'Away First Goal',
                '2:2' => 'Away Last Goal',
                '0:0' => 'No Goals',
            ],
        ],
        '128' => [
            'name' => 'HT/FT Odd/Even',
            'info' => [
                'oo' => 'Half Time Odd, Full Time Odd',
                'oe' => 'Half Time Odd, Full Time Even',
                'eo' => 'Half Time Even, Full Time Odd',
                'ee' => 'Half Time Even, Full Time Even',
            ],
        ],
        '133' => [
            'name' => 'Home To Win Both Halves',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '438' => [
            'name' => 'Home To Win Both Halves',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '134' => [
            'name' => 'Away To Win Both Halves',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '439' => [
            'name' => 'Away To Win Both Halves',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '135' => [
            'name' => 'APenalty Shootout',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '140' => [
            'name' => 'Highest Scoring Half',
            'info' => [
                '1h' => 'First Half',
                '2h' => 'Second Half',
                'tie' => 'Tie',
            ],
        ],
        '442' => [
            'name' => 'Highest Scoring Half',
            'info' => [
                '1h' => 'First Half',
                '2h' => 'Second Half',
                'tie' => 'Tie',
            ],
        ],
        '141' => [
            'name' => 'Highest Scoring Half Home Team',
            'info' => [
                '1h' => 'First Half',
                '2h' => 'Second Half',
                'tie' => 'Tie',
            ],
        ],
        '443' => [
            'name' => 'Highest Scoring Half Home Team',
            'info' => [
                '1h' => 'First Half',
                '2h' => 'Second Half',
                'tie' => 'Tie',
            ],
        ],
        '142' => [
            'name' => 'Highest Scoring Half Away Team',
            'info' => [
                '1h' => 'First Half',
                '2h' => 'Second Half',
                'tie' => 'Tie',
            ],
        ],
        '444' => [
            'name' => 'Highest Scoring Half Away Team',
            'info' => [
                '1h' => 'First Half',
                '2h' => 'Second Half',
                'tie' => 'Tie',
            ],
        ],
        '145' => [
            'name' => 'Both Teams To Score',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '146' => [
            'name' => '2nd Half Both Teams To Score',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '433' => [
            'name' => '2nd Half Both Teams To Score',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '147' => [
            'name' => 'Home To Score In Both Halves',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '436' => [
            'name' => 'Home To Score In Both Halves',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '148' => [
            'name' => 'Away To Score In Both Halves',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '437' => [
            'name' => 'Away To Score In Both Halves',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '149' => [
            'name' => 'Home To Win Either Half',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '440' => [
            'name' => 'Home To Win Either Half',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '150' => [
            'name' => 'Away To Win Either Half',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '441' => [
            'name' => 'Away To Win Either Half',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '151' => [
            'name' => '1st Half Double Chance',
            'info' => [
                '1x' => 'home or draw',
                '2x' => 'away or draw',
                '12' => 'home or away',
            ],
        ],
        '410' => [
            'name' => '1st Half Double Chance',
            'info' => [
                '1x' => 'home or draw',
                '2x' => 'away or draw',
                '12' => 'home or away',
            ],
        ],
        '152' => [
            'name' => 'Half Time/Full Time Correct Score',
            'info' => [
            ],
        ],
        '416' => [
            'name' => 'Half Time/Full Time Correct Score',
            'info' => [
            ],
        ],
        '1201' => [
            'name' => 'Handicap (Virtual Soccer)',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '1203' => [
            'name' => 'Over/Under 2.5 Goals (Virtual Soccer)',
            'info' => [
                'h' => 'over',
                'a' => 'under',
            ],
        ],
        '1204' => [
            'name' => 'Correct Score (Virtual Soccer)',
            'info' => [
            ],
        ],
        '1205' => [
            'name' => '1X2 (Virtual Soccer)',
            'info' => [
                '1' => 'home',
                'x' => 'even',
                '2' => 'away',
            ],
        ],
        '1206' => [
            'name' => 'Total Goal (Virtual Soccer)',
            'info' => [
            ],
        ],
        '1220' => [
            'name' => 'Player Win (Virtual Tennis)',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '1224' => [
            'name' => 'Double Chance (Virtual Soccer)',
            'info' => [
                '1x' => 'home or draw',
                '12' => 'home or away',
                '2x' => 'away or draw',
            ],
        ],
        '1231' => [
            'name' => 'Win (Virtual Sport)',
            'info' => [
            ],
            'is_team' => true,
        ],
        '1232' => [
            'name' => 'Place (Virtual Sport)',
            'info' => [
            ],
            'is_team' => true,
        ],
        '1233' => [
            'name' => 'Win/Place (Virtual Sport)',
            'info' => [
            ],
            'is_team' => true,
        ],
        '1235' => [
            'name' => 'Score Bet (Virtual Tennis)',
            'info' => [
                '1' => 'Game-0',
                '2' => 'Game-15',
                '3' => 'Game-30',
                '4' => 'Game-40',
                '5' => '0-Game',
                '6' => '15-Game',
                '7' => '30-Game',
                '8' => '40-Game',
            ],
        ],
        '1236' => [
            'name' => 'Total Points (Virtual Tennis)',
            'info' => [
            ],
            'is_team' => true,
        ],
        '153' => [
            'name' => 'Game Handicap',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '154' => [
            'name' => 'Set x Winner',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '155' => [
            'name' => 'Set x Game Handicap',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '156' => [
            'name' => 'Set x Total Game O/U',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '1301' => [
            'name' => 'Match Winner',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '1302' => [
            'name' => 'Match Correct Score',
            'info' => [
                '20' => 'Home 2-0 Win',
                '21' => 'Home 2-1 Win',
                '02' => 'Away 0-2 Win',
                '12' => 'Away 1-2 Win',
                '30' => 'Home 3-0 Win',
                '31' => 'Home 3-1 Win',
                '32' => 'Home 3-2 Win',
                '03' => 'Away 0-3 Win',
                '13' => 'Away 1-3 Win',
                '23' => 'Away 2-3 Win',
            ],
        ],
        '1303' => [
            'name' => 'Set Handicap',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '1305' => [
            'name' => 'Match Total Games Odd/Even',
            'info' => [
                'h' => 'odd',
                'a' => 'even',
            ],
        ],
        '1306' => [
            'name' => 'Match Total Games Over/under',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '1308' => [
            'name' => 'Match Games Handicap',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '1311' => [
            'name' => 'Set x Winner',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '1312' => [
            'name' => 'Set x Total Games',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '1316' => [
            'name' => 'Set x Game Handicap',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '1317' => [
            'name' => 'Set x Correct Score',
            'info' => [
                '60' => 'Home 6-0',
                '61' => 'Home 6-1',
                '62' => 'Home 6-2',
                '63' => 'Home 6-3',
                '64' => 'Home 6-4',
                '75' => 'Home 7-5',
                '76' => 'Home 7-6',
                '06' => 'Away 0-6',
                '16' => 'Away 1-6',
                '26' => 'Away 2-6',
                '36' => 'Away 3-6',
                '46' => 'Away 4-6',
                '57' => 'Away 5-7',
                '67' => 'Away 6-7',
            ],
        ],
        '1318' => [
            'name' => 'Set x Total Game Odd/Even',
            'info' => [
                'o' => 'odd',
                'e' => 'even',
            ],
        ],
        '1324' => [
            'name' => 'Set x Game y Winner',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        # 快乐彩 start
        '1501' => [
            'name' => 'Australia',
            'info' => [
            ],
        ],
        '1502' => [
            'name' => 'Bejing',
            'info' => [
            ],
        ],
        '1503' => [
            'name' => 'Slovakia',
            'info' => [
            ],
        ],
        '1504' => [
            'name' => 'Canada',
            'info' => [
            ],
        ],
        '1505' => [
            'name' => 'West Canada',
            'info' => [
            ],
        ],
        '1506' => [
            'name' => 'Taiwan',
            'info' => [
            ],
        ],
        '1507' => [
            'name' => 'Massachusetts',
            'info' => [
            ],
        ],
        '1508' => [
            'name' => 'Ohio',
            'info' => [
            ],
        ],
        '1509' => [
            'name' => 'Malta',
            'info' => [
            ],
        ],
        '1511' => [
            'name' => 'Kentucky',
            'info' => [
            ],
        ],
        '1513' => [
            'name' => 'Michigan',
            'info' => [
            ],
        ],
        '1514' => [
            'name' => 'Keno',
            'info' => [
            ],
        ],
        '1515' => [
            'name' => 'Max Keno',
            'info' => [
            ],
        ],
        '1516' => [
            'name' => 'Max Keno2',
            'info' => [
            ],
        ],
        '1517' => [
            'name' => 'Mini Keno',
            'info' => [
            ],
        ],
        '1519' => [
            'name' => 'Spring',
            'info' => [
            ],
        ],
        '1520' => [
            'name' => 'Summer',
            'info' => [
            ],
        ],
        '1521' => [
            'name' => 'Autumn',
            'info' => [
            ],
        ],
        '1522' => [
            'name' => 'Winter',
            'info' => [
            ],
        ],
        '1524' => [
            'name' => 'Mini Keno 2',
            'info' => [
            ],
        ],
        # 快乐彩 end
        '157' => [
            'name' => 'Odd/Even',
            'info' => [
                'h' => 'odd',
                'a' => 'even'
            ],
        ],
        '159' => [
            'name' => 'Exact Total Goals',
            'info' => [
                'g0' => '0',
                'g1' => '1',
                'g2' => '2',
                'g3' => '3',
                'g4' => '4',
                'G5' => '5',
                'G6' => '6&Over',
            ],
        ],
        '406' => [
            'name' => 'Exact Total Goals',
            'info' => [
                'g0' => '0',
                'g1' => '1',
                'g2' => '2',
                'g3' => '3',
                'g4' => '4',
                'G5' => '5',
                'G6' => '6&Over',
            ],
        ],
        '160' => [
            'name' => 'Next Goal',
            'info' => [
                '1' => 'home',
                'x' => 'none',
                '2' => 'away',
            ],
        ],
        '161' => [
            'name' => 'Exact Home Team Goals',
            'info' => [
                'g0' => '0',
                'g1' => '1',
                'g2' => '2',
                'g3' => '3&Over',
            ],
        ],
        '407' => [
            'name' => 'Exact Home Team Goals',
            'info' => [
                'g0' => '0',
                'g1' => '1',
                'g2' => '2',
                'g3' => '3&Over',
            ],
        ],
        '162' => [
            'name' => 'Exact Away Team Goals',
            'info' => [
                'g0' => '0',
                'g1' => '1',
                'g2' => '2',
                'g3' => '3&Over',
            ],
        ],
        '409' => [
            'name' => 'Exact Away Team Goals',
            'info' => [
                'g0' => '0',
                'g1' => '1',
                'g2' => '2',
                'g3' => '3&Over',
            ],
        ],
        '163' => [
            'name' => 'Result/Total Goals',
            'info' => [
                'hu' => 'Home/Under',
                'ho' => 'Home/Over',
                'du' => 'Draw/Under',
                'do' => 'Draw/Over',
                'au' => 'Away/Under',
                'ao' => 'Away/Over',
            ],
        ],
        '144' => [
            'name' => 'Result/Total Goals',
            'info' => [
                'hu' => 'Home/Under',
                'ho' => 'Home/Over',
                'du' => 'Draw/Under',
                'do' => 'Draw/Over',
                'au' => 'Away/Under',
                'ao' => 'Away/Over',
            ],
        ],
        '164' => [
            'name' => 'Extra Time Next Goal',
            'info' => [
                '1' => 'home',
                'x' => 'none',
                '2' => 'away',
            ],
        ],
        '165' => [
            'name' => 'Extra Time 1H Correct Score',
            'info' => [
            ],
        ],
        '166' => [
            'name' => 'Extra Time Correct Score',
            'info' => [
            ],
        ],
        '167' => [
            'name' => 'Extra Time 1H 1X2',
            'info' => [
                '1' => 'Extra Time HT.1',
                'x' => 'Extra Time HT.X',
                '2' => 'Extra Time HT.2',
            ],
        ],
        '168' => [
            'name' => 'Who advances to next round',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '169' => [
            'name' => 'Next Goal Time',
            'info' => [
                '1-15'  => '1-15 Min',
                '16-30' => '16-30 Min',
                '31-45' => '31-45 Min',
                '46-60' => '46-60 Min',
                '61-75' => '61-75 Min',
                '76-90' => '76-90 Min',
                'none'  => 'none',
            ],
        ],
        '170' => [
            'name' => 'Teams To Score',
            'info' => [
                'h'     => 'home',
                'a'     => 'away',
                'both'  => 'both',
                'none'  => 'none',
            ],
        ],
        '171' => [
            'name' => 'Winning Margin',
            'info' => [
                'h1' => 'Home 1',
                'h2' => 'Home 2',
                'h3' => 'Home 3 up',
                'd'  => 'Draw',
                'a1' => 'Away 1',
                'a2' => 'Away 2',
                'a3' => 'Away 3',
                'ng' => 'No Goal',
            ],
        ],
        '408' => [
            'name' => 'Winning Margin',
            'info' => [
                'h1' => 'Home 1',
                'h2' => 'Home 2',
                'h3' => 'Home 3 up',
                'd'  => 'Draw',
                'a1' => 'Away 1',
                'a2' => 'Away 2',
                'a3' => 'Away 3',
                'ng' => 'No Goal',
            ],
        ],
        '172' => [
            'name' => 'Result And First Team To Score',
            'info' => [
                'hh' => 'Home/Home',
                'hd' => 'Home/Draw',
                'ha' => 'Home/Away',
                'ah' => 'Away/Home',
                'ad' => 'Away/Draw',
                'aa' => 'Away/Away',
                'no' => 'none',
            ],
        ],
        '415' => [
            'name' => 'Result And First Team To Score',
            'info' => [
                'hh' => 'Home/Home',
                'hd' => 'Home/Draw',
                'ha' => 'Home/Away',
                'ah' => 'Away/Home',
                'ad' => 'Away/Draw',
                'aa' => 'Away/Away',
                'no' => 'none',
            ],
        ],
        '173' => [
            'name' => 'Extra Time Yes/No',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '174' => [
            'name' => 'Extra Time and Goal',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '175' => [
            'name' => 'Extra Time and Goal',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '176' => [
            'name' => 'First Ten Minutes 1X2',
            'info' => [
                '1' => 'home',
                'x' => 'draw',
                '2' => 'away',
            ],
        ],
        '177' => [
            'name' => '2H 1x2',
            'info' => [
                '1' => '2H.1',
                'x' => '2H.X',
                '2' => '2H.2',
            ],
        ],
        '178' => [
            'name' => '2H Over/Under',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '179' => [
            'name' => 'Exact 1H Goals',
            'info' => [
                'g0' => '0 Goals',
                'g1' => '1 Goal',
                'g2' => '2 Goals',
                'g3' => '3 Goals',
                'g4' => '4&Over Goals',
            ],
        ],
        '180' => [
            'name' => '1H Next Goal',
            'info' => [
                '1' => 'home',
                'x' => 'none',
                '2' => 'away',
            ],
        ],
        '181' => [
            'name' => '1H Exact Home Team Goals',
            'info' => [
                'g0' => '0 Goals',
                'g1' => '1 Goal',
                'g2' => '2 Goals',
                'g3' => '3&Over Goals',
            ],
        ],
        '182' => [
            'name' => '1H Exact Away Team Goals',
            'info' => [
                'g0' => '0 Goals',
                'g1' => '1 Goal',
                'g2' => '2 Goals',
                'g3' => '3&Over Goals',
            ],
        ],
        '184' => [
            'name' => '2H Odd/Even',
            'info' => [
                'o' => 'odd',
                'e' => 'even',
            ],
        ],
        '428' => [
            'name' => '2H Odd/Even',
            'info' => [
                'o' => 'odd',
                'e' => 'even',
            ],
        ],
        '185' => [
            'name' => '2H Draw No Bet',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '432' => [
            'name' => '2H Draw No Bet',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '186' => [
            'name' => '2H Double Chance',
            'info' => [
                'hd' => 'Home/Draw',
                'ha' => 'Home/Away',
                'da' => 'Draw/Away',
            ],
        ],
        '431' => [
            'name' => '2H Double Chance',
            'info' => [
                'hd' => 'Home/Draw',
                'ha' => 'Home/Away',
                'da' => 'Draw/Away',
            ],
        ],
        '187' => [
            'name' => 'Exact 2H Goals',
            'info' => [
                'g0' => '0 Goals',
                'g1' => '1 Goal',
                'g2' => '2&Over Goals',
            ],
        ],
        '188' => [
            'name' => '1H Both Teams To Score',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '427' => [
            'name' => '1H Both Teams To Score',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '189' => [
            'name' => 'Both Halves Over 1.5 Yes/No',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '434' => [
            'name' => 'Both Halves Over 1.5 Yes/No',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '190' => [
            'name' => 'Both Halves Over 1.5 Yes/No',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '435' => [
            'name' => 'Both Halves Over 1.5 Yes/No',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '191' => [
            'name' => '1H Draw No Bet',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '411' => [
            'name' => '1H Draw No Bet',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '192' => [
            'name' => 'First Goal Time(10 min)',
            'info' => [
                '1-10' => '1-10 Min',
                '11-20' => '11-20 Min',
                '21-30' => '21-30 Min',
                '31-40' => '31-40 Min',
                '41-50' => '41-50 Min',
                '51-60' => '51-60 Min',
                '61-70' => '61-70 Min',
                '71-80' => '71-80 Min',
                '81-90' => '81-90 Min',
                'none'  => 'none',
            ],
        ],
        '193' => [
            'name' => 'First Goal Time(15 min)',
            'info' => [
                '1-15'  => '1-15 Min',
                '16-30' => '16-30 Min',
                '31-45' => '31-45 Min',
                '46-60' => '46-60 Min',
                '61-75' => '61-75 Min',
                '76-90' => '76-90 Min',
                'none'  => 'none',
            ],
        ],
        '194' => [
            'name' => 'Corners Odd/Even',
            'info' => [
                'o' => 'odd',
                'e' => 'even',
            ],
        ],
        '195' => [
            'name' => 'Home Team Exact Corners',
            'info' => [
            ],
        ],
        '196' => [
            'name' => 'Away Team Exact Corners',
            'info' => [
            ],
        ],
        '197' => [
            'name' => 'Home Team Total Corners Over/Under',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '198' => [
            'name' => 'Away Team Total Corners Over/Under',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '199' => [
            'name' => 'Total Corners',
            'info' => [
            ],
        ],
        '200' => [
            'name' => '1H Home Team Exact Corners',
            'info' => [
            ],
        ],
        '201' => [
            'name' => '1H Away Team Exact Corners',
            'info' => [
            ],
        ],
        '202' => [
            'name' => '1H Total Corners',
            'info' => [
            ],
        ],
        '203' => [
            'name' => '1H Corners Odd/Even',
            'info' => [
                'o' => 'odd',
                'e' => 'even',
            ],
        ],
        '204' => [
            'name' => '1H Home Corner Over/Under',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '205' => [
            'name' => '1H Away Corner Over/Under',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '206' => [
            'name' => 'First Corner',
            'info' => [
                'h' => 'home',
                'a' => 'away',
                'n' => 'none',
            ],
        ],
        '207' => [
            'name' => '1H First Corner',
            'info' => [
                'h' => 'home',
                'a' => 'away',
                'n' => 'none',
            ],
        ],
        '208' => [
            'name' => 'Last Corner',
            'info' => [
                'h' => 'home',
                'a' => 'away',
                'n' => 'none',
            ],
        ],
        '209' => [
            'name' => '1H Last Corner',
            'info' => [
                'h' => 'home',
                'a' => 'away',
                'n' => 'none',
            ],
        ],
        '210' => [
            'name' => 'Player Sent Off',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '211' => [
            'name' => '1H Player Sent off',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '212' => [
            'name' => 'Home Team Player Sent Off',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '213' => [
            'name' => '1H Home Team Player Sent Off',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '214' => [
            'name' => 'Away Team Player Sent Off',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '215' => [
            'name' => '1H Away Team Player Sent Off',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '221' => [
            'name' => 'Next 1 minute',
            'info' => [
                '2'   => 'goal yes',
                '-2'  => 'goal no',
                '4'   => 'corner no',
                '-4'  => 'corner no',
                '8'   => 'Free-Kict yes',
                '-8'  => 'Free-Kict no',
                '16'  => 'Goal-Kick yes',
                '-16' => 'Goal-Kickl no',
                '32'  => 'Throw-In yes',
                '-32' => 'Throw-In no',
            ],
        ],
        '222' => [
            'name' => 'Next 5 minute',
            'info' => [
                '2'   => 'goal yes',
                '-2'  => 'goal no',
                '4'   => 'corner no',
                '-4'  => 'corner no',
                '8'   => 'Free-Kict yes',
                '-8'  => 'Free-Kict no',
                '16'  => 'Goal-Kick yes',
                '-16' => 'Goal-Kickl no',
                '32'  => 'Throw-In yes',
                '-32' => 'Throw-In no',
                '128' => 'penalty',
            ],
        ],
        '223' => [
            'name' => 'What will happen first in next 1 minute',
            'info' => [
                '1'   => 'none',
                '2'   => 'goal yes',
                '4'   => 'corner yes',
                '8'   => 'Free-Kict',
                '16'  => 'Goal-Kick',
                '32'  => 'Throw-In',
            ],
        ],
        '224' => [
            'name' => 'What will happen first in next 5 minute',
            'info' => [
                '1'   => 'none',
                '2'   => 'goal yes',
                '4'   => 'corner yes',
                '8'   => 'Free-Kict',
                '64'  => 'booking',
                '128' => 'penalty',
            ],
        ],
        '225' => [
            'name' => 'Next 1 minute set piece',
            'info' => [
                '1'   => 'no',
                '44'  => 'yes',
            ],
        ],
        '501' => [
            'name' => 'Match Winner',
            'info' => [
                'a'  => 'away',
                'h'  => 'home',
            ],
        ],
        '401' => [
            'name' => 'Home Team OU',
            'info' => [
                'o'  => 'over',
                'u'  => 'under',
            ],
        ],
        '402' => [
            'name' => 'Away Team OU',
            'info' => [
                'o'  => 'over',
                'u'  => 'under',
            ],
        ],
        '403' => [
            'name' => '1H Home Team OU',
            'info' => [
                'o'  => 'over',
                'u'  => 'under',
            ],
        ],
        '404' => [
            'name' => '1H Away Team OU',
            'info' => [
                'o'  => 'over',
                'u'  => 'under',
            ],
        ],
        '405' => [
            'name' => '2nd Half Correct Score',
            'info' => [
            ],
        ],
        '412' => [
            'name' => 'Exact 1st Goal',
            'info' => [
            ],
        ],
        '413' => [
            'name' => 'FT Correct Score',
            'info' => [
            ],
        ],
        '414' => [
            'name' => '1H Correct Score',
            'info' => [
            ],
        ],
        '417' => [
            'name' => 'Both Teams To Score/Result',
            'info' => [
                'yh' => 'Yes/Home',
                'ya' => 'Yes/Away',
                'yd' => 'Yes/Draw',
                'nh' => 'No/Home',
                'na' => 'No/Away',
                'nd' => 'No/Draw',
             ],
        ],
        '418' => [
            'name' => 'Both Teams To Score/Total Goal',
            'info' => [
                'yo' => 'Yes&Over',
                'yu' => 'Yes&Under',
                'no' => 'No&Over',
                'nu' => 'No&Under',
            ],
        ],
        '419' => [
            'name' => 'Which Half First Goal',
            'info' => [
                '1h' =>  '1st Half',
                '2h' =>  '2nd Half',
                'n'  =>  'Neither',
            ],
        ],
        '420' => [
            'name' => 'Home Team Which Half First Goal',
            'info' => [
                '1h' =>  '1st Half',
                '2h' =>  '2nd Half',
                'n'  =>  'Neither',
            ],
        ],
        '421' => [
            'name' => 'Away Team Which Half First Goal',
            'info' => [
                '1h' =>  '1st Half',
                '2h' =>  '2nd Half',
                'n'  =>  'Neither',
            ],
        ],
        '422' => [
            'name' => 'First Team 2 Goals',
            'info' => [
                'h' => 'Home',
                'a' => 'Away',
                'n' => 'Neither',
            ],
        ],
        '423' => [
            'name' => 'First Team 3 Goals',
            'info' => [
                'h' => 'Home',
                'a' => 'Away',
                'n' => 'Neither',
            ],
        ],
        '424' => [
            'name' => 'First Goal Method',
            'info' => [
                's'  => 'Shot',
                'h'  => 'Header',
                'p'  => 'Penalty',
                'fk' => 'Free Kick',
                'og' => 'Own Goal',
                'ng' => 'No Goal',
            ],
        ],
        '425' => [
            'name' => 'To Win From Behind',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '426' => [
            'name' => '1h Winning Margin',
            'info' => [
                'h1'  => 'Home to win by 1 goal',
                'h2+' => 'Home to win by 2up goals',
                'd'   => 'Score Draw',
                'a1'  => 'Away to win by 1 goal',
                'a2+' => 'Away to win by 2up goals',
                'ng'  => 'No Goal',
            ],
        ],
        '429' => [
            'name' => 'Exact 2nd Half Goals',
            'info' => [
            ],
        ],
        '445' => [
            'name' => 'Both Team To Score in 1H/2H',
            'info' => [
                'yy' => 'Yes/Yes',
                'yn' => 'Yes/No',
                'ny' => 'No/Yes',
                'nn' => 'No/No',
            ],
        ],
        '446' => [
            'name' => 'Both Team To Score in 1H/2H',
            'info' => [
                'yy' => 'Yes/Yes',
                'yn' => 'Yes/No',
                'ny' => 'No/Yes',
                'nn' => 'No/No',
            ],
        ],
        '447' => [
            'name' => 'Both Team To Score in 1H/2H',
            'info' => [
                'yy' => 'Yes/Yes',
                'yn' => 'Yes/No',
                'ny' => 'No/Yes',
                'nn' => 'No/No',
            ],
        ],
        '601' => [
            'name' => 'FT Winning Margin 14 Way',
            'info' => [
                'h1-2'   => 'HomeTeam to Win by 1 to 2 points',
                'h3-6'   => 'HomeTeam to Win by 3 to 6 points',
                'h7-9'   => 'HomeTeam to Win by 7 to 9 points',
                'h10-13' => 'HomeTeam to Win by 10 to 13 points',
                'h14-16' => 'HomeTeam to Win by 14 to 16 points',
                'h17-20' => 'HomeTeam to Win by 17 to 20 points',
                'h21+'   => 'HomeTeam to Win by 21+ points',
                'a1-2'   => 'AwayTeam to Win by 1 to 2 points',
                'a3-6'   => 'AwayTeam to Win by 3 to 6 points',
                'a7-9'   => 'AwayTeam to Win by 7 to 9 points',
                'a10-13' => 'AwayTeam to Win by 10 to 13 points',
                'a14-16' => 'AwayTeam to Win by 14 to 16 points',
                'a17-20' => 'AwayTeam to Win by 17 to 20 points',
                'a21+'   => 'AwayTeam to Win by 21+ points',
            ],
        ],
        '602' => [
            'name' => 'FT Winning Margin 12 Way',
            'info' => [
                'h1-5'   => 'HomeTeam to Win by 1 to 5 points',
                'h6-10'  => 'HomeTeam to Win by 6 to 10 points',
                'h11-15' => 'HomeTeam to Win by 11 to 15 points',
                'h16-20' => 'HomeTeam to Win by 16 to 20 points',
                'h21-25' => 'HomeTeam to Win by 21 to 25 points',
                'h26+'   => 'HomeTeam to Win by 26+ points',
                'a1-5'   => 'AwayTeam to Win by 1 to 5 points',
                'a6-10'  => 'AwayTeam to Win by 6 to 10 points',
                'a11-15' => 'AwayTeam to Win by 11 to 15 points',
                'a16-20' => 'AwayTeam to Win by 16 to 20 points',
                'a21-25' => 'AwayTeam to Win by 21 to 25 points',
                'a26+'   => 'AwayTeam to Win by 26+ points',
            ],
        ],
        '603' => [
            'name' => 'FT Which team to score the highest quarter',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '604' => [
            'name' => 'FT Which team to score the first basket',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '605' => [
            'name' => 'FT Which team to score the last basket',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '606' => [
            'name' => '1H Race to X',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '607' => [
            'name' => '2H Race to X',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '608' => [
            'name' => '1H Winning Margin 12 Way',
            'info' => [
                'h1-5'   => 'HomeTeam to Win by 1 to 5 points',
                'h6-10'  => 'HomeTeam to Win by 6 to 10 points',
                'h11-15' => 'HomeTeam to Win by 11 to 15 points',
                'h16-20' => 'HomeTeam to Win by 16 to 20 points',
                'h21-25' => 'HomeTeam to Win by 21 to 25 points',
                'h26+'   => 'HomeTeam to Win by 26+ points',
                'd'      => 'Draw',
                'a1-5'   => 'AwayTeam to Win by 1 to 5 points',
                'a6-10'  => 'AwayTeamName to Win by 6 to 10 points',
                'a11-15' => 'AwayTeam to Win by 11 to 15 points',
                'a16-20' => 'AwayTeam to Win by 16 to 20 points',
                'a21-25' => 'AwayTeam to Win by 21 to 25 points',
                'a26+'   => 'wayTeam to Win by 26+ points',
            ],
        ],
        '609' => [
            'name' => 'Quarter X Handicap',
            'info' => [
                'h'   => 'home',
                'a'   => 'away',
            ],
        ],
        '610' => [
            'name' => 'Quarter X Over Under',
            'info' => [
                'o'   => 'over',
                'u'   => 'under',
            ],
        ],
        '611' => [
            'name' => 'Quarter X Odd Even',
            'info' => [
                'o'   => 'odd',
                'e'   => 'even',
            ],
        ],
        '612' => [
            'name' => 'Quarter X Moneyline',
            'info' => [
                'h'   => 'home',
                'a'   => 'away',
            ],
        ],
        '613' => [
            'name' => 'Quarter X Race To Y Points',
            'info' => [
                'h'   => 'home',
                'a'   => 'away',
            ],
        ],
        '614' => [
            'name' => 'Quarter X Winning Margin 7 Way',
            'info' => [
                'h1-4' => 'HomeTeam to Win by 1 to 4 points',
                'h5-8' => 'HomeTeam to Win by 5 to 8 points',
                'h9+'  => 'HomeTeam to Win by 9+ points',
                'd'    => 'Draw',
                'a1-4' => 'AwayTeam to Win by 1 to 4 points',
                'a5-8' => 'AwayTeam to Win by 5 to 8 points',
                'a9+'  => 'AwayTeam to Win by 9+ points',
            ],
        ],
        '615' => [
            'name' => 'Quarter X Home Team Over Under',
            'info' => [
                'o'   => 'over',
                'u'   => 'under',
            ],
        ],
        '616' => [
            'name' => 'Quarter X Away Team Over Under',
            'info' => [
                'o'   => 'over',
                'u'   => 'under',
            ],
        ],
        '617' => [
            'name' => 'Quarter X Which team to score the last basket',
            'info' => [
                'h'   => 'home',
                'a'   => 'away',
            ],
        ],
        '2701' => [
            'name' => 'FT.1X2',
            'info' => [
                '1' => 'home',
                'x' => 'draw',
                '2' => 'away',
            ],
        ],
        '2702' => [
            'name' => '1H 1X2',
            'info' => [
                '1' => 'home',
                'x' => 'draw',
                '2' => 'away',
            ],
        ],
        '2703' => [
            'name' => 'Over/Under',
            'info' => [
                'o'   => 'over',
                'u'   => 'under',
            ],
        ],
        '2704' => [
            'name' => '1H Over/Under',
            'info' => [
                'o'   => 'over',
                'u'   => 'under',
            ],
        ],
        '2705' => [
            'name' => 'Handicap',
            'info' => [
                'h'   => 'home',
                'a'   => 'away',
            ],
        ],
        '2706' => [
            'name' => '1H Handicap',
            'info' => [
                'h'   => 'home',
                'a'   => 'away',
            ],
        ],
        '2707' => [
            'name' => 'Correct Score',
            'info' => [
            ],
        ],
        '2709' => [
            'name' => 'Mix Parlay',
            'info' => [
            ],
        ],
        '1031' => [
            'name' => 'Max5D 60',
            'info' => [
            ],
        ],
        '1033' => [
            'name' => 'Max3D 60',
            'info' => [
            ],
        ],
        '1034' => [
            'name' => 'Max3D 90',
            'info' => [
            ],
        ],
        '1035' => [
            'name' => 'Max11x5 60',
            'info' => [
            ],
        ],
        '1036' => [
            'name' => 'Max11x5 90',
            'info' => [
            ],
        ],
        '1037' => [
            'name' => 'MaxDice 60',
            'info' => [
            ],
        ],
        '1038' => [
            'name' => 'MaxDice 90',
            'info' => [
            ],
        ],
        '1039' => [
            'name' => 'Max Racing',
            'info' => [
            ],
        ],
        '1040' => [
            'name' => 'Max Racing 2',
            'info' => [
            ],
        ],
        '1041' => [
            'name' => 'Penalty Shoot-out',
            'info' => [
            ],
        ],
        '1042' => [
            'name' => 'Penalty Shoot-out 2',
            'info' => [
            ],
        ],
        '1043' => [
            'name' => 'Se Die',
            'info' => [
            ],
        ],
        '1044' => [
            'name' => 'Se Die 2',
            'info' => [
            ],
        ],
        '1045' => [
            'name' => 'Lottery Bull',
            'info' => [
            ],
        ],
        '1046' => [
            'name' => 'Lottery Bull 2',
            'info' => [
            ],
        ],
        '1047' => [
            'name' => 'Max Se Die',
            'info' => [
            ],
        ],
        '1048' => [
            'name' => 'Max Se Die 2',
            'info' => [
            ],
        ],
        '461' => [
            'name' => 'Home Team OU (Dec)',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '462' => [
            'name' => 'Away Team OU (Dec)',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '463' => [
            'name' => '1H Home Team OU (Dec)',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '464' => [
            'name' => '1H Away Team OU (Dec)',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '448' => [
            'name' => 'Last Team To Score',
            'info' => [
                'h' => 'HomeTeamName',
                'a' => 'AwayTeamName',
                'ng'=> 'No Goal',
            ],
        ],
        '449' => [
            'name' => 'Double Chance & Total Goals',
            'info' => [
                '1xo' => 'Home/Draw & Over',
                '1xu' => 'Home/Draw & Under',
                '12o' => 'Home/Away & Over',
                '12u' => 'Home/Away & Under',
                '2xo' => 'Away/Draw & Over',
                '2xu' => 'Away/Draw & Under',
            ],
        ],
        '450' => [
            'name' => 'Odd/Even & Total Goals',
            'info' => [
                'oo' => 'Odd & Over',
                'ou' => 'Odd & Under',
                'eo' => 'Even & Over',
                'eu' => 'Even & Under',
            ],
        ],
        '451' => [
            'name' => 'Both Teams to Score / Double Chance',
            'info' => [
                'y1x' => 'Yes & Home/Draw',
                'y12' => 'Yes & Home/Away',
                'y2x' => 'Yes & Away/Draw',
                'n1x' => 'No & Home/Draw',
                'n12' => 'No & Home/Away',
                'n2x' => 'No & Away/Draw',
            ],
        ],
        '452' => [
            'name' => 'Highest Scoring Half (2 Way)',
            'info' => [
                '1h' => 'First Half',
                '2h' => 'Second Half',
            ],
        ],
        '453' => [
            'name' => '1H 3-Way Handicap',
            'info' => [
                '1' => 'home',
                'x' => 'draw',
                '2' => 'away',
            ],
        ],
        '454' => [
            'name' => 'Double Chance & First Team To Score',
            'info' => [
                '1xh' => 'Home/Draw & Home',
                '12h' => 'Home/Away & Home',
                '2xh' => 'Away/Draw & Home',
                '1xa' => 'Home/Draw & Away',
                '12a' => 'Home/Away & Away',
                '2xa' => 'Away/Draw & Away',
                'ng'  => 'No Goal',
            ],
        ],
        '455' => [
            'name' => 'Time of First Goal',
            'info' => [
                '1-10'  => '00:01-10:00',
                '11-20' => '10:01-20:00',
                '21-30' => '20:01-30:00',
                '31-40' => '30:01-40:00',
                '41-50' => '40:01-50:00',
                '51-60' => '50:01-60:00',
                '61-70' => '60:01-70:00',
                '71-80' => '70:01-80:00',
                '81-90' => '80:01-90:00',
                'ng'    => 'No Goal',
            ],
        ],
        '456' => [
            'name' => '1H Both Teams To Score / Result',
            'info' => [
                'yh' => 'Yes/Home',
                'ya' => 'Yes/Away',
                'yd' => 'Yes/Draw',
                'nh' => 'No/Home',
                'na' => 'No/Away',
                'nd' => 'No/Draw',
            ],
        ],
        '457' => [
            'name' => '1H Both Teams To Score / Total Goals',
            'info' => [
                'yo' => 'Yes & Over',
                'yu' => 'Yes & Under',
                'no' => 'No & Over',
                'nu' => 'No & Under',
            ],
        ],
        '458' => [
            'name' => 'Asian 1X2',
            'info' => [
                '1' => 'FT Asian.1',
                '2' => 'FT Asian.2',
                'x' => 'FT Asian.X',
            ],
        ],
        '459' => [
            'name' => '1H Asian 1X2',
            'info' => [
                '1' => 'HT Asian.1',
                '2' => 'HT Asian.2',
                'x' => 'HT Asian.X',
            ],
        ],
        '460' => [
            'name' => 'Which Team Will Win By 5+ Goals',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '143' => [
            'name' => '1H Result/Total Goals',
            'info' => [
                'ho' => 'Home/Over',
                'hu' => 'Home/Under',
                'do' => 'Draw/Over',
                'du' => 'Draw/Under',
                'ao' => 'Away/Over',
                'au' => 'Away/Under',
            ],
        ],
        '2801' => [
            'name' => 'Match Winner',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '2802' => [
            'name' => '1H Winner',
            'info' => [
                '1' => 'home',
                'x' => 'draw',
                '2' => 'away',
            ],
        ],
        '2803' => [
            'name' => 'Over/Under',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '2804' => [
            'name' => '1H Over/Under',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '2805' => [
            'name' => 'Handicap',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '2806' => [
            'name' => '1H Handicap',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '2807' => [
            'name' => 'Winning Margin 6 Way',
            'info' => [
                'h1-5'  => 'Home to Win by 1 - 5 points',
                'h6-10' => 'Home to Win by 6 - 10 points',
                'h11+'  => 'Home to Win by 11+ points',
                'a1-5'  => 'Away to Win by 1 - 5 points',
                'a6-10' => 'Away to Win by 6 - 10 points ',
                'a11+'  => 'Home to Win by 11+ points',
            ],
        ],
        '2808' => [
            'name' => '1H Winning Margin 7 Way',
            'info' => [
                'h1-5'  => 'Home to Win by 1 - 5 points',
                'h6-10' => 'Home to Win by 6 - 10 points',
                'h11+'  => 'Home to Win by 11+ points',
                'd'     => 'Draw',
                'a1-5'  => 'Away to Win by 1 - 5 points',
                'a6-10' => 'Away to Win by 6 - 10 points',
                'a11+'  => 'Home to Win by 11+ points',
            ],
        ],
        '2809' => [
            'name' => 'FT Race To X Points',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '2811' => [
            'name' => 'Home Team Over/Under',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '2812' => [
            'name' => 'Away Team Over/Under',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '9001' => [
            'name' => 'Map X Moneyline',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9002' => [
            'name' => 'Map X Total Kills Handicap',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9003' => [
            'name' => 'Map X Total Kills Over/Under',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '9004' => [
            'name' => 'Map X Total Kills Moneyline',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9005' => [
            'name' => 'Map X Total Kills Odd/Even',
            'info' => [
                'o' => 'odd',
                'e' => 'even',
            ],
        ],
        '9006' => [
            'name' => 'Map X First Blood',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9007' => [
            'name' => 'Map X First to Y Kills',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9008' => [
            'name' => 'Map X Total Towers Handicap',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9009' => [
            'name' => 'Map X Total Towers Over/Under',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '9010' => [
            'name' => 'Map X Total Towers Moneyline',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9011' => [
            'name' => 'Map X First Tier Y Tower',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9012' => [
            'name' => 'Map X Total Roshans Handicap',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9013' => [
            'name' => 'Map X Total Roshans Over/Under',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '9014' => [
            'name' => 'Map X Total Roshans Moneyline',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9015' => [
            'name' => 'Map X 1st Roshan',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9016' => [
            'name' => 'Map X 2nd Roshan',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9017' => [
            'name' => 'Map X 3rd Roshan',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9018' => [
            'name' => 'Map X Total Barracks Handicap',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9019' => [
            'name' => 'Map X Total Barracks Over/Under',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '9020' => [
            'name' => 'Map X Total Barracks Moneyline',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9021' => [
            'name' => 'Map X Barracks 1st Lane',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9022' => [
            'name' => 'Map X Barracks 2nd Lane',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9023' => [
            'name' => 'Map X Barracks 3rd Lane',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9024' => [
            'name' => 'Map X Total Turrets Handicap',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9025' => [
            'name' => 'Map X Total Turrets Over/Under',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '9026' => [
            'name' => 'Map X Total Turrets Moneyline',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9027' => [
            'name' => 'Map X First Tier Y Turret',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9028' => [
            'name' => 'Map X Total Dragons Handicap',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9029' => [
            'name' => 'Map X Total Dragons Over/Under',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '9030' => [
            'name' => 'Map X Total Dragons Moneyline ',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9031' => [
            'name' => 'Map X 1st Dragon',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9032' => [
            'name' => 'Map X 2nd Dragon',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9033' => [
            'name' => 'Map X 3rd Dragon',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9034' => [
            'name' => 'Map X Total Barons Handicap',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9035' => [
            'name' => 'Map X Total Barons Over/Under',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '9036' => [
            'name' => 'Map X Total Barons Moneyline',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9037' => [
            'name' => 'Map X 1st Baron',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9038' => [
            'name' => 'Map X 2nd Baron',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9039' => [
            'name' => 'Map X 3rd Baron',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9040' => [
            'name' => 'Map X Total Inhibitors Handicap',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9041' => [
            'name' => 'Map X Total Inhibitors Over/Under',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '9042' => [
            'name' => 'Map X Total Inhibitors Moneyline',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9043' => [
            'name' => 'Map X 1st Inhibitor',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9044' => [
            'name' => 'Map X 2nd Inhibitor',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9045' => [
            'name' => 'Map X 3rd Inhibitor',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9046' => [
            'name' => 'Map X Total Tyrants Handicap',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9047' => [
            'name' => 'Map X Total Tyrants Over/Under',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '9048' => [
            'name' => 'Map X Total Tyrants Handicap',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9049' => [
            'name' => 'Map X 1st Tyrant',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9050' => [
            'name' => 'Map X 2nd Tyrant ',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9051' => [
            'name' => 'Map X 3rd Tyrant',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9052' => [
            'name' => 'Map X Total Overlords Handicap',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9053' => [
            'name' => 'Map X Total Overlords Over/Under',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '9054' => [
            'name' => 'Map X Total Overlords Moneyline',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9055' => [
            'name' => 'Map X 1st Overlord',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9056' => [
            'name' => 'Map X 2nd Overlord',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9057' => [
            'name' => 'Map X 3rd Overlord',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9058' => [
            'name' => 'Map X Duration Over/Under(Mins)',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '9059' => [
            'name' => 'Map X Rounds Handicap ',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9060' => [
            'name' => 'Map X Rounds Over/Under',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '9061' => [
            'name' => 'Map X Rounds Odd/Even',
            'info' => [
                'o' => 'odd',
                'e' => 'even',
            ],
        ],
        '9062' => [
            'name' => 'Map X First to Y Rounds',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9063' => [
            'name' => 'Map X First Half',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9064' => [
            'name' => 'Map X Second Half',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9065' => [
            'name' => 'Map X Most First Kill',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9066' => [
            'name' => 'Map X Clutches',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9067' => [
            'name' => 'Map X 16th Round',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9068' => [
            'name' => 'Map X Round Y Moneyline',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9069' => [
            'name' => 'Map X Round Y Total Kills Moneyline',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9070' => [
            'name' => 'Map X Round Y Total Kills Over/Under',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '9071' => [
            'name' => 'Map X Round Y Total Kills Odd/Even',
            'info' => [
                'o' => 'odd',
                'e' => 'even',
            ],
        ],
        '9072' => [
            'name' => 'Map X Round Y First Kill',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9073' => [
            'name' => 'Map X Round Y Bomb Plant',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9074' => [
            'name' => 'Map X Rounds Over/Under (Overtime)',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '9075' => [
            'name' => 'Map X Final Round Bomb Plant',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9076' => [
            'name' => 'Map X Clutches Handicap',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9077' => [
            'name' => 'Map X Round Y Total Kills Handicap',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '9078' => [
            'name' => 'Map X Total Towers Odd/Even',
            'info' => [
                'o' => 'odd',
                'e' => 'even',
            ],
        ],
        '9079' => [
            'name' => 'Map X Total Roshans Odd/Even',
            'info' => [
                'o' => 'odd',
                'e' => 'even',
            ],
        ],
        '9080' => [
            'name' => 'Map X Total Barracks Odd/Even',
            'info' => [
                'o' => 'odd',
                'e' => 'even',
            ],
        ],
        '9081' => [
            'name' => 'Map X Total Turrets Odd/Even',
            'info' => [
                'o' => 'odd',
                'e' => 'even',
            ],
        ],
        '9082' => [
            'name' => 'Map X Total Dragons Odd/Even',
            'info' => [
                'o' => 'odd',
                'e' => 'even',
            ],
        ],
        '9083' => [
            'name' => 'Map X Total Barons Odd/Even',
            'info' => [
                'o' => 'odd',
                'e' => 'even',
            ],
        ],
        '9084' => [
            'name' => 'Map X Total Inhibitors Odd/Even',
            'info' => [
                'o' => 'odd',
                'e' => 'even',
            ],
        ],
        '9085' => [
            'name' => 'Map X Total Tyrants Odd/Even',
            'info' => [
                'o' => 'odd',
                'e' => 'even',
            ],
        ],
        '9086' => [
            'name' => 'Map X Total Overlords Odd/Even',
            'info' => [
                'o' => 'odd',
                'e' => 'even',
            ],
        ],
        '8101' => [
            'name' => 'Big/Small',
            'info' => [
                'b' => 'big',
                's' => 'small',
            ],
        ],
        '8102' => [
            'name' => 'Odd/Even',
            'info' => [
                'o' => 'odd',
                'e' => 'even',
            ],
        ],
        '8103' => [
            'name' => '4 Seasons',
            'info' => [
                'sp' => 'Spring',
                'su' => 'Summer',
                'au' => 'Autumn',
                'wi' => 'Winter',
            ],
        ],
        '8104' => [
            'name' => 'More Odd/More Even',
            'info' => [
                'mo' => 'More Odd',
                'me' => 'More Even',
            ],
        ],
        '8105' => [
            'name' => 'Combo',
            'info' => [
                'bo' => 'Big/Odd',
                'be' => 'Big/Even',
                'so' => 'Small/Odd',
                'se' => 'Small/Even',
            ],
        ],
        '467' => [
            'name' => 'Half Time/Full Time Exact Total Goals',
            'info' => [
            ],
        ],
        '468' => [
            'name' => 'Score Box Handicap',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '469' => [
            'name' => 'Score Box Over/Under',
            'info' => [
                'o' => 'Equal/Over',
                'u' => 'Equal/Under',
            ],
        ],
        '470' => [
            'name' => 'Corners Odd/Even (own)',
            'info' => [
                'o' => 'odd',
                'e' => 'even',
            ],
        ],
        '471' => [
            'name' => '1H Corners Odd/Even (own)',
            'info' => [
                'o' => 'odd',
                'e' => 'even',
            ],
        ],
        '472' => [
            'name' => '2H Corners Odd/Even',
            'info' => [
                'o' => 'odd',
                'e' => 'even',
            ],
        ],
        '473' => [
            'name' => 'Total Corners',
            'info' => [
            ],
        ],
        '474' => [
            'name' => '1H Total Corners (own)',
            'info' => [
            ],
        ],
        '475' => [
            'name' => 'Alternative Corners',
            'info' => [
                'o' => 'over',
                'e' => 'exact',
                'u' => 'under',
            ],
        ],
        '476' => [
            'name' => '1H Alternative Corners',
            'info' => [
                'o' => 'over',
                'e' => 'exact',
                'u' => 'under',
            ],
        ],
        '477' => [
            'name' => 'Corner 3-Way Handicap',
            'info' => [
                '1' => 'home',
                'x' => 'draw',
                '2' => 'away',
            ],
        ],
        '478' => [
            'name' => '1H Corner 3-Way Handicap',
            'info' => [
                '1' => 'home',
                'x' => 'draw',
                '2' => 'away',
            ],
        ],
        '479' => [
            'name' => 'Time Of First Corner',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '481' => [
            'name' => 'Time Of 2H First Corner',
            'info' => [
                'y' => 'yes',
                'n' => 'no',
            ],
        ],
        '482' => [
            'name' => 'Home Team Over/Under Corners',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '483' => [
            'name' => 'Away Team Over/Under Corners',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '484' => [
            'name' => '1H Home Team Over/Under Corners',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '485' => [
            'name' => '1H Away Team Over/Under Corners',
            'info' => [
                'o' => 'over',
                'u' => 'under',
            ],
        ],
        '486' => [
            'name' => 'Corners Race',
            'info' => [
                'h' => 'home',
                'a' => 'away',
                'n' => 'neither',
            ],
        ],
        '487' => [
            'name' => '1H Corners Race',
            'info' => [
                'h' => 'home',
                'a' => 'away',
                'n' => 'neither',
            ],
        ],
        '488' => [
            'name' => 'First Corner (2-Way)',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '489' => [
            'name' => '1H First Corner (2-Way)',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '490' => [
            'name' => '2H First Corner (2-Way)',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '491' => [
            'name' => 'Last Corner (2-Way)',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '492' => [
            'name' => '1H Last Corner (2-Way)',
            'info' => [
                'h' => 'home',
                'a' => 'away',
            ],
        ],
        '493' => [
            'name' => 'Half Time/Full Time Total Corners',
            'info' => [
            ],
        ],
        '494' => [
            'name' => '1H Correct Corners',
            'info' => [
            ],
        ],
        '495' => [
            'name' => '2H Correct Corners',
            'info' => [
            ],
        ],
        '496' => [
            'name' => 'Corner Highest Scoring Half',
            'info' => [
                '1h'  => 'First Half',
                '2h'  => 'Second Half',
                'tie' => 'Tie',
            ],
        ],
        '497' => [
            'name' => 'Corner Highest Scoring Half(2-Way)',
            'info' => [
                '1h' => 'First Half',
                '2h' => 'Second Half',
            ],
        ],
        '4601' => [
            'name' => 'Baccarat',
            'info' => [
            ],
        ],
        '4602' => [
            'name' => 'Xoc Dia',
            'info' => [
            ],
        ],
        '4603' => [
            'name' => 'Sic Bo',
            'info' => [
            ],
        ],
        '4604' => [
            'name' => 'Fish Prawn Crab',
            'info' => [
            ],
        ],
        '376' => [
            'name' => 'Penalty Shootout Over/Combination (First 10)',
            'info' => [
                'h' => 'home',
                'a' => 'away',
                's' => 'score'
            ],
        ],
    ];
}

