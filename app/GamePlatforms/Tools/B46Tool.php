<?php
namespace App\GamePlatforms\Tools;

use App\Models\Config;
use App\Models\User;
use App\Models\Game;
use App\Models\GameBetDetail;
use App\Models\GamePlatformUser;
use App\Repositories\GamePlatformTransferDetailRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class B46Tool extends Tool
{
    protected $currencies = [
        'VND' => 'VND',
        'THB' => 'THB',
    ];

    protected $languages = [
        'vi-VN' => 'vi',
        'en-US' => 'en',
        'th'    => 'th',
        'zh-CN' => 'zh-cn',
    ];

    protected $oddsTypes = [
        User::ODDS_CHINA        => 2,
        User::ODDS_INDONESIAN   => 3,
        User::ODDS_AMERICAN     => 0,
        User::ODDS_DECIMAL      => 1,
        User::ODDS_MALAY        => 4,
    ];

    protected $betTypes = [
        1   => '1x2 win',
        2   => 'Handicap',
        3   => 'Over Under',
        4   => 'Home Totals',
        5   => 'Away Totals',
        6   => 'Mix Parlay',
        7   => 'Teaser',
        8   => 'Manual Play',
        97  => 'Odd Even',
        99  => 'Outright',
    ];

    # 第三方状态mapping
    protected $platformMappingStatuses = [
        'WIN'       => GameBetDetail::PLATFORM_STATUS_BET_SUCCESS,
        'WON'       => GameBetDetail::PLATFORM_STATUS_BET_SUCCESS,
        'LOSE'      => GameBetDetail::PLATFORM_STATUS_BET_SUCCESS,
        'DRAW'      => GameBetDetail::PLATFORM_STATUS_BET_SUCCESS,
        'OPEN'      => GameBetDetail::PLATFORM_STATUS_WAITING,
        'PENDING'   => GameBetDetail::PLATFORM_STATUS_WAITING,
        'CANCELLED' =>  GameBetDetail::PLATFORM_STATUS_CANCEL,
        'DELETED'   => GameBetDetail::PLATFORM_STATUS_BET_FAIL,
    ];

    protected $sportsMapping = [
        'B46ESports'  => 'e-sports',
    ];

    protected $usernameSuffix = '0x0';

    public function generateToken($agentCode, $agentKey, $secretKey)
    {
        $timestamp = time()*1000;
        $hashToken = md5($agentCode. $timestamp . $agentKey);
        $tokenPayLoad = $agentCode . '|' . $timestamp . '|' . $hashToken;
        $token = $this->encryptAES($secretKey, $tokenPayLoad);

        return $token;
    }

    private function encryptAES($secretKey, $tokenPayLoad)
    {
        $iv = "RandomInitVector";
        $encrypt = openssl_encrypt($tokenPayLoad, "AES-128-CBC", $secretKey, OPENSSL_RAW_DATA, $iv);
        return base64_encode($encrypt);
    }

    public function transferBetDetail($originBetDetails)
    {
        $betDetails = [];
        $now = now();
        $totalRecords = 0;

        foreach ($originBetDetails as $originBetDetail) {
            $totalRecords = $totalRecords + count($originBetDetail);
            foreach ($originBetDetail as $record) {
                if (!$game = Game::findByPlatformAndCode($this->platform->code, 'B46Sports')) {
                    continue;
                }
                if (!$user = $this->getUser($record['userCode'])) {
                    continue;
                }
                $availables = $this->getAvailableBetAndProfit($record);
                $now = now();
                $betDetails[] = [
                    'platform_code' => $this->platform->code,
                    'product_code' => $game->product_code,
                    'order_id' => $record['wagerId'],
                    'game_code' => $game->code,
                    'game_type' => $game->type,
                    'game_name' => $record['sport'],
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'bet_at' => $this->transferGMT8($record['wagerDateFm'])->toDateTimeString(),
                    'payout_at' => $this->transferGMT8($record['settleDateFm'])->toDateTimeString(),
                    'odds' => $record['odds'] . "\n" . $this->getLocalOddsType($record['oddsFormat']),
                    'platform_currency' => $record['currencyCode'],
                    'stake' => $record['stake'],
                    'bet' => $availables['bet'],
                    'profit' => $record['winLoss'],
                    'prize' => $record['stake'] + $record['winLoss'],
                    'user_currency' => $user->currency,
                    'user_stake' => $record['stake'],
                    'user_bet' => $availables['bet'],
                    'user_profit' => $record['winLoss'],
                    'user_prize' => $record['stake'] + $record['winLoss'],
                    'platform_profit' => -1 * $record['winLoss'],
                    'platform_status' => $this->getPlatformStatus($record),
                    'available_bet' => $availables['bet'],
                    'available_profit' => -1 * $availables['profit'],
                    'bet_info' => $this->getBetInfo($record),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        if (!empty($betDetails)) {
            # 添加总的投注明细表
            batch_insert('game_bet_details', $betDetails, true);
        }
        return [
            'origin_total'   => $totalRecords,
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
            # This could have been much more simple but considering my experience with this platform(returning null)
            if(is_null($result)) {
                $result = [];
            }
            if(!isset($result['code'])) {
                if ('register' == $method) {
                    return $result['userCode'];
                } elseif ('login' == $method) {
                    return $result['loginUrl'];
                } elseif ('balance' == $method) {
                    if ('ACTIVE' == $result['status']) {
                        return $result['availableBalance'];
                    }
                } elseif ('transfer' == $method ) {
                    if (isset($result['availableBalance'])) {
                        return GamePlatformTransferDetailRepository::setSuccess($data['detail']);
                    }
                } elseif ('check' == $method ) {
                    if (isset($result['status']) && 'SUCCESS' == $result['status']) {
                        return GamePlatformTransferDetailRepository::setSuccess($data['detail']);
                    }elseif (isset($result['status']) && 'FAILED' == $result['status']) {
                        return GamePlatformTransferDetailRepository::setFail($data['detail']);
                    } else {
                        error_response(422, 'check fail');
                    }
                } elseif ('pull' == $method) {
                    return $result;
                } else {
                    error_response(422, $method . '未知错误');
                }
            }else {
                switch ($result['code']) {
                    case 103:
                    case 104:
                    case 105:
                    case 106:
                    case 107:
                    case 108:
                    case 109:
                    case 110:
                    case 111:
                    case 112:
                    case 113:
                    case 114:
                    case 115:
                    case 116:
                    case 305:
                    case 306:
                    case 307:
                    case 308:
                    case 309:
                    case 310:
                    case 311:
                    case 403:
                    case 405:
                    case 406:
                    case 407:
                    case 423:
                        if ('transfer' == $method || 'check' == $method) {
                            return GamePlatformTransferDetailRepository::setFail($data['detail'], $result['message']);
                        }
                        error_response(422, $result['message']);
                        break;
                    default:
                        if ('transfer' == $method || 'check' == $method) {
                            return GamePlatformTransferDetailRepository::setWaiting($data['detail'], static::$commonErrors[static::ERROR_UNKNOWN]);
                        }
                        error_response(422, $this->getError($result['description']));
                        break;
                }

            }

        }
    }


    public function getLocalOddsType($type)
    {
        $localOddsTypeMapping = array_flip($this->oddsTypes);
        $localOddsType = isset($localOddsTypeMapping[$type]) ? $localOddsTypeMapping[$type] : 1;
        return User::$odds[$localOddsType];
    }


    # 获取有效投注
    public function getAvailableBetAndProfit($record)
    {
        $result = [
            'bet'    => 0,
            'profit' => 0,
        ];

        if (in_array(strtoupper($record['status']), ['CANCELLED', 'DELETED', 'OPEN', 'PENDING'])) {
            return $result;
        }
        if (in_array(strtoupper($record['result']), ['DRAW'])) {
            return $result;
        }

        if('Horse Racing' == $record['sport']) {
            return $result;
        }
        # 盘口
        $odds = $record['odds'];
        switch ($record['oddsFormat']) {
            case 0: # 美国盘 -100 ~ -204
                if ($odds >= -204 && $odds <= -100) {
                    return $result;
                }
                break;
            case 1: # 欧洲盘  1.1 ~ 1.49
                if ($odds >= 1.1 && $odds <= 1.49) {
                    return $result;
                }
                break;
            case 2: # 香港盘  0.1 ~ 0.49
                if ($odds >= 0.1 && $odds <= 0.49) {
                    return $result;
                }
                break;

            case 3: # 印尼盘  -10 ~ -2.04
                if ($odds >= -10 && $odds <= -2.04) {
                    return $result;
                }
                break;
            case 4: # 马来盘  0.1 ~ 0.49
                if ($odds >= 0.1 && $odds <= 0.49) {
                    return $result;
                }
                break;
        }

        $result['bet']    = $record['stake'];
        $result['profit'] = $record['winLoss'];

        return $result;
    }


    # 获取投注信息
    public function getBetInfo($record)
    {
        $betInfo = '';
        try {
            if (0 == count($record['parlaySelections'])) {

                $betInfo = $this->singleBetInfo($record, $betInfo);
            } else {
                $parlayType = isset($this->betTypes[$record['betType']]) ? $this->betTypes[$record['betType']] : '';
                $betInfo .= $parlayType . "\n";
                foreach ($record['parlaySelections'] as $parlay) {
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
        $betType = isset($this->betTypes[$record['betType']]) ? $this->betTypes[$record['betType']] : '';
        $betInfo .= $record['sport'] . '/' . $betType . "\n";
        $betInfo .= $record["selection"]. "\n";
        if (!empty($record['handicap'])) {
            $betInfo .= $record['handicap'] . ' ';
        }

        $scores = $record['scores'];
        if (count($scores) > 0) {
            foreach($scores as $score) {
                if(0 == $score['period']) {
                    $betInfo .= $score['score'];
                }
            }
        }

        if (!empty($record['odds'])) {
            $betInfo .= "\n@ " . $record['odds'];
        }

        $betInfo .= "\n";

        # 主队
        if (!empty($record['homeTeam'])) {
            $betInfo .= $record['homeTeam'] . "\n";
        }

        # 客队
        if (!empty($record['awayTeam'])) {
            $betInfo .= $record['awayTeam'] . "\n";
        }

        if (!empty($record['eventDateFm'])) {
            $betInfo .=  $this->transferGMT8($record['eventDateFm'])->toDateTimeString() . "\n";
        }

        # 联赛
        if (!empty($record['league'])) {
            $betInfo .= $record['league'] . "\n";
        }

        # 结果
        if (!empty($record['legStatus'])) {
            $betInfo .= $record['legStatus'] . "\n";
        }elseif(!empty($record['result'])) {
            $betInfo .= $record['result'] . "\n";
        }

        $betInfo .= "\n";
        return $betInfo;
    }

    public function getPlatformStatus($record)
    {
        if(isset($this->platformMappingStatuses[$record['status']])){
            return $this->platformMappingStatuses[$record['status']];
        }
        if(isset($this->platformMappingStatuses[$record['result']])){
            return $this->platformMappingStatuses[$record['result']];
        }
        if(true == $record['inPlay']){
            $this->platformMappingStatuses['RUNNING'];
        }
    }

    public function getUser($accountCode){

        if(!$platformUser  = GamePlatformUser::where('platform_user_id', $accountCode)->first()){
            return false;
        }
        return $platformUser->user;
    }

    public function transferGMT8($time, $add = true)
    {
        if(true == $add) {
            return Carbon::parse($time)->addHours(12);
        }
        return Carbon::parse($time)->subHours(12);
    }

    public function formatUserName($username)
    {
        $operationId = Config::findValue('operation_id');
        $from = '/'.preg_quote($operationId, '/').'/';
        if( (strlen($username) - strlen($operationId)) < 6 ){
            $username = $username . $this->usernameSuffix;
        }
        return substr(preg_replace($from, $operationId.'.', $username, 1), 0, 50);
    }

    public function getSports($gameCode){
        if(isset($this->sportsMapping[$gameCode])) {
            return $this->sportsMapping[$gameCode];
        }
    }
}
