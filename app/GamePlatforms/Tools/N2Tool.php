<?php
namespace App\GamePlatforms\Tools;

use App\Models\Game;
use App\Models\GameBetDetail;
use App\Models\GamePlatformUser;
use App\Repositories\GamePlatformTransferDetailRepository;

class N2Tool extends Tool
{

    protected $currencies = [
        'VND' => '7041',
//        'VND' => '1111',
        'THB' => '764',
    ];

    protected $languages = [
        'vi-VN' => 'vi',
        'en-US' => 'en',
        'th'    => 'th',
        'zh-CN' => 'zh-CN',
    ];

    protected $message = [];

    protected $errors = [
        '001' => 'ERR_INVALID_REQ',
        '002' => 'ERR_INVALID_IP',
        '003' => 'ERR_SYSTEM_OPR',
        '101' => 'ERROR_INVALID_ACCOUNT_ID',
        '102' => 'ERROR_ALREADY_LOGIN',
        '103' => 'ERROR_DATABASE_ERROR',
        '104' => 'ERROR_ACCOUNT_SUSPENDED',
        '105' => 'ERROR_INVALID_CURRENCY',
        '201' => 'ERR_INVALID_REQ',
        '202' => 'ERR_DB_OPEATION',
        '203' => 'ERR_INVALID_CLIENT',
        '204' => 'ERR_EXCEED_AMOUNT',
        '205' => 'ERR_INVALID_VENDOR',
        '306' => 'ERR_INVALID_CURRENCYID',
        '401' => 'ERR_DUPLICATE_REFNO',
        '402' => 'ERR_INVALID_PREFIX|ERR_INVALID_IP',
        '403' => 'ERR_INVALID_AMOUNT',
        '404' => 'ERR_ILLEGAL_DECIMAL',
        '501' => 'ERR_INVALID_ACODE',
        '502' => 'ERR_INVALID_BEGINDATE',
        '503' => 'ERR_INVALID_ENDDATE',
        '504' => 'ERR_INVALID_ENDDATELOWBEGINDATE',
        '505' => 'ERR_INVALID_RETURN_ACODE',
        '506' => 'ERR_DATA_ SNAPSHOT',
        '507' => 'ERR_INVALID_TIMEZONE',
        '601' => 'ERR_INVALID_LOGIN_URL',
        '602' => 'ERR_INVALID_AUTO_LOGIN_URL',
        '611' => 'ERR_INVALID_USER',
        '612' => 'ERR_BLACKLISTED_USER',
        '613' => 'ERR_LOGIN_DENIED',
        '614' => 'ERR_SIGN_FAILURE',
        '615' => 'ERR_REQUEST_EXPIRED',
        '616' => 'ERR_SYSTEM_ERROR',
        '701' => 'ERR_DBCHECK_CONTROL',
        '801' => 'ERR_XML_INPUT',
        '903' => 'ERR_WINDOW_HOUR',
    ];

    /**
     * 获取uuid
     *
     * @param GamePlatformUser $platformUser
     * @return string
     */
    public function getUuid(GamePlatformUser $platformUser)
    {
        return md5($platformUser->name . strtolower($this->platform->code) . md5($platformUser->password));
    }

    /**
     * 获取请求id
     *
     * @param $prefix
     * @return string
     * @throws \Exception
     */
    public function getRequestId($prefix)
    {
        return $prefix . time() .random_int(10000, 99999);
    }


