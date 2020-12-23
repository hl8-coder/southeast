<?php
namespace App\GamePlatforms\Tools;

use App\Models\Game;
use App\Models\GameBetDetail;
use App\Models\GamePlatformUser;
use App\Repositories\GamePlatformTransferDetailRepository;

class EBETTool extends Tool
{
    protected $currencies = [
        'VND' => 'VND',
        'THB' => 'THB',
    ];

    protected $languages = [
        'zh-CN' => 'zh_cn',
        'vi-VN' => 'vi_vn',
        'en_us' => 'en_us',
        'th'    => 'th_th',
    ];

    protected $message = [
        'login_url' => 'http://hl8vnd.drfs.live/h5/c878f8',
    ];

    protected $errors = [
        '-1'    =>  'Recharge record no exist',
        '0'     =>  'Recharging',
        '201'   =>  'Repeated rechargeId',
        '202'   =>  'Channel no exist',
        '203'   =>  "RechargeId can't null",
        '204'   =>  "Recharge money can't null",
        '205'   =>  'Recharge money too small',
        '206'   =>  'SeqNo not exists',
        '207'   =>  'Refund Money Inconsistent',
        '208'   =>  'Record is not existed',
        '209'   =>  'Check record already refund',
        '401'   =>  'User or password error',
        '410'   =>  'Token error',
        '500'   =>  'Server error',
        '505'   =>  'Channel under maintenance',
        '4003'  =>  'System busy, try back later',
        '4004'  =>  'SW timeout',
        '4025'  =>  'Parameter error',
        '4026'  =>  'Signature error',
        '4027'  =>  'IP is not authorized',
        '4028'  =>  'Trial user can’t recharge credit',
        '4029'  =>  'Function disable',
        '4030'  =>  'Data type error',
        '4037'  =>  'User not existence',
        '4038'  =>  'Frequent request',
        '4202'  =>  'Visit count no enough',
        '5001'  =>  'Recharge failed',
        '5002'  =>  'Not enough cash',
        '5003'  =>  'Sub channel no exist',
        '5004'  =>  'SW does not have this feature',
        '5005'  =>  'Wallet not enabled',
        '5006'  =>  'Unable to extract in the game',
    ];

    /**
     * 获取token
     *
     * @param GamePlatformUser $platformUser
     * @return string
     */
    public function getAccessToken(GamePlatformUser $platformUser)
    {
        return md5($platformUser->name . strtolower($this->platform->code) . md5($platformUser->password));
    }

    /**
     * 签名
     *
     * @param  mixed    $data
     * @return string
     */
    public function sign($data, $prefix)
    {
        if (!is_array($data)) {
            $data = [$data];
        }
        $prefixPrivateKey = $this->getPrefixPrivateKey($prefix);
        \openssl_sign(implode('', $data), $signature, $prefixPrivateKey, 'md5');

        return \base64_encode($signature);
    }

    /**
     * 签证签名
     *
     * @param mixed     $data       原始数据
     * @param string    $signature  签名后字符串
     * @return bool
     */
    public function checkSign($data, $signature, $prefix)
    {
        if (is_array($data)) {
            $data = implode('', $data);
        }
        $prefixPublicKey = $this->getPrefixPublicKey($prefix);
        $result =  \openssl_verify($data, base64_decode($signature), $prefixPublicKey, 'md5');

        return 1 === $result;
    }

    /**
     * 获取第三方会员名称
     *
     * @param   GamePlatformUser $platformUser
     * @return  mixed|string
     */
    public function getPlatformUserName(GamePlatformUser $platformUser)
    {
        $platformUserName = app()->isLocal() ? 'test' . '_' .  $platformUser->name : $platformUser->name;

        $platformUser->updateName($platformUserName);

        return $platformUserName;
    }

