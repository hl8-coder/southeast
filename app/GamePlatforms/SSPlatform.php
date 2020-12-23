<?php
namespace App\GamePlatforms;

use App\GamePlatforms\Tools\SSTool;
use App\Models\GamePlatformUser;
use App\Repositories\GamePlatformTransferDetailRepository;
use Illuminate\Support\Facades\Log;

class SSPlatform extends BaseGamePlatform
{
    /**
     * @var SSTool
     */
    protected $tool;

    protected $client;

    public function __construct($arguments)
    {
        parent::__construct($arguments);

        try {
//            $this->client = new \SoapClient($this->platform->request_url, array('cache_wsdl' => WSDL_CACHE_NONE, 'trace' => 1));
        } catch (\Exception $exception) {
            Log::info($exception->getMessage());
        }
    }

    /**
     * 注册
     * 1、获取请求参数
     * 2、发起请求
     * 3、解析结果
     * 4、返回第三方注册id并更新本地第三方平台会员id
     *
     * @return GamePlatformUser
     */
    public function register(GamePlatformUser $platformUser)
    {
        $platformUserId = '';

        $platformUser->updatePlatformUserId($platformUserId);

        return $platformUser;
    }
    # 注册 end

    # 登录 start
    public function login()
    {

        # 获取已注册会员
        $platformUser = $this->getPlatformUser();
        # 拼接参数
        $data['request']['HashValue']           = $this->account['hash_value'];
        $data['request']['ClientExternalKey']   = $platformUser->name;
        $data['request']['PortalName']          = $this->account['portal_name'];
        $data['request']['GameType']            = $this->tool->getGameType($this->data['code']);
        $data['request']['UserName']            = $platformUser->name;
        $data['request']['IpAddress']           = $this->data['ip'];
        $data['request']['DeviceType']          = $this->tool->getDevice($this->data['device']);
        $data['request']['IsClientVerified']    = true;
        $data['request']['CurrencyCode']        = $this->tool->getPlatformCurrency($platformUser->user->currency);
        $hashData = $data;
        unset($hashData['request']['DeviceType'], $hashData['request']['IsClientVerified']);
        $object = $this->setRequest('login', $hashData, $data);
        $response = $this->client->RegisterToken($object);

        $token = $this->tool->checkResponse($response, 'login', $this->data);

        $request['GameName'] = $this->data['code'];
        $request['token'] = $token;
        $request['lang'] =  $this->tool->getPlatformLanguage($platformUser->user->language);

        $launcherUrl = $this->tool->getLauncherUrl($this->platform->code, $this->data['code'], $this->account) ;

        # 判断地址中有无出现?
        if (false === strpos($launcherUrl, '?')) {
            $launcherUrl = $launcherUrl . '?' . http_build_query($request);
        } else {
            $launcherUrl = $launcherUrl . '&' . http_build_query($request);
        }
        return $launcherUrl;
    }
    # 登录 end

    # 查询余额 start
    public function balance($isRemoteRegister=false)
    {
        # 获取已注册会员
        $platformUser = $this->getPlatformUser();
        # 拼接参数
        $data['request']['HashValue']           = $this->account['hash_value'];
        $data['request']['ClientExternalKey']   = $platformUser->name;
        $data['request']['PortalName']          = $this->account['portal_name'];
        $data['request']['UserName']            = $platformUser->name;
        $data['request']['AccountType']         = 'GeneralAccount';
        $data['request']['CurrencyCode']        = $this->tool->getPlatformCurrency($platformUser->user->currency);

        # 加密
        $hashData = $data;
        unset($hashData['request']['UserName']);
        $object = $this->setRequest('balance', $hashData, $data);
        $response = $this->client->GetBalance($object);
        $balance = $this->tool->checkResponse($response, 'balance', $this->data);

        $platformUser->updateBalance($balance);

        return $platformUser;
    }
    # 查询余额 end

    # 转账 start
    public function transfer()
    {
        # 获取账户余额
        $platformUser = $this->balance();
        $detail = $this->data['detail'];

        # 更新转账前余额
        if ($detail->isIncome()) {
            GamePlatformTransferDetailRepository::setToBeforeBalance($detail, $platformUser->balance);
        } else {
            GamePlatformTransferDetailRepository::setFromBeforeBalance($detail, $platformUser->balance);
        }
        # 拼接参数
        $data['request']['HashValue']           = $this->account['hash_value'];
        $data['request']['ClientExternalKey']   = $platformUser->name;
        $data['request']['PortalName']          = $this->account['portal_name'];
        $data['request']['UserName']            = $platformUser->name;
        $data['request']['Direction']           = $detail->isIncome() ? 'ToGambling' : 'FromGambling';
        $data['request']['Amount']              = $detail->conversion_amount;
        $data['request']['TransactionId']       = $detail->order_no;
        $data['request']['IpAddress']           = $detail['user_ip'];
        $data['request']['AccountType']         = 'GeneralAccount';
        $data['request']['CurrencyCode']        = $this->tool->getPlatformCurrency($platformUser->user->currency);
        # 加密
        $object = $this->setRequest('transfer', $data, $data);
        $response = $this->client->Transfer($object);
        return $this->tool->checkResponse($response, 'transfer', $this->data);

    }
    # 转账 end

    # 拉取报表 start

    public function pull()
    {
        $schedule = $this->data['schedule'];
        $data['request']['HashValue']           = $this->account['hash_value'];
        $data['request']['PortalName']          = $this->account['portal_name'];
        $startDate                              = $schedule->start_at->subMinutes($this->platform->offset)->toDateTimeString();
        $endDate                                = $schedule->end_at->toDateTimeString();
        $data['request']['StartDate']           = $this->tool->parseUTCMinus8($startDate)->format('Y-m-d\TH:i:s');
        $data['request']['EndDate']             = $this->tool->parseUTCMinus8($endDate)->format('Y-m-d\TH:i:s');
        $hashData =  $data;
        $hashData['request']['StartDate']       = $this->tool->parseUTCMinus8($startDate)->format('d/m/Y H:i');
        $hashData['request']['EndDate']         = $this->tool->parseUTCMinus8($endDate)->format('d/m/Y H:i');
        $object = $this->setRequest('pull', $hashData, $data);
        $response = $this->client->GetClientTransactions($object);
        $result = $this->tool->checkResponse($response, 'pull', $this->data);
        return $this->tool->insertBetDetails($result);
    }
    # 拉取报表 end

    # 获取游戏列表 start

    # 获取游戏列表 end
    public function setRequest($method, $hashData, $data)
    {
        # 加密,并且替换hash_value


        $data['request']['HashValue'] = $this->tool->hash($hashData['request']);
        $this->tool->requestLog($method, $data);
        # 转化对象
        return $this->tool->arrayToObject($data);
    }
}
