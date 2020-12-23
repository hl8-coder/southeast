<?php
namespace App\GamePlatforms\Tools;

use App\Models\Config;
use App\Models\GamePlatform;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Tool
{
    protected $platform;
    protected $user;

    protected $currencies = [];
    protected $languages  = [];
    protected $message    = [];
    protected $errors     = [];

    const ERROR_TRANSACTION_NOT_EXIST = 1;
    const ERROR_UNKNOWN               = 2;

    protected static $commonErrors = [
        self::ERROR_TRANSACTION_NOT_EXIST   => 'The Transaction is no exists.',
        self::ERROR_UNKNOWN                 => 'Unknown mistake.',
    ];

    #For game providers that accepts only numeric values for transaction ID
    protected $numericOperationIds = [
      'hl8' => 99,
      'hl'  => 99,
      'eg'  => 98
    ];

    public function __construct(GamePlatform $platform, User $user=null)
    {
        $this->platform = $platform;
        $this->user     = $user;
    }

    /**
     * 获取游戏平台对应的币别
     *
     * @param  string   $currency   会员币别
     * @return mixed
     */
    public function getPlatformCurrency($currency)
    {
        return isset($this->currencies[$currency]) ? $this->currencies[$currency] : $currency;
    }

    /**
     * 获取游戏平台对应的语言
     *
     * @param  string   $language   会员语言
     * @return mixed
     */
    public function getPlatformLanguage($language)
    {
        return isset($this->languages[$language]) ? $this->languages[$language] : $language;
    }

    /**
     * 获取平台额其他信息
     *
     * @param  string $key
     * @return mixed|string
     */
    public function getMessage($key)
    {
        return isset($this->message[$key]) ? $this->message[$key] : '';
    }

    /**
     * 获取错误信息
     *
     * @param  string   $key
     * @return mixed|string
     */
    public function getError($key)
    {
        return isset($this->errors[$key]) ? $this->errors[$key] : 'Unknown mistake.';
    }


    /**
     * 记录请求日志
     *
     * @param $method
     * @param $request
     */
    public function requestLog($method, $request)
    {
        $request = is_string($request) ? $request : json_encode($request);

        Log::stack([strtolower($this->platform->code)])->info("\n平台: ". $this->platform->code ."\n方法: " . $method . "\n请求参数: " . $request);
    }

    /**
     * 记录返回日志
     *
     * @param $method
     * @param $statusCode
     * @param $response
     */
    public function responseLog($method, $statusCode, $response)
    {
        $response = is_string($response) ? $response : json_encode($response);

        Log::stack([strtolower($this->platform->code)])->info("\n平台: ". $this->platform->code ."\n方法: " . $method . "\n返回code:" . $statusCode . "\n内容: " . $response);
    }

    /**
     * 添加投注明细
     *
     * @param $originBetDetails
     * @return array
     */
    public function insertBetDetails($originBetDetails)
    {

//        if (!empty($originBetDetails)) {
//            # 添加拉取记录
//            $this->insertOriginBetDetails(strtolower($this->platform->code) . '_bet_details', $originBetDetails);
//        }

        return $this->transferBetDetail($originBetDetails);
    }

    /**
     * 添加原始投注记录
     *
     * @param $collection
     * @param $details
     */
    protected function insertOriginBetDetails($collection, $details)
    {
        DB::connection('mongodb')->collection($collection)->insert($details);
    }

    /**
     * 转换投注明细
     *
     * @param $originBetDetails
     * @return array
     */
    public function transferBetDetail($originBetDetails)
    {
        return [
            'origin_total'   => 0,
            'transfer_total' => 0,
        ];
    }

    /**
     * 获取会员平台名称
     *
     * @param $gamePlatformUserName
     * @return UserRepository|\Illuminate\Database\Eloquent\Model|null|object
     */
    public function getUser($gamePlatformUserName)
    {
        $operationId = Config::findValue('operation_id');

        if (false === strpos($gamePlatformUserName, $operationId)) {
            return null;
        }

        $name = substr($gamePlatformUserName, strlen($operationId));

        return UserRepository::findByName($name);
    }

    /**
     * 批量获取user
     *
     * @param $gamePlatformUserNames
     * @return mixed
     */
    public function getUsers($gamePlatformUserNames, $suffix='')
    {
        $operationId = Config::findValue('operation_id') . $suffix;
        $effectiveNames = [];

        foreach ($gamePlatformUserNames as $k => $name) {
            if (false === strpos($name, $operationId)) {
                continue;
            }
            $effectiveNames[] = substr($name, strlen($operationId));
        }

        $users = User::query()->isUser()->whereIn('name', $effectiveNames)->get();
        $result = [];

        foreach ($users as $user) {
            $result[$operationId . $user->name] = $user;
        }

        return $result;
    }

    /**
     * 用于区分多平台共用一个商户问题
     *
     * @param $orderNo
     * @return string
     */
    public function getTransferOrderNo($orderNo, $isNumeric=false)
    {
        $operationId = Config::findValue('operation_id');
        #For game providers that accepts only numeric values for transaction ID
        if(true == $isNumeric) {
            return $this->numericOperationIds[strtolower($operationId)].$orderNo;
        }
        return $operationId . '_' . $orderNo;
    }

    /**
     * 获取二维数组指定的不重复key对应的值
     *
     * @param $array
     * @param $key
     * @return array
     */
    public function getUniqueColumn($array, $key)
    {
        return array_unique(array_column($array, $key));
    }
}