    public function transferBetDetail($data)
    {
        $betDetails = [];

        $now = now();

        $count = 0;
        foreach ($data as $originBetDetails) {
            $count += $originBetDetails['count'];
            foreach ($originBetDetails['betHistories'] as $key => $record) {

                if (!$game = Game::findByPlatformAndCode($this->platform->code, $record['gameType'])) {
                    continue;
                }

                if (!$user = $this->getUser($record['username'])) {
                    continue;
                }

                $bet = $this->getAvailableBet($record['gameType'], $record['bet'], $record['betMap'], $record['judgeResult']);
                $niuniuWithHoldingTotal = isset($record['niuniuWithHoldingTotal']) ? $record['niuniuWithHoldingTotal'] : 0;
                $stake = $record['bet'] + $niuniuWithHoldingTotal;
                $bet = $bet + $niuniuWithHoldingTotal;
                $prize = $record['payoutWithoutholding'] + $niuniuWithHoldingTotal;

                $betDetails[] = [
                    'platform_code'         => $this->platform->code,
                    'product_code'          => $game->product_code,
                    'platform_currency'     => $user->currency,
                    'order_id'              => $record['betHistoryId'],
                    'game_code'             => $record['gameType'],
                    'game_type'             => $game->type,
                    'game_name'             => $game->getEnName(),
                    'user_id'               => $user->id,
                    'user_name'             => $user->name,
                    'issue'                 => $record['roundNo'],
                    'bet_at'                => date('Y-m-d H:i:s', $record['createTime']),
                    'payout_at'             => date('Y-m-d H:i:s', $record['payoutTime']),
                    'stake'                 => $stake,
                    'bet'                   => $bet,
                    'prize'                 => $prize,
                    'profit'                => $record['balance'],
                    'user_currency'         => $user->currency,
                    'user_stake'            => $stake,
                    'user_bet'              => $bet,
                    'user_prize'            => $prize,
                    'user_profit'           => $record['balance'],
                    'platform_profit'       => -1 * $record['balance'],
                    'platform_status'       => !empty($record['betMap']) ? GameBetDetail::PLATFORM_STATUS_BET_SUCCESS : GameBetDetail::PLATFORM_STATUS_BET_FAIL,
                    'bet_info'              => $this->getBetInfo($record),
                    'win_info'              => $this->getWinInfo($record),
                    'available_bet'         => $bet,
                    'available_profit'      => -1 * $record['balance'],
                    'created_at'            => $now,
                    'updated_at'            => $now,
                ];
            }
        }

        if (!empty($betDetails)) {
            # 添加总的投注明细表
            batch_insert('game_bet_details', $betDetails, true);
        }

        return [
            'origin_total'   => $count,
            'transfer_total' => count($betDetails),
        ];
    }

    public function getBetInfo($record)
    {
        $game = isset(static::$gameTypes[$record['gameType']]) ? static::$gameTypes[$record['gameType']] : '';

        $betMap = collect($record['betMap'])->pluck(['betType'])->toArray();

        $bets = transfer_array_show_value($betMap, static::$betTypes);
        $betInfo = $game . ' : ' . implode(',', $bets);

        return $betInfo;
    }

