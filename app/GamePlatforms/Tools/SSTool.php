<?php
namespace App\GamePlatforms\Tools;

use Carbon\Carbon;
use App\Models\Game;
use App\Models\GameBetDetail;
use App\Models\GamePlatformProduct;
use App\Models\GamePlatformUser;
use App\Models\User;
use App\Repositories\GamePlatformTransferDetailRepository;
use App\Repositories\GamePlatformUserRepository;

class SSTool extends Tool
{
    const DEFAULT_GAME_TYPE = 'OtherGamesSlot';

    protected $currencies = [
        'VND' => 'VND',
        'THB' => 'THB',
    ];

    protected $languages = [
        'zh-CN' => 'zh',
        'vi-VN' => 'vi',
        'en_us' => 'en',
        'th'    => 'th',
    ];

    protected $devices = [
        User::DEVICE_PC     => 'Web',
        User::DEVICE_MOBILE => 'Mobile',
    ];

    protected $vendorUserPrefix = 'TPW-';

    public $statusRelation = [
        '0' => GameBetDetail::PLATFORM_STATUS_WAITING,
        '1' => GameBetDetail::PLATFORM_STATUS_BET_SUCCESS,
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

    protected $gameTypes = [
        'JetX'          => 'JetX',
        'Donuts'        => 'Slot',
        'LuckyHunter'   => 'Slot',
        'HappyDiver'    => 'Slot',
        'LuckyFisher'   => 'Slot',
        'FunFruit'      => 'Slot',
        'FruitBar'      => 'Slot',
        'CarsSlot'      => 'Slot',
        'CitySlot'      => 'Slot',
        'Dota'          => 'Slot',
        'Birds'         => 'Slot',
        'Galaxy'        => 'Slot',
        'Casino'        => 'Slot',
        'Sport'         => 'Slot',
        'DonutCity'     => 'Slot',
        'Aztec'         => 'Slot',
        'Flowers'       => 'Slot',
        'BookOfWin'     => 'Slot',
        'Viking'        => 'Slot',
        'Pharaoh'       => 'Slot',
        'Cowboy'        => 'Slot',
        'Christmas'     => 'Slot',
        'Football'      => 'Slot',
        'Samurai'       => 'Slot',
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
       // dd( implode(':', $data));
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
        return isset($this->devices[$device]) ? $this->devices[$device] : 'Web';
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


    public function insertBetDetails($originBetDetails)
    {
        $account    = $this->platform->account;
        $betDetails = [];
        $now        = now();
        $ctr=1;
        foreach ($originBetDetails as  $record) {
            $ctr++;
            $gameCode = isset($record->GameCode)? $record->GameCode: $record->GameType;
            if (!$game = Game::findByPlatformAndCode($this->platform->code, $gameCode)) {
                continue;
            }
            if (!$user = $this->getUser(str_replace($this->vendorUserPrefix, '', $record->ClientExternalKey))) {
                continue;
            }
            if(0 == floatval($record->BetAmount)) {
                continue;
            }

            $languageSet      = $game->getLanguageSet('en-US');
            $availables       = $this->getAvailableBetAndProfit($record);
            $betDetails[] = [
                'platform_code'     => $this->platform->code,
                'product_code'      => $game->product_code,
                'order_id'          => isset($record->TransactionGroupReference) ? $record->TransactionGroupReference.'-'.$record->ReferenceId: $record->ReferenceId,
                'game_code'         => $game->code,
                'game_type'         => $game->type,
                'game_name'         => $game->getEnName(),
                'user_id'           => $user->id,
                'user_name'         => $user->name,
                'issue'             => '',
                'bet_at'            => $this->parseUTCPLus8($record->TransactionDate),
                'payout_at'         => !empty($record->SettleDate) ? $this->parseUTCPLus8($record->SettleDate) : '',
                'odds'              => '',
                'platform_currency' => $user->currency,
                'stake'             => $record->BetAmount,
                'bet'               => $availables['bet'],
                'profit'            => $record->profit,
                'prize'             => floatval($record->BetAmount) + floatval($record->profit),
                'user_currency'     => $user->currency,
                'user_stake'        => $record->BetAmount,
                'user_bet'          => $availables['bet'],
                'user_profit'       => $record->profit,
                'user_prize'        => floatval($record->BetAmount) + floatval($record->profit),
                'platform_profit'   => -1 * floatval($record->profit),
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
            'origin_total'   => count($originBetDetails ),
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
            switch ($response->GetClientTransactionsResult->ErrorCode) {
                case 'None':
                    if(isset($response->GetClientTransactionsResult->ClientTransactionReports->ClientTransactionReport)) {
                        return $response->GetClientTransactionsResult->ClientTransactionReports->ClientTransactionReport;
                    }
                    return array();
                    break;
                default:
                    break;
            }

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
            case 6: # 币别错误getGameType
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

    public function parseUTCPLus8($timeStr)
    {
        return Carbon::parse($timeStr)->addHours(16)->toDateTimeString();
    }

    public function parseUTCMinus8($timeStr)
    {
        return Carbon::parse($timeStr)->subHours(16);
    }

    public function getGameType($gameCode){
        return isset($this->gameCode[$gameCode]) ?  $this->gameCode[$gameCode] : self::DEFAULT_GAME_TYPE;
    }

    public function getLauncherUrl($platformCode, $gameCode, $account)
    {
        if( $game = Game::findByPlatformAndCode($platformCode, $gameCode)) {
            $key  = strtolower(str_replace( $game->platform_code.'_', '', $game->product_code) . '_url');
            if(isset($account[$key])) {
                return  $account[$key];
            }
        }
    }

    private function getAvailableBetAndProfit($record)
    {
        $result = [
            'bet'    => 0,
            'profit' => 0,
        ];

        if( empty($record->SettleDate)  ) {
            return $result;
        }


        $result['bet']    = $record->BetAmount;
        $result['profit'] = $record->profit;
        return $result;
    }

    private function getBetInfo($record)
    {
        $device = '';
        $info = 'bet:' . $record->BetAmount . ' result:' . $record->profit . ' time:' . $this->parseUTCPLus8($record->TransactionDate) . ' in:' . $device;
        return $info;
    }


    private function getPlatformStatus($record)
    {
        if( !empty($record->SettleDate) ) {
            return GameBetDetail::PLATFORM_STATUS_BET_SUCCESS;
        }
        return GameBetDetail::PLATFORM_STATUS_WAITING;
    }


}
