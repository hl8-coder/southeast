<?php
namespace App\GamePlatforms;

use App\GamePlatforms\Tools\SmartSoftTool;
use App\Models\GamePlatformUser;
use App\Repositories\GamePlatformTransferDetailRepository;

class SmartSoftPlatform extends BaseGamePlatform
{
    /**
     * @var SmartSoftTool
     */
    protected $tool;

    protected $client;

    public function __construct($arguments)
    {
        parent::__construct($arguments);

        $this->client = new \SoapClient($this->platform->request_url);
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
        $data['request']['ClientExternalKey']   = $this->account['client_external_key'];
        $data['request']['PortalName']          = $this->account['portal_name'];
        $data['request']['GameType']            = $this->data['code'];
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

        return $this->tool->getMessage('game_url') . $token;
    }
    # 登录 end

    # 查询余额 start
    public function balance($isRemoteRegister=false)
    {
        # 获取已注册会员
        $platformUser = $this->getPlatformUser();
        # 拼接参数
        $data['request']['HashValue']           = $this->account['hash_value'];
        $data['request']['ClientExternalKey']   = $this->account['client_external_key'];
        $data['request']['PortalName']          = $this->account['portal_name'];
        $data['request']['AccountType']         = 'GeneralAccount';
        $data['request']['CurrencyCode']        = $this->tool->getPlatformCurrency($platformUser->user->currency);

        # 加密
        $object = $this->setRequest('balance', $data);

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
        $data['request']['ClientExternalKey']   = $this->account['client_external_key'];
        $data['request']['PortalName']          = $this->account['portal_name'];
        $data['request']['GameType']            = $this->data['code'];
        $data['request']['UserName']            = $platformUser->name;
        $data['request']['Direction']           = $detail->isIncome() ? 'ToGambling' : 'FromGambling';
        $data['request']['Amount']              = $detail->conversion_amount;
        $data['request']['TransactionId']       = $detail->order_no;
        $data['request']['IpAddress']           = $this->data['ip'];
        $data['request']['AccountType']         = 'GeneralAccount';
        $data['request']['CurrencyCode']        = $this->tool->getPlatformCurrency($platformUser->user->currency);

        # 加密
        $object = $this->setRequest('transfer', $data);

        $response = $this->client->Transfer($object);

        return $this->tool->checkResponse($response, 'transfer', $this->data);

    }
    # 转账 end

    # 确认账单 start

    # 确认账单 end

    # 拉取报表 start

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