    public function getWinInfo($record)
    {
        $game = isset(static::$gameTypes[$record['gameType']]) ? static::$gameTypes[$record['gameType']] : '';

        $lotteryResult = $game .' Lottery Result : ';

        # 投注详情
        switch ($record['gameType']) {
            case 1: #  百家乐
            case 7: #  区块链百家乐
                $bankerCards = transfer_array_show_value($record['bankerCards'], static::$cardTypes);
                $playerCards = transfer_array_show_value($record['playerCards'], static::$cardTypes);
                $lotteryResult .= ' Banker Cards ' . implode(',', $bankerCards) . '; Player Cards ' . implode(',', $playerCards) . '.';
                $judge = transfer_array_show_value($record['judgeResult'], static::$betTypes);
                $lotteryResult .= 'Judge Result: ' . implode(',', $judge) . '.';
                break;
            case 2: #  龙虎
                $dragonCard = transfer_show_value($record['dragonCard'], static::$cardTypes);
                $tigerCard  = transfer_show_value($record['tigerCard'], static::$cardTypes);
                $lotteryResult .= ' Dragon Cards ' . $dragonCard . '; Tiger Cards ' . $tigerCard . '.';
                $judge = transfer_array_show_value($record['judgeResult'], static::$betTypes);
                $lotteryResult .= 'Judge Result: ' . implode(',', $judge) . '.';
                break;
            case 3: #  骰宝
                $lotteryResult .= ' All Dices ' . implode(',', $record['allDices']) ;
                $judge = transfer_array_show_value($record['judgeResult'], static::$betTypes);
                $lotteryResult .= 'Judge Result: ' . implode(',', $judge) . '.';
                break;
            case 4: #  轮盘
                $lotteryResult .= ' Number ' . $record['number'];
                $judge = transfer_array_show_value($record['judgeResult'], static::$betTypes);
                $lotteryResult .= 'Judge Result: ' . implode(',', $judge) . '.';
                break;

            case 8: #  牛牛
                $judge = transfer_array_show_value($record['judgeResult'], static::$cardTypes);
                $lotteryResult = 'Judge Result: ' . implode(',', $judge) . '.';
                break;
            case 5: #  水果机
            case 6: #  试玩水果机
            default:
                $lotteryResult = '';
                break;
        }

        return $lotteryResult;
    }

    /**
     * 解析回复
     *
     * @param $response
     * @param $method
     * @return mixed|\SimpleXMLElement|string
     * @throws
     */
    public function checkResponse($response, $method, $data)
    {
        $result = get_response_body($response, 'json');

        $statusCode = $response->getStatusCode();

        $this->responseLog($method, $statusCode, $result);

        if ($statusCode >= 300) {
            error_response(500, 'request error.');
        } else {
            switch ($result['status']) {

                case 200:
                    if ('register' == $method) {
                        return '';
                    } elseif ('balance' == $method) {
                        $wallets = $result['results'][0]['wallet'];
                        $wallet  = collect($wallets)->where('typeId', $this->platform->message['wallet_type'])->first();
                        return $wallet['money'];
                    } elseif ('pull' == $method) {
                        return $result;
                    } elseif ('transfer' == $method || 'check' == $method) {
                        GamePlatformTransferDetailRepository::setPlatformOrderNo($data['detail'], $result['rechargeReqId']);
                        return GamePlatformTransferDetailRepository::setSuccess($data['detail']);
                    } elseif ('kick_out' == $method) {
                        return true;
                    }
                    break;
                case 401:
                    if ('register' == $method) {
                        return '';
                    }
                case 0: # 充值中
                case 4003: # 系统繁忙，请稍后再试
                    if ('transfer' == $method || 'check' == $method) {
                        return GamePlatformTransferDetailRepository::setWaiting($data['detail'], $this->getError($result['status']));
                    }
                default:
                    if ('transfer' == $method || 'check' == $method) {
                        return GamePlatformTransferDetailRepository::setFail($data['detail'], $this->getError($result['status']));
                    }
                    error_response($result['status'], $this->getError($result['status']));
                    break;
            }
        }
    }

    # 获取有效流水
    public function getAvailableBet($gameType, $bet, $betDetails, $winResult)
    {
        switch ($gameType) {
            case '1': # 百家乐
            case '7': # 百家乐
                if (in_array('68', $winResult)) {
                    foreach ($betDetails as $betDetail) {
                        if ('60' == $betDetail['betType'] || '80' == $betDetail['betType']) {
                            $bet -= $betDetail['betMoney'];
                        }
                    }
                }
                break;
        }

        return $bet;
    }

    # 游戏类型
    public static $gameTypes = [
        '1'  => 'Baccarat',
        '2'  => 'Dragon Tiger',
        '3'  => 'Sic-Bo',
        '4'  => 'Roulette',
        '5'  => 'Slot',
        '6'  => 'Tips',
        '7'  => 'Block Chain Baccarat',
        '8'  => 'NiuNiu',
    ];

