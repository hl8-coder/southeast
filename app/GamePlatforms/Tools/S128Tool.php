<?php


namespace App\GamePlatforms\Tools;

use App\Models\Game;
use App\Models\GameBetDetail;
use App\Models\User;
use App\Repositories\GamePlatformTransferDetailRepository;
use Illuminate\Support\Facades\Log;

class S128Tool extends Tool
{
    public $languages = [
        'en-US' => 'en-US',
        'th'    => 'en-US',
        'zh-CN' => 'zh-CN',
        'vi-VN' => 'vi-VN'
    ];
    public $odds = [
        User::ODDS_CHINA      => 'HK',
        User::ODDS_INDONESIAN => 'MY',
        User::ODDS_AMERICAN   => 'MY',
        User::ODDS_DECIMAL    => 'EU',
        User::ODDS_MALAY      => 'MY',
    ];
    public $localOdds = [
        'HK' => User::ODDS_CHINA,
        'EU' => User::ODDS_DECIMAL,
        'MY' => User::ODDS_MALAY,
    ];

    public $recordStatus = [
        'BDD', // 和
        'MERON', // 龙赢
        'WALA', // 凤赢
        'FTD', // 大和
    ];

    public $statusRelation = [
        'WIN'    => GameBetDetail::PLATFORM_STATUS_BET_SUCCESS,
        'LOSE'   => GameBetDetail::PLATFORM_STATUS_BET_SUCCESS,
        'REFUND' => GameBetDetail::PLATFORM_STATUS_CANCEL,
        'CANCEL' => GameBetDetail::PLATFORM_STATUS_CANCEL,
        'VOID'   => GameBetDetail::PLATFORM_STATUS_CANCEL,
        // WIN/LOSE/REFUND/CANCEL /VOID
    ];


    protected $errors = [
        '61.00'  => 'datetime range exceed 24 hours',
        '61.00a' => 'repeat access within allow interval - 60 secs',
        '61.01'  => 'api key not found',
        '61.01a' => 'create new player fail',
        '61.02'  => 'login id not found', // deposit => ref no. existed
        '61.03'  => 'amount exceed balance',
        '99'     => 'login id not found',
    ];

    public function getOddsType(int $type): string
    {
        return isset($this->odds[$type]) ? $this->odds[$type] : 'MY';
    }

    public function getLocalOddsType(string $type): int
    {
        $odds = $this->localOdds;
        return $odds[$type];
    }

    public function getLanguage(string $language): string
    {
        $languages = $this->languages;
        return isset($languages[$language]) ? $languages[$language] : '';
    }


    public function checkResponse($response, string $methodName = 'login', array $data = [])
    {
        $body = get_response_body($response, 'xml');
        $this->responseLog($methodName, $body['status_code'], $body);

        if ('check_user_exists' == $methodName && $body['status_code'] =='61.02'){
            return false;
        }
        switch ($body['status_code']) {
            case '00':
                switch ($methodName) {
                    case 'balance' :
                        return $body['balance'];
                        break;
                    case 'deposit' :
                    case 'withdraw':
                        $detail = $data['detail'];
                        GamePlatformTransferDetailRepository::setPlatformOrderNo($detail, $body['trans_id']);
                        return GamePlatformTransferDetailRepository::setSuccess($detail);
                        break;
                    case 'check':
                        $detail = $data['detail'];
                        if ($this->checkDataMatch($detail, $body)){
                            $detail = GamePlatformTransferDetailRepository::setSuccess($detail);
                        }else{
                            $detail =  GamePlatformTransferDetailRepository::setFail($detail);
                        }
                        return $detail;
                        break;
                    case 'check_user_exists':
                        return true;
                        break;
                    case 'pull':
                    case 'login':
                    case 'launcher':
                    default:
                        return $body;
                        break;
                }
                break;
            case '61.00':
            case '61.00a':
            case '61.01':
            case '61.01a':
            case '61.02':
            case '61.03':
            case '99':
                switch ($methodName) {
                    case 'deposit' :
                    case 'withdraw':
                    case 'check':
                        $detail = $data['detail'];
                        $transId = is_string($body['trans_id']) ? $body['trans_id'] : json_encode($body['trans_id']);
                        GamePlatformTransferDetailRepository::setPlatformOrderNo($detail, $transId);
                        return GamePlatformTransferDetailRepository::setFail($detail, $this->getError($body['status_code']));
                        break;
                    case 'login':
                    case 'launcher':
                    case 'balance' :
                    case 'pull' :
                        error_response(422, $this->getError($body['status_code']));
                        break;
                    default:
                        error_response(422, 'error');
                }
                break;
            default:
                error_response(422, 'error');
                break;
        }
    }


