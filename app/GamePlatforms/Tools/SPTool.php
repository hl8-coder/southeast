<?php
namespace App\GamePlatforms\Tools;

use App\Models\ExchangeRate;
use App\Models\Game;
use App\Models\GameBetDetail;
use App\Models\GamePlatformProduct;
use App\Repositories\GamePlatformTransferDetailRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SPTool extends Tool
{
    protected $currencies = [
        'VND' => 'VND',
        'THB' => 'THB',
    ];

    protected $languages = [
        'vi-VN' => 'vn',
        'en-US' => 'en_US',
        'th'    => 'th',
        'zh-CN' => 'zh_CN',
    ];

    protected $message = [
        'client' => 'https://www.sai.slgaming.net/app.aspx',
    ];

    protected $errors = [
        '102' => 'Secret key incorrect',
        '106' => 'Server busy. Try again later.',
        '108' => 'Username length/format incorrect.',
        '111' => 'Query time range out of limitation.',
        '112' => 'API recently called.',
        '113' => 'Username duplicated.',
        '114' => 'Currency not exist.',
        '116' => 'Username does not exist.',
        '120' => 'Amount must greater than zero.',
        '121' => 'Your Wallet Balance Is Not Enough To Transfer,Please Try Again.',
        '122' => 'Order ID already exist.',
        '124' => 'DB error.',
        '127' => 'Invalid order ID format.',
        '129' => 'System under maintenance.',
        '130' => 'User account is locked (disabled).',
        '133' => 'User creation failed.',
        '134' => 'Game code not found.',
        '135' => 'Game access denied.',
        '142' => 'Parameter(s) error.',
        '145' => 'Parameter decimal point greater than 2.',
    ];

    public function transferBetDetail($originBetDetails)
    {
        $betDetails = [];

        $now = now();

        # 只有一条数据时需要包装成数组
        if (isset($originBetDetails['BetTime'])) {
            $originBetDetails = [$originBetDetails];
        }

        foreach ($originBetDetails as $record) {

            if (!$game = Game::findByPlatformAndCode($this->platform->code, $record['Detail'])) {
                continue;
            }

            if (!$user = $this->getUser($record['Username'])) {
                continue;
            }

            $betDetails[] = [
                'platform_code'         => $this->platform->code,
                'product_code'          => $game->product_code,
                'platform_currency'     => $user->currency,
                'order_id'              => $record['BetID'],
                'game_code'             => $game->code,
                'game_type'             => $game->type,
                'game_name'             => $game->getEnName(),
                'user_id'               => $user->id,
                'user_name'             => $user->name,
                'issue'                 => $record['Round'],
                'stake'                 => $record['BetAmount'],
                'bet'                   => $record['BetAmount'],
                'prize'                 => $record['ResultAmount'] + $record['BetAmount'],
                'profit'                => $record['ResultAmount'],
                'bet_at'                => $record['BetTime'],
                'payout_at'             => $record['PayoutTime'],
                'user_currency'         => $user->currency,
                'user_stake'            => $record['BetAmount'],
                'user_bet'              => $record['BetAmount'],
                'user_prize'            => $record['ResultAmount'] + $record['BetAmount'],
                'user_profit'           => $record['ResultAmount'],
                'platform_profit'       => -1 * $record['ResultAmount'],
                'after_balance'         => $record['Balance'],
                'platform_status'       => GameBetDetail::PLATFORM_STATUS_BET_SUCCESS,
                'available_bet'         => $record['BetAmount'],
                'available_profit'      => -1 * $record['ResultAmount'],
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

    public function encrypt($request, $account)
    {
        $qs = http_build_query($request);
        $q = openssl_encrypt($qs, 'DES-CBC', $account['encrypt_key'], OPENSSL_RAW_DATA, $account['encrypt_key']);
        return base64_encode($q);
    }

    public function buildMd5($request, $account)
    {
        $qs = http_build_query($request);
        return md5($qs . $account['md5_key'] . $request['Time'] . $account['secret_key']);
    }

    public function checkResponse($response, $method, $data)
    {
        $result = get_response_body($response, 'xml');
        $statusCode = $response->getStatusCode();

        $this->responseLog($method, $statusCode, $result);

        if ($statusCode >= 300) {
            error_response(500, 'request error.');
        } else {
            switch ($result['ErrorMsgId']) {
                case 0:
                    if ('register' == $method) {
                        return $result['Username'];
                    } elseif ('login' == $method) {
                        return $result['GameURL'];
                    } elseif ('balance' == $method) {
                        if ('true' == $result['IsSuccess']) {
                            return $result['Balance'];
                        }
                    } elseif ('transfer' == $method) { # 如果转账成功，发起检查订单状态
                        return $this->checkTransferSuccess($result, $data);
                    } elseif ('check' == $method) {
                        if ('true' == $result['isExist']) {
                            return GamePlatformTransferDetailRepository::setSuccess($data['detail']);
                        } else {
                            return GamePlatformTransferDetailRepository::setFail($data['detail'], static::$commonErrors[static::ERROR_TRANSACTION_NOT_EXIST]);
                        }
                    } elseif ('pull' == $method) {
                        return isset($result['BetDetailList']['BetDetail']) ? $result['BetDetailList']['BetDetail'] : [];
                    } elseif ('kick_out' == $method) {
                      return true;
                    } else {
                        error_response(422, '未知错误');
                    }
                    break;
                case 108: # 用户名长度或者格式错误
                case 111: # 查询时间范围超出限制
                case 112: # 近期已调用
                case 113: # 用户名已存在
                case 114: # 币种不存在
                case 116: # 用户不存在
                case 120: # 数值必须大于0
                case 121: # 信用点或借记点不足
                case 122: # 订单ID已经存在
                case 127: # 不正确订单格式
                case 129: # 系统维护中
                case 130: # 用户账户锁定（无效)
                case 133: # 建立帐户失败
                case 134: # 游戏代码不存在
                case 135: # 游戏没有开放
                case 142: # 输入参数错误
                case 145: # 输入浮点数超过2位数错误
                    if ('transfer' == $method || 'check' == $method) {
                        return GamePlatformTransferDetailRepository::setFail($data['detail'], $this->errors[$result['ErrorMsgId']]);
                    }
                    error_response(422, $this->getError($result['ErrorMsgId']));
                    break;
                case 106: # 伺服器未准备, 稍后尝试
                case 124: # 数据库错误
                default:
                    if ('transfer' == $method || 'check' == $method) {
                        return GamePlatformTransferDetailRepository::setWaiting($data['detail'], static::$commonErrors[static::ERROR_UNKNOWN]);
                    }
                    error_response(422, $this->getError($result['ErrorMsgId']));
                    break;
            }
        }
    }

    public function checkTransferSuccess($result, $data)
    {
        $detail = $data['detail'];

        $amount = $detail->isIncome() ? $result['CreditAmount'] : $result['DebitAmount'];
        if ($detail->conversion_amount == $amount && $detail->platform_order_no == $result['OrderId']) {
            # 更新状态成功
            GamePlatformTransferDetailRepository::setSuccess($detail);
        } else {
            GamePlatformTransferDetailRepository::setWaitManualConfirm($detail);
        }

        return $detail;
    }


}