    public static $betTypes = [
        '60'  => 'player',
        '66'  => 'player pair',
        '68'  => 'tie',
        '80'  => 'banker',
        '88'  => 'banker pair',
        '86'  => 'bankerLucky6',
        '81'  => 'bankerDragonBonus',
        '61'  => 'playerDragonBonus',
        '70'  => 'big',
        '71'  => 'small',
        '82'  => 'bankerOdd',
        '83'  => 'bankerEven',
        '62'  => 'playerOdd',
        '63'  => 'playerEven',
        '10'  => 'dragon',
        '11'  => 'tiger',
        '100' => 'odd',
        '101' => 'even',
        '102' => 'big',
        '103' => 'small',
        '104' => 'pair 1',
        '105' => 'pair 2',
        '106' => 'pair 3',
        '107' => 'pair 4',
        '108' => 'pair 5',
        '109' => 'pair 6',
        '110' => 'triple 1',
        '111' => 'triple 2',
        '112' => 'triple 3',
        '113' => 'triple 4',
        '114' => 'triple 5',
        '115' => 'triple 6',
        '116' => 'triple All',
        '117' => '4 point',
        '118' => '5 point',
        '119' => '6 point',
        '120' => '7 point',
        '121' => '8 point',
        '125' => '9 point',
        '126' => '10 point',
        '127' => '11 point',
        '128' => '12 point',
        '129' => '13 point',
        '130' => '14 point',
        '131' => '15 point',
        '132' => '16 point',
        '133' => '17 point',
        '134' => 'single point 1',
        '135' => 'single point 2',
        '136' => 'single point 3',
        '137' => 'single point 4',
        '138' => 'single point 5',
        '139' => 'single point 6',
        '140' => 'combinations 1-2',
        '141' => 'combinations 1-3',
        '142' => 'combinations 1-4',
        '143' => 'combinations 1-5',
        '144' => 'combinations 1-6',
        '145' => 'combinations 2-3',
        '146' => 'combinations 2-4',
        '147' => 'combinations 2-5',
        '148' => 'combinations 2-6',
        '149' => 'combinations 3-4',
        '150' => 'combinations 3-5',
        '151' => 'combinations 3-6',
        '152' => 'combinations 4-5',
        '153' => 'combinations 4-6',
        '154' => 'combinations 5-6',
        '155' => 'two same number',
        '156' => 'three different numbers',
        '200' => 'straight up',
        '201' => 'split bet',
        '202' => 'street bet',
        '203' => 'corner bet',
        '204' => 'Three Numbers 3 numbers bet',
        '205' => 'Four Numbers 4 numbers bet',
        '206' => 'line bet',
        '207' => 'column bet',
        '208' => 'dozen bet',
        '209' => 'red',
        '210' => 'black',
        '211' => 'odd',
        '212' => 'even',
        '213' => 'big',
        '214' => 'small',
        '301' => 'player 1 equal',
        '302' => 'player 1 double',
        '303' => 'player 2 equal',
        '304' => 'player 2 double',
        '305' => 'player 3 equal',
        '306' => 'player 3 double',
    ];

