<?php
namespace App\GamePlatforms\Tools;

use App\Models\ExchangeRate;
use App\Models\Game;
use App\Models\GameBetDetail;
use App\Repositories\GamePlatformTransferDetailRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;

class RTGTool extends Tool
{
    protected $currencies = [
        'VND' => 'USD',
        'THB' => 'THB',
    ];

    protected $languages = [
        'zh-CN' => 'zh-CN',
        'vi-VN' => 'vi-VN',
        'en-US' => 'en-US',
        'th'    => 'th-TH',
    ];

    protected $errors = [
        '400' => 'Incorrect username/password/unable to authenticate, see returned error string.',
        '401' => 'IP restrictions without system certification.',
        '403' => 'IP restrictions.',
        '404' => 'Can not find player',
        '409' => 'Player already exists',
        '500' => 'Unknown error, please contact customer service.',
    ];

    public function transferBetDetail($originBetDetails)
    {
        $betDetails = [];
        $now = now();
        foreach ($originBetDetails as $key => $record) {

            if (!$game = Game::findByPlatformAndCode($this->platform->code, $record['gameId'])) {
                continue;
            }

            if (!$user = $this->getUser($record['playerName'])) {
                continue;
            }

            if ($rate = ExchangeRate::findRateByUserAndPlatform($user, $this->platform)) {
                $userCurrency   = $rate->user_currency;
                $userBet        = round($record['bet'] * $rate->inverse_conversion_value, 6);
                $userPrize      = round($record['win'] * $rate->inverse_conversion_value, 6);
                $userProfit     = round(($record['win'] - $record['bet']) * $rate->inverse_conversion_value, 6);
                $afterBalance   = round($record['balanceEnd'] * $rate->inverse_conversion_value, 6);
                $jpc            = round($record['jpBet'] * $rate->inverse_conversion_value, 6);
                $jpw            = round($record['jpWin'] * $rate->inverse_conversion_value, 6);
            } else {
                $userCurrency   = $record['currency'];
                $userBet        = $record['bet'];
                $userPrize      = $record['win'];
                $userProfit     = $record['win'] - $record['bet'];
                $afterBalance   = $record['balanceEnd'];
                $jpc            = $record['jpBet'];
                $jpw            = $record['jpWin'];
            }

            $betDetails[$key] = [
                'platform_code'         => $this->platform->code,
                'product_code'          => $game->product_code,
                'platform_currency'     => $record['currency'],
                'order_id'              => $record['id'],
                'game_code'             => $game->code,
                'game_type'             => $game->type,
                'game_name'             => $game->getEnName(),
                'user_id'               => $user->id,
                'user_name'             => $user->name,
                'issue'                 => $record['gameNumber'],
                'stake'                 => $record['bet'],
                'bet'                   => $record['bet'],
                'profit'                => $record['win'] - $record['bet'],
                'bet_at'                => $this->parseUTC8($record['gameStartDate']),
                'payout_at'             => $this->parseUTC8($record['gameDate']),
                'prize'                 => $record['win'],
                'user_currency'         => $userCurrency,
                'user_stake'            => $userBet,
                'user_bet'              => $userBet,
                'user_prize'            => $userPrize,
                'user_profit'           => $userProfit,
                'after_balance'         => $afterBalance,
                'platform_profit'       => -1 * $userProfit,
                'platform_status'       => GameBetDetail::PLATFORM_STATUS_BET_SUCCESS,
                'available_bet'         => $userBet,
                'available_profit'      => -1 * $userProfit,
                'jpc'                   => $jpc,
                'jpw'                   => $jpw,
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

    public function parseUTC8($timeStr)
    {
        return Carbon::parse($timeStr)->addHours(8)->toDateTimeString();
    }

    public function parseUTC0($timeStr)
    {
        $time = Carbon::parse($timeStr)->subHours(8)->toDateTimeString();

        return str_replace(' ', 'T', $time) . 'Z';
    }

    public function checkResponse($response, $method, $data)
    {
        if ('balance' == $method) {
            $result = get_response_body($response);
        } else {
            $result = get_response_body($response, 'json');
        }

        $statusCode = $response->getStatusCode();

        $this->responseLog($method, $statusCode, $result);

        switch ($statusCode) {
            case 200:
                if ('transfer' == $method) {
                    return $this->checkTransferSuccess($result, $data);
                } elseif ('login' == $method) {
                    return $result['instantPlayUrl'];
                } elseif ('balance' == $method) {
                    return (float)$result;
                } elseif ('pull' == $method) {
                    return $result['items'];
                } elseif ('auth' == $method) {
                    return $result['token'];
                }
                break;
            case 201:
                if ('register' == $method) {
                    return $result['id'];
                }
                break;
            case 400:
            case 401:
            case 403:
            case 404:
                error_response($statusCode, $this->errors[$statusCode]);
                break;
            case 409:
                if ('register' == $method) {
                    return '';
                }
                if ('transfer' == $method) {
                    return GamePlatformTransferDetailRepository::setFail($data['detail'], $this->errors[$statusCode]);
                }
                error_response($statusCode, $this->errors[$statusCode]);
            break;
            case 500:
            default:
                if ('transfer' == $method) {
                    return GamePlatformTransferDetailRepository::setWaitManualConfirm($data['detail'], $this->errors[$statusCode]);
                }
                error_response(500, $this->errors[500]);
                break;
        }
    }

    public function checkTransferSuccess($result, $data)
    {
        $detail = $data['detail'];
        if (isset($result['errorMessage']) && 'OK' == $result['errorMessage']) {
            # 更新第三方交易id
            GamePlatformTransferDetailRepository::setPlatformOrderNo($detail, $result['transactionId']);
            # 更新状态成功
            $detail = GamePlatformTransferDetailRepository::setSuccess($detail);
        } else {
            $detail = GamePlatformTransferDetailRepository::setWaitManualConfirm($detail);
        }

        return $detail;
    }
}