<?php
namespace App\GamePlatforms\Tools;

use App\Models\GameBetDetail;
use App\Models\GamePlatformProduct;
use App\Models\GamePlatformUser;
use App\Models\User;
use App\Repositories\GamePlatformTransferDetailRepository;
use App\Repositories\GamePlatformUserRepository;

class SmartSoftTool extends Tool
{

    protected $currencies = [
        'VND' => 'VND',
        'THB' => 'THB',
    ];

    protected $languages = [

    ];


    protected $devices = [
        User::DEVICE_PC     => 'Web',
        User::DEVICE_MOBILE => 'Mobile',
    ];

    protected $message = [
        'game_url' => 'http://test.ssgportal.com/JetX/JetX/Loader.aspx?GameName=JetX&StartPage=Board&lang=en&Token=',
    ];

    protected $errors = [
        'None'                  => 'None',
        'InternalError'         => 'Internal Error',
        'IncorrectHashValue'    => 'Incorrect HashValue',
        'ClientNotFound'        => 'Client NotFound',
        'InvalidToken'          => 'Invalid Token',
        'NotEnoughMoney'        => 'Not Enough Money',
        'TournamentNotFound'    => 'Tournament Not Found',
        'ClientNotVerified'     => 'Client Not Verified',
    ];

    /**
     * 签名
     *
     * @param  mixed    $data
     * @return string
     */
    public function hash($data)
    {
        if (!is_array($data)) {
            $data = [$data];
        }
        return hash('md5', implode(':', $data));
    }

    /**
     * 签证签名
     *
     * @param mixed     $data       原始数据
     * @param string    $signature  签名后字符串
     * @return bool
     */
    public function checkSign($data, $signature)
    {
        if (is_array($data)) {
            $data = implode('', $data);
        }

        $result =  \openssl_verify($data, base64_decode($signature), $this->platform->rsa_public_key, 'md5');

        return 1 === $result;
    }

    /**
     * 获取装置
     *
     * @param $device
     * @return mixed|string
     */
    public function getDevice($device)
    {
        return isset($this->devices[$device]) ? $this->devices[$device] : '';
    }

    /**
     * 数组转对象
     *
     * @param $arr
     * @return object|void
     */
    public function arrayToObject($arr)
    {
        if (gettype($arr) != 'array') {
            return;
        }
        foreach ($arr as $k => $v) {
            if (gettype($v) == 'array' || getType($v) == 'object') {
                $arr[$k] = (object)$this->arrayToObject($v);
            }
        }

        return (object)$arr;
    }