    public static $cardTypes = [
        '0'  => '2 club',   '13' => '2 diamond',  '26' => '2 heart',  '39' => '2 spades',
        '1'  => '3 club',   '14' => '3 diamond',  '27' => '3 heart',  '40' => '3 spades',
        '2'  => '4 club',   '15' => '4 diamond',  '28' => '4 heart',  '41' => '4 spades',
        '3'  => '5 club',   '16' => '5 diamond',  '29' => '5 heart',  '42' => '5 spades',
        '4'  => '6 club',   '17' => '6 diamond',  '30' => '6 heart',  '43' => '6 spades',
        '5'  => '7 club',   '18' => '7 diamond',  '31' => '7 heart',  '44' => '7 spades',
        '6'  => '8 club',   '19' => '8 diamond',  '32' => '8 heart',  '45' => '8 spades',
        '7'  => '9 club',   '20' => '9 diamond',  '33' => '9 heart',  '46' => '9 spades',
        '8'  => '10 club',  '21' => '10 diamond', '34' => '10 heart', '47' => '10 spades',
        '9'  => 'J club',   '22' => 'J diamond',  '35' => 'J heart',  '48' => 'J spades',
        '10' => 'Q club',   '23' => 'Q diamond',  '36' => 'Q heart',  '49' => 'Q spades',
        '11' => 'K club',   '24' => 'K diamond',  '37' => 'K heart',  '50' => 'K spades',
        '12' => 'A club',   '25' => 'A diamond',  '38' => 'A heart',  '51' => 'A spades',
    ];

    public static $localRsaKey = [
        'vnd_' => [
            'rsa_our_private_key'   => '-----BEGIN PRIVATE KEY-----
MIIBVgIBADANBgkqhkiG9w0BAQEFAASCAUAwggE8AgEAAkEAhta2CJ/RMAkxChx6
2m9cnfzGf4n97A0GXDYz1/wNYcAiJvb7E8GKKCgbAS5cIQ5LmL2/tS1Q4q2hNkPb
osATewIDAQABAkAPirwcrl8sTELsyW+XsfJY+4Pdu4gbJz0ub8j2AkWAmLwCYjxX
r+THSSmWYAm+DXmKyxVw57QvTmk+/ETPelHRAiEAudzgG025QDxrC/QVKtYKccoT
jfykX10GKMXolCm0PXcCIQC5uLIhYsvh1zQdxlmlZRGOSvMdufy9gGo/Y8QjwxcL
HQIhAJe+LvHbuP0q1rLBqm54pbpVIzXvKDv7dMXhHouoqNDtAiEAhLKMxAH9Pu4u
1J9maiCevJacwr6i8RuRzp0QBaVdD5kCIQCDoSmL+LEQFAZtEjPh1MyT5WOBnx3b
otUqkwwE1ErTnA==
-----END PRIVATE KEY-----',
            'rsa_public_key'        => '-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAIbWtgif0TAJMQocetpvXJ38xn+J/ewN
Blw2M9f8DWHAIib2+xPBiigoGwEuXCEOS5i9v7UtUOKtoTZD26LAE3sCAwEAAQ==
-----END PUBLIC KEY-----',
        ],
        'thb_' => [
            'rsa_our_private_key'   => '-----BEGIN PRIVATE KEY-----
MIIBVAIBADANBgkqhkiG9w0BAQEFAASCAT4wggE6AgEAAkEAmBNdSHspmYD/hHE3
9Paz3hhCPG8ls1wH73TF+cKCmBBK5xSwmDbMz3eACAPnEkNLe9/eAZI5AW65hgqZ
4osS9QIDAQABAkAmCXHjlyqogmNmtQrR8oK6okau5v3/Bp3VftelyMjxT37/jSlw
hSKXTFn7AJvCuTSSTacS39rr1AZ7aptzWB9hAiEA0wRmeHZPD5WoqasQDW7Vw0p0
u0ntYFuBCeApBd91IYsCIQC4fmocrpvaRHG84rjF+I8G04342sS9epdRM+t3Tt4t
fwIgNMXW8q1z3EvrYHNdkl5zq2GFjAlTClQYE2YyHDvkST0CIFA1/dJcg7wAl+aE
N8syhpR0M7xm+LRccR9H0G69plDnAiEAi4Ot55JPzutYcI8z0WI5czt1KiZTvMsr
Hes5dLTVTlg=
-----END PRIVATE KEY-----',
            'rsa_public_key'        => '-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAJgTXUh7KZmA/4RxN/T2s94YQjxvJbNc
B+90xfnCgpgQSucUsJg2zM93gAgD5xJDS3vf3gGSOQFuuYYKmeKLEvUCAwEAAQ==
-----END PUBLIC KEY-----',
        ]
    ];

