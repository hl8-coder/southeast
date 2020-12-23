<?php
namespace App\GamePlatforms\Tools;

use App\Models\ExchangeRate;
use App\Models\Game;
use App\Models\GameBetDetail;
use App\Models\GamePlatformProduct;
use App\Repositories\GamePlatformTransferDetailRepository;
use App\Repositories\GamePlatformUserRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SATool extends Tool
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

    protected $message = [];

    protected $errors = [
        '106' => 'Server busy. Try again later.',
        '108' => 'Username length/format incorrect.',
        '111' => 'Query time range out of limitation.',
        '112' => 'API recently called.',
        '113' => 'Username duplicated.',
        '114' => 'Currency not exist.',
        '116' => 'Username does not exist.',
        '120' => 'Amount must greater than zero.',
        '121' => 'Not enough points to credit/debit/bet.',
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

            $game = Game::findByPlatformAndCode($this->platform->code, 'SA_LIVE');

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
                'bet'                   => $record['Rolling'],
                'prize'                 => $record['ResultAmount'] + $record['BetAmount'],
                'profit'                => $record['ResultAmount'],
                'bet_at'                => $record['BetTime'],
                'payout_at'             => $record['PayoutTime'],
                'user_currency'         => $user->currency,
                'user_stake'            => $record['BetAmount'],
                'user_bet'              => $record['Rolling'],
                'user_prize'            => $record['ResultAmount'] + $record['BetAmount'],
                'user_profit'           => $record['ResultAmount'],
                'platform_profit'       => -1 * $record['ResultAmount'],
                'after_balance'         => $record['Balance'],
                'platform_status'       => $record['State'] ? GameBetDetail::PLATFORM_STATUS_BET_SUCCESS : GameBetDetail::PLATFORM_STATUS_BET_FAIL,
                'available_bet'         => $record['Rolling'],
                'available_profit'      => -1 * $record['ResultAmount'],
                'bet_info'              => $this->getBetInfo($record),
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
        $q  = openssl_encrypt($qs, 'DES-CBC', $account['encrypt_key'], OPENSSL_RAW_DATA, $account['encrypt_key']);
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

        if ($statusCode == 502) {
            error_response(502, 'Time out.');
        } elseif ($statusCode >= 300) {
            error_response(500, 'request status.');
        } else {
            switch ($result['ErrorMsgId']) {
                case 0:
                    if ('register' == $method) {
                        return $result['Username'];
                    } elseif ('login' == $method) {
                        return $result;
                    } elseif ('balance' == $method) {
                        if ($result['IsSuccess']) {
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
                    if ('register' == $method) {
                        return '';
                    }
                case 114: # 币种不存在
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
                    error_response(422, $this->errors[$result['ErrorMsgId']]);
                    break;
                case 116: # 用户不存在[重置会员远程注册]
                    if ($platformUser = GamePlatformUserRepository::findByUserAndPlatform($this->user->id, $this->platform->code)) {
                        $platformUser->update([
                            'platform_created_at' => null,
                            'platform_user_id' => null,
                        ]);
                    }
                    error_response(422, $this->errors[$result['ErrorMsgId']]);
                    break;
                case 106: # 伺服器未准备, 稍后尝试
                case 124: # 数据库错误
                default:
                    if ('transfer' == $method || 'check' == $method) {
                        return GamePlatformTransferDetailRepository::setWaiting($data['detail'], static::$commonErrors[static::ERROR_UNKNOWN]);
                    }
                    error_response(422, '未知错误');
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
            $detail = GamePlatformTransferDetailRepository::setSuccess($detail);
        } else {
            $detail = GamePlatformTransferDetailRepository::setWaitManualConfirm($detail);
        }

        return $detail;
    }

    public function getBetInfo($record)
    {
        $game = isset(static::$gameTypes[$record['GameType']]) ? static::$gameTypes[$record['GameType']] : '';

        $betInfo = $game . ' : ';

        switch ($record['GameType']) {
            case 'bac':
                $betInfo .= isset(static::$betTypeOfBaccarat[$record['BetType']]) ? static::$betTypeOfBaccarat[$record['BetType']] : '';
                break;
            case 'dtx':
                $betInfo .= isset(static::$betTypeOfDragonTiger[$record['BetType']]) ? static::$betTypeOfDragonTiger[$record['BetType']] : '';
                break;
            case 'sicbo':
                $betInfo .= isset(static::$betTypeOfSicBo[$record['BetType']]) ? static::$betTypeOfSicBo[$record['BetType']] : '';
                break;
            case 'ftan':
                $betInfo .= isset(static::$betTypeOfFanTan[$record['BetType']]) ? static::$betTypeOfFanTan[$record['BetType']] : '';
                break;
            case 'rot':
                $betInfo .= isset(static::$betTypeOfRoulette[$record['BetType']]) ? static::$betTypeOfRoulette[$record['BetType']] : '';
                break;
            case 'moneywheel':
                $betInfo .= isset(static::$betTypeOfMoneyWheel[$record['BetType']]) ? static::$betTypeOfMoneyWheel[$record['BetType']] : '';
                break;
            case 'tip':
                $betInfo .= isset(static::$betTypeOfTip[$record['BetType']]) ? static::$betTypeOfTip[$record['BetType']] : '';
                break;
        }

        return $betInfo;
    }

    public static $gameTypes = [
        'bac'         => 'Baccarat',
        'dtx'         => 'Dragon Tiger',
        'sicbo'       => 'Sic Bo',
        'ftan'        => 'Fan Tan',
        'rot'         => 'Roulette',
        'moneywheel'  => 'Money Wheel',
        'tip'         => 'Tips',
        'slot'        => 'Slot Game',
        'minigame'    => 'Mini Game',
        'multiplayer' => 'Mulit-player Game',
    ];

    # 百家乐投注类型
    public static $betTypeOfBaccarat = [
        '0'  => 'Tie',
        '1'  => 'Player',
        '2'  => 'Banker',
        '3'  => 'Player Pair',
        '4'  => 'Banker Pair',
        '25' => 'NC. Tie',
        '26' => 'NC. Player',
        '27' => 'NC. Banker',
        '28' => 'NC. Player Pair',
        '29' => 'NC. Banker Pair',
        '30' => 'SuperSix',
        '36' => 'Player Natural',
        '37' => 'Banker Natural',
        '40' => 'NC. Player Natural',
        '41' => 'NC. Banker Natural',
        '42' => 'Cow Cow Player',
        '43' => 'Cow Cow Banker',
        '44' => 'Cow Cow Tie',
    ];

    # 龙虎投注类型
    public static $betTypeOfDragonTiger = [
        '0' => 'Tie',
        '1' => 'Dragon',
        '2' => 'Tiger',
    ];

    # 骰宝投注类型
    public static $betTypeOfSicBo = [
        '0'  => 'Small',                '55'  => 'Three Even',
        '1'  => 'Big',                  '56'  => '1 2 3 4',
        '2'  => 'Odd',                  '57'  => '2 3 4 5',
        '3'  => 'Even',                 '58'  => '2 3 5 6',
        '4'  => 'Number 1',             '59'  => '3 4 5 6',
        '5'  => 'Number 2',             '60'  => '112',
        '6'  => 'Number 3',             '61'  => '113',
        '7'  => 'Number 4',             '62'  => '114',
        '8'  => 'Number 5',             '63'  => '115',
        '9'  => 'Number 6',             '64'  => '116',
        '10' => 'All 1',                '65'  => '221',
        '11' => 'All 2',                '66'  => '223',
        '12' => 'All 3',                '67'  => '224',
        '13' => 'All 4',                '68'  => '225',
        '14' => 'All 5',                '69'  => '226',
        '15' => 'All 6',                '70'  => '331',
        '16' => 'All same',             '71'  => '332',
        '17' => 'Point 4',              '72'  => '334',
        '18' => 'Point 5',              '73'  => '335',
        '19' => 'Point 6',              '74'  => '336',
        '20' => 'Point 7',              '75'  => '441',
        '21' => 'Point 8',              '76'  => '442',
        '22' => 'Point 9',              '77'  => '443',
        '23' => 'Point 10',             '78'  => '445',
        '24' => 'Point 11',             '79'  => '446',
        '25' => 'Point 12',             '80'  => '551',
        '26' => 'Point 13',             '81'  => '552',
        '27' => 'Point 14',             '82'  => '553',
        '28' => 'Point 15',             '83'  => '554',
        '29' => 'Point 16',             '84'  => '556',
        '30' => 'Point 17',             '85'  => '661',
        '31' => 'Specific double 1, 2', '86'  => '662',
        '32' => 'Specific double 1, 3', '87'  => '663',
        '33' => 'Specific double 1, 4', '88'  => '664',
        '34' => 'Specific double 1, 5', '89'  => '665',
        '35' => 'Specific double 1, 6', '90'  => '126',
        '36' => 'Specific double 2, 3', '91'  => '135',
        '37' => 'Specific double 2, 4', '92'  => '234',
        '38' => 'Specific double 2, 5', '93'  => '256',
        '39' => 'Specific double 2, 6', '94'  => '346',
        '40' => 'Specific double 3, 4', '95'  => '123',
        '41' => 'Specific double 3, 5', '96'  => '136',
        '42' => 'Specific double 3, 6', '97'  => '145',
        '43' => 'Specific double 4, 5', '98'  => '235',
        '44' => 'Specific double 4, 6', '99'  => '356',
        '45' => 'Specific double 5, 6', '100' => '124',
        '46' => 'Pair 1',               '101' => '146',
        '47' => 'Pair 2',               '102' => '236',
        '48' => 'Pair 3',               '103' => '245',
        '49' => 'Pair 4',               '104' => '456',
        '50' => 'Pair 5',               '105' => '125',
        '51' => 'Pair 6',               '106' => '134',
        '52' => 'Three Odd',            '107' => '156',
        '53' => 'Two Odd One Even',     '108' => '246',
        '54' => 'Two Even One Odd',     '109' => '345',
    ];

    # 番摊投注类型
    public static $betTypeOfFanTan = [
        '0'  => 'Odd',      '21'  => '4 Nim 3',
        '1'  => 'Even',     '22'  => '12 Kwok',
        '2'  => '1 Zheng',  '23'  => '14 Kwok',
        '3'  => '2 Zheng',  '24'  => '23 Kwok',
        '4'  => '3 Zheng',  '25'  => '34 Kwok',
        '5'  => '4 Zheng',  '26'  => '1 Tong 23',
        '6'  => '1 Fan',    '27'  => '1 Tong 24',
        '7'  => '2 Fan',    '28'  => '1 Tong 34',
        '8'  => '3 Fan',    '29'  => '2 Tong 13',
        '9'  => '4 Fan',    '30'  => '2 Tong 14',
        '10' => '1 Nim 2',  '31'  => '2 Tong 34',
        '11' => '1 Nim 3',  '32'  => '3 Tong 12',
        '12' => '1 Nim 4',  '33'  => '3 Tong 14',
        '13' => '2 Nim 1',  '34'  => '3 Tong 24',
        '14' => '2 Nim 3',  '35'  => '4 Tong 12',
        '15' => '2 Nim 4',  '36'  => '4 Tong 13',
        '16' => '3 Nim 1',  '37'  => '4 Tong 23',
        '17' => '3 Nim 2',  '38'  => '123 Chun',
        '18' => '3 Nim 4',  '39'  => '124 Chun',
        '19' => '4 Nim 1',  '40'  => '134 Chun',
        '20' => '4 Nim 2',  '41'  => '234 Chun'
    ];

    # 轮盘投注类型
    public static $betTypeOfRoulette = [
        '0~36' =>'0~36', '97'  => '0,1,2',
        '37' => '0,1',   '98'  => '0,2,3',
        '38' => '0,2',   '99'  => '1,2,3',
        '39' => '0,3',   '100' => '4,5,6',
        '40' => '1,2',   '101' => '7,8,9',
        '41' => '1,4',   '102' => '10,11,12',
        '42' => '2,3',   '103' => '13,14,15',
        '43' => '2,5',   '104' => '16,17,18',
        '44' => '3,6',   '105' => '19,20,21',
        '45' => '4,5',   '106' => '22,23,24',
        '46' => '4,7',   '107' => '25,26,27',
        '47' => '5,6',   '108' => '28,29,30',
        '48' => '5,8',   '109' => '31,32,33',
        '49' => '6,9',   '110' => '34,35,36',
        '50' => '7,8',   '111' => '1,2,4,5',
        '51' => '7,10',  '112' => '2,3,5,6',
        '52' => '8,9',   '113' => '4,5,7,8',
        '53' => '8,11',  '114' => '5,6,8,9',
        '54' => '9,12',  '115' => '7,8,10,11',
        '55' => '10,11', '116' => '8,9,11,12',
        '56' => '10,13', '117' => '10,11,13,14',
        '57' => '11,12', '118' => '11,12,14,15',
        '58' => '11,14', '119' => '13,14,16,17',
        '59' => '12,15', '120' => '14,15,17,18',
        '60' => '13,14', '121' => '16,17,19,20',
        '61' => '13,16', '122' => '17,18,20,21',
        '62' => '14,15', '123' => '19,20,22,23',
        '63' => '14,17', '124' => '20,21,23,24',
        '64' => '15,18', '125' => '22,23,25,26',
        '65' => '16,17', '126' => '23,24,26,27',
        '66' => '16,19', '127' => '25,26,28,29',
        '67' => '17,18', '128' => '26,27,29,30',
        '68' => '17,20', '129' => '28,29,31,32',
        '69' => '18,21', '130' => '29,30,32,33',
        '70' => '19,20', '131' => '31,32,34,35',
        '71' => '19,22', '132' => '32,33,35,36',
        '72' => '20,21', '133' => '1,2,3,4,5,6',
        '73' => '20,23', '134' => '4,5,6,7,8,9',
        '74' => '21,24', '135' => '7,8,9,10,11,12',
        '75' => '22,23', '136' => '10,11,12,13,14,15',
        '76' => '22,25', '137' => '13,14,15,16,17,18',
        '77' => '23,24', '138' => '16,17,18,19,20,21',
        '78' => '23,26', '139' => '19,20,21,22,23,24',
        '79' => '24,27', '140' => '22,23,24,25,26,27',
        '80' => '25,26', '141' => '25,26,27,28,29,30',
        '81' => '25,28', '142' => '28,29,30,31,32,33',
        '82' => '26,27', '143' => '31,32,33,34,35,36',
        '83' => '26,29', '144' => '1st 12 (1~12)',
        '84' => '27,30', '145' => '2nd 12 (13~24)',
        '85' => '28.29', '146' => '3rd 12 (25~36)',
        '86' => '28,31', '147' => '1st Row (1~34)',
        '87' => '29,30', '148' => '2nd Row (2~35)',
        '88' => '29,32', '149' => '3rd Row (3~36)',
        '89' => '30,33', '150' => '1~18 (Small)',
        '90' => '31,32', '151' => '19~36 (Big)',
        '91' => '31,34', '152' => 'Odd',
        '92' => '32,33', '153' => 'Even',
        '93' => '32,35', '154' => 'Red',
        '94' => '33,36', '155' => 'Black',
        '95' => '34,35', '156' => '0,1,2,3',
        '96' => '35,36',
    ];

    # 轮盘投注类型
    public static $betTypeOfMoneyWheel = [
        '0'  => '1',
        '1'  => '2',
        '2'  => '9',
        '3'  => '16',
        '4'  => '24',
        '5'  => '50 Gold',
        '6'  => '50 Black',
        '7'  => 'Fish',
        '8'  => 'Prawn',
        '9'  => 'Crab',
        '10' => 'Coin',
        '11' => 'Gourd',
        '12' => 'Rooster',
    ];

    # 轮盘投注类型
    public static $betTypeOfTip = [
        '254'  => 'tip',
    ];
}