    public function transferBetDetail($originBetDetails)
    {
        $betDetails = [];

        $key = 0;
        $now = now();
        foreach ($originBetDetails as $details) {
            if (!$game = Game::findByPlatformAndCode($this->platform->code, $details['code'])) {
                continue;
            }
            foreach ($details as $set) {
                if (is_array($set)) {
                    $winInfo = $this->getWinInfo($details['code'], $set[1]);
                    # 会员
                    foreach ($set[0] as $bet) {
                        if (!$user = $this->getUser($bet['login'])) {
                            continue;
                        }

                        $betDetails[$key] = [
                            'platform_code'         => $this->platform->code,
                            'product_code'          => $game->product_code,
                            'platform_currency'     => $user->currency,
                            'order_id'              => $user->id . '-' . $set['id'],
                            'game_code'             => $game->code,
                            'game_type'             => $game->type,
                            'game_name'             => $game->getEnName(),
                            'user_id'               => $user->id,
                            'user_name'             => $user->name,
                            'issue'                 => '',
                            'stake'                 => $bet['bet_amount'],
                            'prize'                 => $bet['payout_amount'],
                            'profit'                => $bet['hold'],
                            'bet_at'                => $set['startdate'],
                            'payout_at'             => $set['enddate'],
                            'user_currency'         => $user->currency,
                            'user_stake'            => $bet['bet_amount'],
                            'user_prize'            => $bet['payout_amount'],
                            'user_profit'           => $bet['hold'],
                            'after_balance'         => 0,
                            'platform_profit'       => -1 * $bet['hold'],
                            'platform_status'       => GameBetDetail::PLATFORM_STATUS_BET_SUCCESS,
                            'available_profit'      => -1 * $bet['hold'],
                            'win_info'              => $winInfo,
                            'created_at'            => $now,
                            'updated_at'            => $now,
                        ];

                        # 投注信息
                        $tempBetInfo = [];
                        $betAmount = $bet['bet_amount'];
                        foreach ($bet as $betDetail) {
                            if (is_array($betDetail)) {
                                $tempBetInfo[] = $this->getBetInfo($details['code'], $betDetail);
                                # 获取有效投注
                                if (isset($set[2])) {
                                    $betAmount = $this->getAvailableBet($details['code'], $betAmount, $betDetail, $set[2]['result']);
                                }
                            }
                        }
                        $betDetails[$key]['bet_info']       = implode('|', $tempBetInfo);
                        $betDetails[$key]['bet']            = $betAmount;
                        $betDetails[$key]['user_bet']       = $betAmount;
                        $betDetails[$key]['available_bet']  = $betAmount;
                        $key++;
                    }
                }

            }
        }

        if (!empty($betDetails)) {
            # 添加总的投注明细表
            batch_insert('game_bet_details', $betDetails, true);
        }

        return [
            'origin_total'   => count($betDetails),
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
    public function checkResponse($response, $method, $data)
    {
        $statusCode = $response->getStatusCode();
        $body = (string)$response->getBody();
        $this->responseLog($method, $statusCode, $body);

        switch ($method) {
            case 'balance':
                $result = $this->analysisBalanceXml($body);
                break;
            case 'transfer':
                $result = $this->analysisTransferXml($body);
                break;
            case 'deposit_confirm':
                $result = $this->analysisDepositConfirmXml($body);
                break;
            case 'pull':
                $result = $this->analysisPullXml($body);
                break;
        }

        if ($statusCode >= 300) {
            error_response(422, 'Game exception.');
        } else {
            switch ($result['status']) {
                case 0:
                    if ('balance' == $method) {
                        return (float)$result['balance'];
                    } elseif ('transfer' == $method) { # 如果转账成功，发起检查订单状态
                        $detail = $data['detail'];
                        if ($result['refno'] == $this->getTransferOrderNo($detail->order_no)) {
                            GamePlatformTransferDetailRepository::setPlatformOrderNo($detail, $result['paymentid']);
                            if ($detail->isIncome()) {
                                $detail->update(['bet_order_id' => $result['id']]);
                                return GamePlatformTransferDetailRepository::setWaiting($detail);
                            } else {
                                return GamePlatformTransferDetailRepository::setSuccess($detail);
                            }
                        } else {
                            return GamePlatformTransferDetailRepository::setWaitManualConfirm($detail, 'refno not match.');
                        }
                    } elseif ('pull' == $method) {
                        return $result['list'];
                    } elseif ('deposit_confirm' == $method) {
                        $detail = $data['detail'];
                        if ($result['paymentid'] == $detail->platform_order_no) {
                            return GamePlatformTransferDetailRepository::setSuccess($detail);
                        } else {
                            return GamePlatformTransferDetailRepository::setWaitManualConfirm($detail, 'paymentid not match.');
                        }
                    }

                    break;
                case 1:
                    if ('balance' == $method) { # 在线
                        return (float)$result['balance'];
                    }
                    break;
                case 003: # 无效操作
                case 105: # 无效货币
                case 201: # 无效请求
                case 202: # 数据库操作错误
                case 203: # 非验证客户
                    if ('balance' == $method) { # 在线
                        return 0;
                    }
                case 204: # 超出限定金额
                case 205: # 非验证商家
                case 401: # 重复参考号码
                case 403: # 无效金额
                case 404: # 无效小数点
                case 801: # XML输入错误
                default:
                    if ('transfer' == $method || 'deposit_confirm' == $method) {
                        return GamePlatformTransferDetailRepository::setFail($data['detail'], $this->getError($result['status']));
                    }
                    error_response(422, $this->getError($result['status']));
                    break;
            }
        }
    }

    /**
     * 解析登录回调内容
     *
     * @param $xml
     * @return array
     */
    public function analysisLoginCallBackXml($xml)
    {
        $result = [];
        $data = str_replace('utf-16', 'utf-8', $xml);
        $data = simplexml_load_string($data);
        $result[$data->attributes()[0]->getName()] = (string)$data->attributes()[0];
        $element = $data->element;
        $result[$element->attributes()[0]->getName()] = (string)$element->attributes()[0];
        foreach ($element->properties as $property) {
            $result[(string)$property['name']] = (string)$property;
        }
        return $result;
    }

    /**
     * 解析查询余额内容
     *
     * @param $xml
     * @return array
     */
    public function analysisBalanceXml($xml)
    {
        $result = [];
        $data = str_replace('utf-16', 'utf-8', $xml);
        $data = simplexml_load_string($data);
        $result['request_status'] = (string)$data->status;
        $result[$data->children()[1]->attributes()[0]->getName()] = (string)$data->children()[1]->attributes()[0];
        $element = $data->children()[1]->element;
        $result[$element->attributes()[0]->getName()] = (string)$element->attributes()[0];
        foreach ($element->properties as $property) {
            $result[(string)$property['name']] = (string)$property;
        }
        return $result;
    }

    /**
     * 解析查询转账返回内容
     *
     * @param $xml
     * @return array
     */
    public function analysisTransferXml($xml)
    {
        $result = [];
        $data = str_replace('utf-16', 'utf-8', $xml);
        $data = simplexml_load_string($data);
        $result['request_status'] = (string)$data->status;
        $result[$data->children()[1]->attributes()[0]->getName()] = (string)$data->children()[1]->attributes()[0];
        $element = $data->children()[1]->element;
        $result[$element->attributes()[0]->getName()] = (string)$element->attributes()[0];
        foreach ($element->properties as $property) {
            $result[(string)$property['name']] = (string)$property;
        }
        return $result;
    }

    /**
     * 解析充值确认返回内容
     *
     * @param $xml
     * @return array
     */
    public function analysisDepositConfirmXml($xml)
    {
        $result = [];
        $data = str_replace('utf-16', 'utf-8', $xml);
        $data = simplexml_load_string($data);

        $result['request_status'] = (string)$data->status;

        $element = $data->children()[1]->element;
        if ('success' == $result['request_status']) {
            $result[$data->children()[1]->attributes()[0]->getName()] = (string)$data->children()[1]->attributes()[0];
            $result[$element->attributes()[0]->getName()] = (string)$element->attributes()[0];
        }
        foreach ($element->properties as $property) {
            $result[(string)$property['name']] = (string)$property;
        }
        return $result;
    }

    /**
     * 解析拉取报表返回内容
     *
     * @param $xml
     * @return array
     */
    public function analysisPullXml($xml)
    {
        $result = [];
        $xml = str_replace('utf-16', 'utf-8', $xml);
        $data = simplexml_load_string($xml);
        $result['request_status'] = (string)$data->status;
        if ('fail' == $result['request_status']) {
            $element = $data->children()[1]->element;
            foreach ($element->properties as $property) {
                $result[(string)$property['name']] = (string)$property;
            }
        } else {
            $gameInfo = $data->result->gameinfo;
            $result['status'] = 0;
            $result['list'] = $this->analysisXml($gameInfo);
        }

        return $result;
    }

    public function analysisXml(\SimpleXMLElement $simpleXMLElement)
    {
        $result = [];
        foreach ($simpleXMLElement->attributes() as $attr) {
            $result[$attr->getName()] = (string)$attr;
        }

        foreach ($simpleXMLElement->children() as $child) {

            if ($child->children()->count() > 0) {
                $result[] = $this->analysisXml($child);
            } else {
                $result[$child->getName()] = (string)$child;
            }
        }

        return $result;
    }

    # 获取有效流水
    public function getAvailableBet($gameCode, $bet, $betDetail, $winResult)
    {
        switch ($gameCode) {
            case '90091': # 百家乐
                if ('tie' == strtolower($winResult)) {
                    foreach ($betDetail as $key=> $value) {
                        if ('bank' == $key || 'player' == $key) {
                            $bet -= $value;
                        }
                    }
                }
                break;
        }

        return $bet;
    }

    public function getBetInfo($gameCode, $bets)
    {
        $betInfo = '';

        switch ($gameCode) {
            case '90091': # 百家乐
                $betInfo = $this->transferBetInfo($bets, static::$betTypeOfBaccarat);
                break;
            case '90092': # 免拥百家乐
                $betInfo = $this->transferBetInfo($bets, static::$betTypeOfBaccarat);
                break;
            case '50002': # 转盘
                $betInfo = $this->transferBetInfo($bets, static::$betTypeOfRoulette);
                break;
            case '60001': # 骰宝
                $betInfo = $this->transferBetInfo($bets, static::$betTypeOfSicbo);
                break;
            case '51002': # 电子转盘
                $betInfo = $this->transferBetInfo($bets, static::$betTypeOfRoulette);;
                break;
            case '52002': # 手动电子转盘
                $betInfo = $this->transferBetInfo($bets, static::$betTypeOfRoulette);;
                break;
            case '61001': # 电子骰宝
                $betInfo = $this->transferBetInfo($bets, static::$betTypeOfSicbo);
                break;
            case '62001': # 手动电子骰宝
                $betInfo = $this->transferBetInfo($bets, static::$betTypeOfSicbo);
                break;
            case '91091': # 电子百家乐
                $betInfo = $this->transferBetInfo($bets, static::$betTypeOfBaccarat);
                break;
            case '91092': # 电子免拥百家乐
                $betInfo = $this->transferBetInfo($bets, static::$betTypeOfBaccarat);
                break;
            case '110001': # 电子传统 21 点手动
                $betInfo = $this->transferBetInfo($bets, static::$betTypeOfTraditionalBlackjack);
                break;
            case '110002': # 电子淘金 21 点手动
                $betInfo = $this->transferBetInfo($bets, static::$betTypeOfFreeBetBlackjack);
                break;
            case '110003': # 电子换牌 21 点手动
                $betInfo = $this->transferBetInfo($bets, static::$betTypeOfBlackjackSwitch);
                break;
        }

        return $betInfo;
    }

    # 转换投注信息
    public function transferBetInfo($bets, $betTypes)
    {
        $betInfo = [];

        foreach ($bets as $key => $amount) {
            $betInfo[] = (isset($betTypes[$key]) ? $betTypes[$key] : $key) . ':' . $amount;
        }

        return implode(';', $betInfo);
    }

    # 百家乐投注类型
    public static $betTypeOfBaccarat = [
        'bank'          => 'Banker',
        'player'        => 'Player',
        'tie'           => 'Tie',
        'bankdp'        => 'Banker Pair',
        'playerdp'      => 'Player Pair',
        'bankodd'       => 'Banker Odd',
        'bankeven'      => 'Banker Even',
        'playerodd'     => 'Player Odd',
        'playereven'    => 'Player Even',
        'super6'        => 'Bet amount on Super 6',
        'big'           => 'Big',
        'small'         => 'Small',
        'perfectdp'     => 'Perfect Pair',
        'eitherdp'      => 'Either Pair',
        'bankdpc'       => 'Banker Pair Combination',
        'playerdpc'     => 'Player Pair Combination',
        'bankd7'        => 'Dragon 7',
        'playerp8'      => 'Panda 8',
        'bankdb'        => 'Banker Dragon Bonus',
        'playerdb'      => 'Player Dragon Bonus',
        'bankcase'      => 'Banker Natural Hand Win',
        'playercase'    => 'Player Natural Hand Win',
        'bank0'         => 'Banker Total 0',
        'bank1'         => 'Banker Total 1',
        'bank2'         => 'Banker Total 2',
        'bank3'         => 'Banker Total 3',
        'bank4'         => 'Banker Total 4',
        'bank5'         => 'Banker Total 5',
        'bank6'         => 'Banker Total 6',
        'bank7'         => 'Banker Total 7',
        'bank8'         => 'Banker Total 8',
        'bank9'         => 'Banker Total 9',
        'player0'       => 'Player Total 0',
        'player1'       => 'Player Total 1',
        'player2'       => 'Player Total 2',
        'player3'       => 'Player Total 3',
        'player4'       => 'Player Total 4',
        'player5'       => 'Player Total 5',
        'player6'       => 'Player Total 6',
        'player7'       => 'Player Total 7',
        'player8'       => 'Player Total 8',
        'player9'       => 'Player Total 9',
    ];

    # 转盘投注类型
    public static $betTypeOfRoulette = [
        'chip0' => 0,
        'chip1' => 1,
        'chip2' => 2,
        'chip3' => 3,
        'chip4' => 4,
        'chip5' => 5,
        'chip6' => 6,
        'chip7' => 7,
        'chip8' => 8,
        'chip9' => 9,
        'chip10' => 10,
        'chip11' => 11,
        'chip12' => 12,
        'chip13' => 13,
        'chip14' => 14,
        'chip15' => 15,
        'chip16' => 16,
        'chip17' => 17,
        'chip18' => 18,
        'chip19' => 19,
        'chip20' => 20,
        'chip21' => 21,
        'chip22' => 22,
        'chip23' => 23,
        'chip24' => 24,
        'chip25' => 25,
        'chip26' => 26,
        'chip27' => 27,
        'chip28' => 28,
        'chip29' => 29,
        'chip30' => 30,
        'chip31' => 31,
        'chip32' => 32,
        'chip33' => 33,
        'chip34' => 34,
        'chip35' => 35,
        'chip36' => 36,
        'chip37' => '0、1',
        'chip38' => '0、2',
        'chip39' => '0、3',
        'chip40' => '1、2',
        'chip41' => '1、4',
        'chip42' => '2、3',
        'chip43' => '2、5',
        'chip44' => '3、6',
        'chip45' => '4、5',
        'chip46' => '4、7',
        'chip47' => '5、6',
        'chip48' => '5、8',
        'chip49' => '6、9',
        'chip50' => '7、8',
        'chip51' => '7、10',
        'chip52' => '8、9',
        'chip53' => '8、11',
        'chip54' => '9、12',
        'chip55' => '10、11',
        'chip56' => '10、13',
        'chip57' => '11、12',
        'chip58' => '11、14',
        'chip59' => '12、15',
        'chip60' => '13、14',
        'chip61' => '13、16',
        'chip62' => '14、15',
        'chip63' => '14、17',
        'chip64' => '15、18',
        'chip65' => '16、17',
        'chip66' => '16、19',
        'chip67' => '17、18',
        'chip68' => '17、20',
        'chip69' => '18、21',
        'chip70' => '19、20',
        'chip71' => '19、22',
        'chip72' => '20、21',
        'chip73' => '20、23',
        'chip74' => '21、24',
        'chip75' => '22、23',
        'chip76' => '22、25',
        'chip77' => '23、24',
        'chip78' => '23、26',
        'chip79' => '24、27',
        'chip80' => '25、26',
        'chip81' => '25、28',
        'chip82' => '26、27',
        'chip83' => '26、29',
        'chip84' => '27、30',
        'chip85' => '28、29',
        'chip86' => '28、31',
        'chip87' => '29、30',
        'chip88' => '29、32',
        'chip89' => '30、33',
        'chip90' => '31、32',
        'chip91' => '31、34',
        'chip92' => '32、33',
        'chip93' => '32、35',
        'chip94' => '33、36',
        'chip95' => '34、35',
        'chip96' => '35、36',
        'chip97' => '0、1、2',
        'chip98' => '0、2、3',
        'chip99' => '1、2、3',
        'chip100' => '4、5、6',
        'chip101' => '7、8、9',
        'chip102' => '10、11、12',
        'chip103' => '13、14、15',
        'chip104' => '16、17、18',
        'chip105' => '19、20、21',
        'chip106' => '22、23、24',
        'chip107' => '25、26、27',
        'chip108' => '28、29、30',
        'chip109' => '31、32、33',
        'chip110' => '34、35、36',
        'chip111' => '0、1、2、3',
        'chip112' => '1、2、4、5',
        'chip113' => '2、3、5、6',
        'chip114' => '4、5、7、8',
        'chip115' => '5、6、8、9',
        'chip116' => '7、8、10、11',
        'chip117' => '8、9、11、12',
        'chip118' => '10、11、13、14',
        'chip119' => '11、12、14、15',
        'chip120' => '13、14、16、17',
        'chip121' => '14、15、17、18',
        'chip122' => '16、17、19、20',
        'chip123' => '17、18、20、21',
        'chip124' => '19、20、22、23',
        'chip125' => '20、21、23、24',
        'chip126' => '22、23、25、26',
        'chip127' => '23、24、26、27',
        'chip128' => '25、26、28、29',
        'chip129' => '26、27、29、30',
        'chip130' => '28、29、31、32',
        'chip131' => '29、30、32、33',
        'chip132' => '31、32、34、35',
        'chip133' => '32、33、35、36',
        'chip134' => '1、2、3、4、5、6',
        'chip135' => '4、5、6、7、8、9',
        'chip136' => '7、8、9、10、11、12',
        'chip137' => '10、11、12、13、14、15',
        'chip138' => '13、14、15、16、17、18',
        'chip139' => '16、17、18、19、20、21',
        'chip140' => '19、20、21、22、23、24',
        'chip141' => '22、23、24、25、26、27',
        'chip142' => '25、26、27、28、29、30',
        'chip143' => '28、29、30、31、32、33',
        'chip144' => '31、32、33、34、35、36',
        'chip145' => '1、4、7、10、13、16、19、22、25、28、31、34',
        'chip146' => '2、5、8、11、14、17、20、23、26、29、32、35',
        'chip147' => '3、6、9、12、15、18、21、24、27、30、33、36',
        'chip148' => '1 ----- 12',
        'chip149' => '13 ----- 24',
        'chip150' => '25 ----- 36',
        'chip151' => '1 ----- 18',
        'chip152' => '19 ----- 36',
        'chip153' => 'Even',
        'chip154' => 'Odd',
        'chip155' => 'Red',
        'chip156' => 'Black',
    ];

    # 骰宝投注类型
    public static $betTypeOfSicbo = [
        'chip0'  => 'BIG',
        'chip1'  => 'SMALL',
        'chip2'  => 'ODD',
        'chip3'  => 'EVEN',
        'chip4'  => 'Represents value of 1 come up',
        'chip5'  => 'Represents value of 2 come up',
        'chip6'  => 'Represents value of 3 come up',
        'chip7'  => 'Represents value of 4 come up',
        'chip8'  => 'Represents value of 5 come up',
        'chip9'  => 'Represents value of 6 come up',
        'chip10' => 'Specific Double come up, value is 1',
        'chip11' => 'Specific Double come up, value is 2',
        'chip12' => 'Specific Double come up, value is 3',
        'chip13' => 'Specific Double come up, value is 4',
        'chip14' => 'Specific Double come up, value is 5',
        'chip15' => 'Specific Double come up, value is 6',
        'chip16' => 'Specific Triple come up, value is 1',
        'chip17' => 'Specific Triple come up, value is 2',
        'chip18' => 'Specific Triple come up, value is 3',
        'chip19' => 'Specific Triple come up, value is 4',
        'chip20' => 'Specific Triple come up, value is 5',
        'chip21' => 'Specific Triple come up, value is 6',
        'chip22' => 'Any Triple come up',
        'chip23' => 'Value of outcome is 4',
        'chip24' => 'Value of outcome is 5',
        'chip25' => 'Value of outcome is 6',
        'chip26' => 'Value of outcome is 7',
        'chip27' => 'Value of outcome is 8',
        'chip28' => 'Value of outcome is 9',
        'chip29' => 'Value of outcome is 10',
        'chip30' => 'Value of outcome is 11',
        'chip31' => 'Value of outcome is 12',
        'chip32' => 'Value of outcome is 13',
        'chip33' => 'Value of outcome is 14',
        'chip34' => 'Value of outcome is 15',
        'chip35' => 'Value of outcome is 16',
        'chip36' => 'Value of outcome is 17',
        'chip37' => 'Value of 1 and 2 come up',
        'chip38' => 'Value of 1 and 3 come up',
        'chip39' => 'Value of 1 and 4 come up',
        'chip40' => 'Value of 1 and 5 come up',
        'chip41' => 'Value of 1 and 6 come up',
        'chip42' => 'Value of 2 and 3 come up',
        'chip43' => 'Value of 2 and 4 come up',
        'chip44' => 'Value of 2 and 5 come up',
        'chip45' => 'Value of 2 and 6 come up',
        'chip46' => 'Value of 3 and 4 come up',
        'chip47' => 'Value of 3 and 5 come up',
        'chip48' => 'Value of 3 and 6 come up',
        'chip49' => 'Value of 4 and 5 come up',
        'chip50' => 'Value of 4 and 6 come up',
        'chip51' => 'Value of 5 and 6 come up',
        'chip52' => 'Any 3 among 1, 2, 3, 4 come up',
        'chip53' => 'Any 3 among 2, 3, 4, 5 come up',
        'chip54' => 'Any 3 among 2, 3, 5, 6 come up',
        'chip55' => 'Any 3 among 3, 4, 5, 6 come up',
    ];

    # 电子传统 21 点手动
    public static $betTypeOfTraditionalBlackjack = [
        'seatnum'   => 'Player seat number',
        'hand'      => 'Player hand information',
        'main'      => 'Initial bet amount',
        'double'    => 'Bet amount on Double',
        'bj21p3'    => 'Bet amount on 21+3',
        'pair'      => 'Bet amount on Pair',
        'insurance' => 'Bet amount on Insurance',
    ];

    # 电子淘金 21 点手动
    public static $betTypeOfFreeBetBlackjack = [
        'seatnum'    => 'Player seat number',
        'hand'       => 'Player hand information',
        'main'       => 'Initial bet amount',
        'freesplit'  => 'Bet on Free Split',
        'freedouble' => 'Bet on Free Double',
        'double'     => 'Bet amount on Double',
        'gold'       => 'Bet amount on Pot of Gold',
    ];

    # 电子换牌 21 点手动
    public static $betTypeOfBlackjackSwitch = [
        'seatnum'    => 'Player seat number',
        'hand'       => 'Player hand information',
        'main'       => 'Initial bet amount',
        'double'     => 'Bet amount on Double',
        'supermatch' => 'Bet amount on Super Match',
        'insurance'  => 'Bet amount on Insurance',
    ];

    public function getWinInfo($gameCode, $wins)
    {
        $winInfo = [];

        foreach ($wins as $win) {
            $winInfo[] = $this->transferWinInfo($gameCode, $win);
        }
        return implode(';', $winInfo);
    }

    public function transferWinInfo($gameCode, $win)
    {
        $tempWinInfo = '';
        switch ($gameCode) {
            case '90091': # 百家乐
            case '90092': # 免拥百家乐
            case '91091': # 电子百家乐
            case '91092': # 电子免拥百家乐
                foreach ($win as $key => $detail) {
                    if ('side' == $key) {
                        $tempWinInfo .= (isset(static::$BaccaratSide[$detail]) ? static::$BaccaratSide[$detail] : $detail) . ':';
                    } elseif ('type' == $key) {
                        $tempWinInfo .= isset(static::$BaccaratType[$detail]) ? static::$BaccaratType[$detail] : $detail;
                    } else {
                        $tempWinInfo .= ' ' . $detail;
                    }
                }
                break;
            case '50002': # 转盘
            case '51002': # 电子转盘
            case '52002': # 手动电子转盘
                foreach ($win as $key => $detail) {
                    if ('type' == $key) {
                        $tempWinInfo .= (isset(static::$rouletteType[$detail]) ? static::$rouletteType[$detail] : $detail) . ':';
                    } elseif ('value' == $key) {
                        $tempWinInfo .= $detail;
                    }
                }
                break;
            case '60001': # 骰宝
            case '61001': # 电子骰宝
            case '62001': # 手动电子骰宝
                foreach ($win as $key => $detail) {
                    if ('side' == $key) {
                        $tempWinInfo .= (isset(static::$sicboSide[$detail]) ? static::$sicboSide[$detail] : $detail) . ':';
                    } elseif ('value' == $key) {
                        $tempWinInfo .= $detail;
                    }
                }
                break;
            case '110001': # 电子传统 21 点手动
            case '110002': # 电子淘金 21 点手动
            case '110003': # 电子换牌 21 点手动
                $tempCard = [];
                foreach ($win as $key => $detail) {
                    if (is_array($detail)) {
                        $cardStr = '';
                        foreach ($detail as $cardKey => $card) {
                            if ('seq' == $cardKey) {
                                $cardStr .= $card . ':';
                            } elseif ('type' == $cardKey) {
                                $cardStr .= isset(static::$blackjackType[$card]) ? static::$blackjackType[$card] : $card;
                            } else {
                                $cardStr .=  ' ' . $card;
                            }
                        }
                        $tempCard[] = $cardStr;
                    } elseif ('seatnum' == $key) {
                        $tempWinInfo .= 'Dealer and Player seat number - ' . $detail . ',';
                    } elseif ('hand' == $key) {
                        $tempWinInfo .= 'Dealer and Player hand information : ' . $detail;
                    }
                }
                $tempWinInfo .= implode(';', $tempCard);
                break;
        }

        return $tempWinInfo;

    }

    # 百家乐牌所属
    public static $BaccaratSide = [
        'bank1'   => '1st card on Banker',
        'bank2'   => '2nd card on Banker',
        'bank3'   => '3rd card on Banker',
        'player1' => '1st card on Player',
        'player2' => '2nd card on Player',
        'player3' => '3rd card on Player',
    ];

    # 百家乐牌花心
    public static $BaccaratType = [
        '1' => 'Spade',
        '2' => 'Heart',
        '3' => 'Club',
        '4' => 'Diamond',
    ];

    # 轮盘珠子颜色
    public static $rouletteType = [
        '1' => 'Green',
        '2' => 'Red',
        '3' => 'Black',
    ];

    # 骰宝
    public static $sicboSide = [
        '1' => '1st die',
        '2' => '2nd die',
        '3' => '3rd die',
    ];

    # 21点
    public static $blackjackType = [
        '1' => 'Spade',
        '2' => 'Heart',
        '3' => 'Club',
        '4' => 'Diamond',
    ];
}