    public static $productRsaKey = [
        'vnd_' => [
            'rsa_our_private_key'   => '-----BEGIN PRIVATE KEY-----
MIIBVgIBADANBgkqhkiG9w0BAQEFAASCAUAwggE8AgEAAkEAhta2CJ/RMAkxChx6
2m9cnfzGf4n97A0GXDYz1/wNYcAiJvb7E8GKKCgbAS5cIQ5LmL2/tS1Q4q2hNkPb
osATewIDAQABAkAPirwcrl8sTELsyW+XsfJY+4Pdu4gbJz0ub8j2AkWAmLwCYjxX
r+THSSmWYAm+DXmKyxVw57QvTmk+/ETPelHRAiEAudzgG025QDxrC/QVKtYKccoT
jfykX10GKMXolCm0PXcCIQC5uLIhYsvh1zQdxlmlZRGOSvMdufy9gGo/Y8QjwxcL
HQIhAJe+LvHbuP0q1rLBqm54pbpVIzXvKDv7dMXhHouoqNDtAiEAhLKMxAH9Pu4u
1J9maiCevJacwr6i8RuRzp0QBaVdD5kCIQCDoSmL+LEQFAZtEjPh1MyT5WOBnx3b
otUqkwwE1ErTnA==
-----END PRIVATE KEY-----',
            'rsa_public_key'        => '-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAIbWtgif0TAJMQocetpvXJ38xn+J/ewN
Blw2M9f8DWHAIib2+xPBiigoGwEuXCEOS5i9v7UtUOKtoTZD26LAE3sCAwEAAQ==
-----END PUBLIC KEY-----',
        ],
        'thb_' => [
            'rsa_our_private_key'   => '-----BEGIN PRIVATE KEY-----
MIIBVgIBADANBgkqhkiG9w0BAQEFAASCAUAwggE8AgEAAkEAx3CK8T+AQ/Ohs0O0
aBSrNORR1co338cbPoA3DDQkfDxRf4S4QEeI0Gd+55BqfVJ63KGoXEk6Ejl4bt2P
pF6CWwIDAQABAkAjYsiEPWwYomWSVPTxcpld7RTIBfUb80vWLxGVTLscf5cUBHh9
BDohwAmDg2wZqIvYvGyOFNRaLuE2MDyCAC9BAiEA5h2Din+UNKdatAvB4yCEMC9y
G3mk27yc0C3L22dsV/MCIQDd365MzmwxPmbPLwGGolyRoJY0JoIld58MW877FGvt
+QIhAIVe1b6P8vYRiSCRL+7UCcljtl72QfaZesmtwVmIt+LBAiEAuX5TU6VUcMsR
2u8aoHvHVnzidtEnB7n9f7CV1/YX0ckCIQCatTJZkrZv3at/qp03sQiKZO0g2WXx
Mn8HlWg/MVKhTQ==
-----END PRIVATE KEY-----',
            'rsa_public_key'        => '-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAMdwivE/gEPzobNDtGgUqzTkUdXKN9/H
Gz6ANww0JHw8UX+EuEBHiNBnfueQan1SetyhqFxJOhI5eG7dj6ReglsCAwEAAQ==
-----END PUBLIC KEY-----',
        ]
    ];

    public function getPrefixPrivateKey($prefix)
    {
        if (app()->isLocal()) {
            return static::$localRsaKey[$prefix]['rsa_our_private_key'];
        } else {
            return static::$productRsaKey[$prefix]['rsa_our_private_key'];
        }
    }

    public function getPrefixPublicKey($prefix)
    {
        if (app()->isLocal()) {
            return static::$localRsaKey[$prefix]['rsa_public_key'];
        } else {
            return static::$productRsaKey[$prefix]['rsa_public_key'];
        }    }
}