    public function transferBetDetail($originBetDetails)
    {
        # 添加拉取记录
        $this->insertOriginBetDetails(strtolower($this->platform->code) . '_bet_details', $originBetDetails);

        $betDetails = [];
        $product = GamePlatformProduct::findProductByType($this->platform->code, GamePlatformProduct::TYPE_LIVE);
        foreach ($originBetDetails['betHistories'] as $key => $record) {

            if (!$gamePlatformUser = GamePlatformUserRepository::findByNameAndPlatform($this->platform->code, $record['username'])) {
                continue;
            }

            $betDetails[$key] = [
                'platform_code'     => $this->platform->id,
                'product_code'      => $product->code,
                'platform_currency' => $gamePlatformUser->currency,
                'order_id'          => $record['betHistoryId'],
                'game_code'         => $record['gameType'],
                'game_type'         => $product->type,
                'game_name'         => $record['gameName'],
                'user_name'         => $gamePlatformUser->user_name,
                'bet'               => $record['bet'],
                'profit'            => $record['balance'],
                'bet_at'            => $record['createTime'],
                'payout_at'         => $record['payoutTime'],
                'prize'             => $record['payout'],
                'user_currency'     => $gamePlatformUser->currency,
                'user_bet'          => $record['bet'],
                'user_prize'        => $record['payout'],
                'user_profit'       => $record['balance'],
                'platform_profit'   => -1 * $record['balance'],
                'available_profit'  => -1 * $record['balance'],
                'platform_status'   => GameBetDetail::PLATFORM_STATUS_BET_SUCCESS,
                'bet_info'          => isset($record['judgeResult']) ? $record['judgeResult'] : [],
            ];

            # 空陣列代表用户下注失败
            if (empty($record['betMap'])) {
                $betDetails[$key]['platform_status'] = GameBetDetail::PLATFORM_STATUS_BET_FAIL;
            }

            # 投注详情
            switch ($record['gameType']) {
                case 1: #  百家乐
                    $betDetails[$key]['win_info'] = [
                        'player_cards' => $record['playerCards'],
                        'banker_cards'  => $record['bankerCards'],
                    ];
                    break;
                case 2: #  龙虎
                    $betDetails[$key]['win_info'] = [
                        'dragon_card' => $record['dragonCard'],
                        'tiger_card'  => $record['tigerCard'],
                    ];
                    break;
                case 3: #  骰宝
                    $betDetails[$key]['win_info'] = $record['allDices'];
                    break;
                case 4: #  轮盘
                    $betDetails[$key]['win_info'] = $record['number'];
                    break;
                case 5: #  水果机
                    $betDetails[$key]['win_info'] = [];
                    break;
                case 6: #  试玩水果机
                    $betDetails[$key]['win_info'] = [];
                    break;
                case 7: #  区块链百家乐
                    $betDetails[$key]['win_info'] = [];
                    break;
                case 8: #  牛牛
                    $betDetails[$key]['win_info'] = $record['niuniuResult'];
                    break;
                default:
                    continue;
                    break;
            }
        }

        if (!empty($betDetails)) {
            # 添加总的投注明细表
            batch_insert('game_bet_details', $betDetails, true);
        }

        return [
            'origin_total'   => $originBetDetails['count'],
            'transfer_total' => count($betDetails),
        ];
    }

    /**
     * 解析回复
     *
     * @param $response
     * @param $method
     * @return mixed|\SimpleXMLElement|string
     */
    public function checkResponse($response, $method, $data)
    {
        $this->responseLog($method, 0, (array)$response);

        if ('login' == $method) {
            switch ($response->RegisterTokenResult->ErrorCode) {
                case 'None':
                    return $response->RegisterTokenResult->TokenKey;
                    break;
                default:
                    error_response(422, $this->errors[$response->RegisterTokenResult->ErrorCode]);
                    break;
            }
        } elseif ('balance' == $method) {
            switch ($response->GetBalanceResult->ErrorCode) {
                case 'None':
                    return $response->GetBalanceResult->Balance;
                    break;
                default:
                    error_response(422, $this->errors[$response->GetBalanceResult->ErrorCode]);
                    break;
            }
        } elseif ('transfer' == $method || 'check' == $method) {
            switch ($response->TransferResult->ErrorCode) {
                case 'None':
                    return GamePlatformTransferDetailRepository::setSuccess($data['detail']);
                    break;
                default:
                    return GamePlatformTransferDetailRepository::setFail($data['detail'], $this->errors[$response->TransferResult->ErrorCode]);
                    break;
            }
        } elseif ('pull' == $method) {

        }
    }

    /**
     * 检查交易是否成功
     *
     * @param $result
     * @return mixed
     */
    protected function checkTransferSuccess($result, $data)
    {
        $detail = $data['detail'];

        switch ($result['Data']['status']) {
            case 0:
                GamePlatformTransferDetailRepository::setPlatformOrderNo($detail, $result['Data']['trans_id']);
                GamePlatformTransferDetailRepository::setSuccess($detail);
                break;
            case 1: # 执行过程中失败
            case 2: # 会员不存在
            case 3: # 余额不足
            case 4: # 比最小或最大限制的轉帳金額還更少或更多
            case 5: # 重複的轉帳識別碼
            case 6: # 币别错误
            case 7: # 传入参数错误
            case 8: # 玩家盈餘限制(玩家贏超過系統 可轉出有效值時)
            case 9: # 厂商辨识码失效
            case 10: # 系统维护中
            case 11: # 系统忙线中，请稍后再试
                GamePlatformTransferDetailRepository::setFail($detail);
                error_response(422, '转账失败.');
                break;
            default:
                GamePlatformTransferDetailRepository::setWaitingAndAddCheckJob($detail);
                error_response(422, '未知错误.');
                break;

        }

        return $detail;
    }
}