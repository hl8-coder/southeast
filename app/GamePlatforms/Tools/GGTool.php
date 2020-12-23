<?php


namespace App\GamePlatforms\Tools;


use App\Models\Game;
use App\Models\GameBetDetail;
use App\Models\User;
use App\Repositories\GamePlatformTransferDetailRepository;
use Illuminate\Support\Facades\Log;

class GGTool extends Tool
{
    public $languages = [
        // 我方，对方
        'en-US' => 'en-US',
        'th'    => 'th',
        'zh-CN' => 'zh-CN',
        'vi-VN' => 'vi-VN',
    ];

    public $currency = [
        'THB' => 'THB',
        'CNY' => 'CNY',
        'USD' => 'USD',
        'VND' => 'VND2',
    ];

    public $exchange = [
        'THB'  => '2',
        'CNY'  => '1',
        'USD'  => '0.2',
        'VND2' => '3',
    ];

    public $localOdds = [
        'CNY'  => User::ODDS_CHINA,
        'USD'  => User::ODDS_AMERICAN,
        'VND2' => User::ODDS_MALAY,
        'THB'  => User::ODDS_MALAY,
    ];

    public $playerDevice = [
        '0' => 'PC Web',
        '1' => 'Android',
        '2' => 'iOS',
        '3' => 'Android Web',
        '4' => 'iOS Web',
    ];

    // 订单关系
    public $statusRelation = [
        '0' => GameBetDetail::PLATFORM_STATUS_WAITING,
        '1' => GameBetDetail::PLATFORM_STATUS_BET_SUCCESS,
    ];


    protected $errors = [
        '0' => 'success',
        '1' => 'other mistake', # 其它错误
        '2' => 'password mistake', # 密码错误
        '6' => 'agent not exist', # 代理商不存在
        '7' => 'Des mistake', # Des 值错误
        '8' => 'Key mistake', # Key 值错误
        '9' => 'Information not completed', # 资料不全
    ];

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
        $body = get_response_body($response, 'json');
        $this->responseLog($methodName, $body['code'], $body);
        switch ($body['code']) {
            case '0':
                switch ($methodName) {
                    case 'balance' :
                        return $body['balance'];
                        break;
                    case 'transfer':
                    case 'check':
                        $detail = $data['detail'];
                        return GamePlatformTransferDetailRepository::setSuccess($detail);
                        break;
                    case 'register':
                        return (string)'';
                        break;
                    case 'login':
                        return $body['url'];
                    case 'kick_out':
                        return true;
                        break;
                    case 'pull':
                    default:
                        return $body;
                        break;
                }
                break;
            case '1':
            case '2':
            case '6':
            case '7':
            case '8':
            case '9':
                switch ($methodName) {
                    case 'transfer':
                    case 'check':
                        $detail = $data['detail'];
                        return GamePlatformTransferDetailRepository::setFail($detail, $this->getError($body['msg']));
                        break;
                    case 'login':
                    case 'balance' :
                    case 'register' :
                    case 'pull' :
                        error_response(422, $this->getError($body['code']));
                        break;
                    default:
                        error_response(422, $this->getError($body['code']));
                }
                break;
            default:
                error_response(422, $body['msg']);
                break;
        }
    }


    public function insertBetDetails($body)
    {
        $account    = $this->platform->account;
        $reportRows = $body['recordlist3'];
        $betDetails = [];
        $now        = now();
        foreach ($reportRows as $key => $record) {
            if (!$game = Game::findByPlatformAndCode($this->platform->code, $record['gameId'])) {
                continue;
            }

            if (!$user = $this->getUser(str_replace($account['agent_name'], '', $record['accountno']))) {
                continue;
            }

            if ($record['closed'] == '0') { // 未结算
                continue;
            }

            $languageSet      = $game->getLanguageSet('en-US');
            $availables       = $this->getAvailableBetAndProfit($record);
            $betDetails[$key] = [
                'platform_code'     => $this->platform->code,
                'product_code'      => $game->product_code,
                'order_id'          => $record['betid'],
                'game_code'         => $game->code,
                'game_type'         => $game->type,
                'game_name'         => $languageSet['name'],
                'user_id'           => $user->id,
                'user_name'         => $user->name,
                'issue'             => $record['autoid'],
                'bet_at'            => $record['bettimeStr'],
                'payout_at'         => $record['paytimeStr'],
                'odds'              => '',
                'platform_currency' => $this->getLocalOddsType($record['cuuency']),
                'stake'             => $record['bet'],
                'bet'               => $availables['bet'],
                'profit'            => $record['profit'],
                'prize'             => $record['bet'] + $record['profit'],
                'user_currency'     => $user->currency,
                'user_stake'        => $record['bet'],
                'user_bet'          => $availables['bet'],
                'user_profit'       => $record['profit'],
                'user_prize'        => $record['bet'] + $record['profit'],
                'after_balance'     => '',
                'platform_profit'   => -1 * $record['profit'],
                'platform_status'   => $this->getPlatformStatus($record),
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
            'origin_total'   => count($reportRows),
            'transfer_total' => count($betDetails),
        ];
    }

    private function getBetInfo($record)
    {
        $device = $this->playerDevice[$record['origin']];
        $info = 'bet:' . $record['bet'] . ' result:' . $record['profit'] . ' time:' . $record['bettimeStr'] . ' in:' . $device;
        return $info;
    }

    private function getAvailableBetAndProfit($record)
    {
        $result = [
            'bet'    => 0,
            'profit' => 0,
        ];

        $result['bet']    = $record['bet'];
        $result['profit'] = $record['profit'];
        return $result;
    }

    private function getPlatformStatus($record)
    {
        $status = $this->statusRelation;
        return isset($status[$record['closed']]) ? $status[$record['closed']] : GameBetDetail::PLATFORM_STATUS_WAITING;
    }


}