    public function insertBetDetails($bodies)
    {
        $betDetails = [];
        $now        = now();
        $original   = 0;
        foreach ($bodies as $body) {
            $reportRows = $this->handleData($body['data']);
            $original += count($reportRows);
            foreach ($reportRows as $key => $record) {

                if (!$game = Game::findByPlatformAndCode($this->platform->code, 's128')) {
                    continue;
                }

                if (!$user = $this->getUser($record['login_id'])) {
                    continue;
                }
                $languageSet      = $game->getLanguageSet('en-US');
                $availables       = $this->getAvailableBetAndProfit($record);
                $betDetails[]     = [
                    'platform_code'     => $this->platform->code,
                    'product_code'      => $game->product_code,
                    'order_id'          => $record['ticket_id'],
                    'game_code'         => $game->code,
                    'game_type'         => $game->type,
                    'game_name'         => $languageSet['name'],
                    'user_id'           => $user->id,
                    'user_name'         => $user->name,
                    'issue'             => '',
                    'bet_at'            => $record['created_datetime'],
                    'payout_at'         => $record['processed_datetime'],
                    'odds'              => $this->getLocalOddsType($record['odds_type']),
                    'platform_currency' => $user->currency,
                    'stake'             => $record['stake'],
                    'bet'               => $availables['bet'],
                    'profit'            => $record['winloss'],
                    'prize'             => $record['stake'] + $record['winloss'],
                    'user_currency'     => $user->currency,
                    'user_stake'        => $record['stake'],
                    'user_bet'          => $availables['bet'],
                    'user_profit'       => $record['winloss'],
                    'user_prize'        => $record['stake'] + $record['winloss'],
                    'after_balance'     => $record['balance_close'],
                    'platform_profit'   => -1 * $record['winloss'],
                    'platform_status'   => $this->getPlatformStatus($record),
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

        return [
            'origin_total'   => $original,
            'transfer_total' => count($betDetails),
        ];
    }

    private function handleData($data): array
    {
        $keys = ['ticket_id', 'login_id', 'arena_code', 'arena_name_cn', 'match_no', 'match_type', 'match_date',
            'fight_no', 'fight_datetime', 'meron_cock', 'meron_cock_cn', 'wala_cock', 'wala_cock_cn', 'bet_on',
            'odds_type', 'odds_asked', 'odds_given', 'stake', 'stake_money', 'balance_open', 'balance_close',
            'created_datetime', 'fight_result', 'status', 'winloss', 'comm_earned', 'payout', 'balance_open1',
            'balance_close1', 'processed_datetime'];

        if (is_array($data)) {
            return $data;
        }
        $rows = explode('|', $data);
        array_walk($rows, function (&$item) use ($keys) {
            $item = explode(',', $item);
            $item = array_combine($keys, $item);
        });
        return $rows;
    }

    private function getBetInfo($record)
    {
        $info = "bet_on:" . $record['bet_on'] . "; odds:" . $record['odds_given'] . "; amount:" . $record['stake'];
        $info .= '; result:' . $record['status'] . '_' . $record['winloss'];
        return $info;
    }

    private function getAvailableBetAndProfit($record)
    {
        $result = [
            'bet'    => 0,
            'profit' => 0,
        ];
        if (in_array($record['status'], ['REFUND', 'CANCEL', 'VOID'])) {
            return $result;
        }

        if ($record['bet_on'] == 'BDD') {
            return $result;
        }

        $result['bet']    = $record['stake'];
        $result['profit'] = $record['winloss'];
        return $result;
    }

    private function getPlatformStatus($record)
    {
        $status = $this->statusRelation;
        return isset($status[$record['status']]) ? $status[$record['status']] : GameBetDetail::PLATFORM_STATUS_WAITING;
    }

    private function checkDataMatch($detail, $body)
    {
        $detailAmount   = $detail->amount;
        $responseAmount = $body['amount'];
        if ($detail->isIncome()){
            return $detailAmount == (int)$responseAmount && !empty($body['found']);
        }else{
            return -$detailAmount == (int)$responseAmount && !empty($body['found']);
        }
    }


}
