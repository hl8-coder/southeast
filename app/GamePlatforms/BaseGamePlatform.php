<?php

namespace App\GamePlatforms;

use App\Models\GamePlatformUser;
use App\Repositories\GamePlatformTransferDetailRepository;
use App\Repositories\GamePlatformUserRepository;
use App\Repositories\UserRepository;

class BaseGamePlatform
{
    protected $user;
    protected $platform;
    protected $account;
    protected $data;
    protected $tool;
    protected $request = [
        'url'       => '',
        'data'      => [],
        'headers'   => [],
        'method'    => 'post',
        'data_type' => 'form_params',
        'timeout'   => 3000,
    ];

    protected $url = [];

    public function __construct($arguments)
    {
        $this->user     = $arguments[0];
        $this->platform = $arguments[1];
        $this->account  = $this->platform->account;
        $this->data     = !empty($arguments[2]) ? $arguments[2] : [];
        $classStr       = 'App\\GamePlatforms\\Tools\\' . strtoupper($this->platform->code) . 'Tool';
        $this->tool     = new $classStr($this->platform, $this->user);
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
        $request = $this->getRegisterRequest($platformUser);

        $response = $this->call($request);

        $platformUserId = $this->analysisRegisterResponse($response);

        $platformUser->updatePlatformUserId($platformUserId);

        return $platformUser;
    }

    public function getRegisterRequest(GamePlatformUser $platformUser)
    {
        return $this->request;
    }

    public function analysisRegisterResponse($response)
    {
        return $response;
    }

    /**
     *
     * 登录
     * 1、获取第三方会员，如果未注册调用注册接口
     * 2、返回游戏链接
     *
     * @return mixed
     */
    public function login()
    {
        # 获取已注册会员
        $platformUser = $this->getPlatformUser();

        $request = $this->getLoginRequest($platformUser);

        $result = $this->call($request);

        return $this->analysisLoginResponse($result, $platformUser);
    }

    public function getLoginRequest(GamePlatformUser $platformUser)
    {
        return $this->request;
    }

    public function analysisLoginResponse($response, $platformUser)
    {
        return $response;
    }

    /**
     * 获取注册会员
     *
     * 未注册调用注册方法
     *
     * @param bool $isRemoteRegister 是否需要远程注册
     * @return mixed
     */
    public function getPlatformUser($isRemoteRegister = true)
    {
        $platformUser = GamePlatformUserRepository::findByUserAndPlatform($this->user->id, $this->platform->code);

        # 先注册本地游戏平台会员
        if (!$platformUser) {
            $platformUser = GamePlatformUserRepository::userRegisterPlatform($this->user, $this->platform);
        }

        # 如果需要远程注册调用注册方法
        if (!$platformUser->isRemoteRegistered() && $isRemoteRegister) {
            $platformUser = $this->register($platformUser);
        }

        return $platformUser;
    }


    /**
     * 余额
     * 1、获取请求参数
     * 2、发起请求
     * 3、解析结果获取最新balance，并更新本地第三方会员balance
     *
     * @param boolean $isRemoteRegister
     * @return GamePlatformUser $platformUser
     */
    public function balance($isRemoteRegister = true)
    {
        # 获取已注册会员
        $platformUser = $this->getPlatformUser($isRemoteRegister);

        $request = $this->getBalanceRequest($platformUser);

        $result = $this->call($request);

        $balance = $this->analysisBalanceResponse($result);

        $platformUser->updateBalance($balance);

        return $platformUser;
    }

    public function getBalanceRequest(GamePlatformUser $platformUser)
    {
        return $this->request;
    }

    public function analysisBalanceResponse($response)
    {
        return $response;
    }

    /**
     * 转账
     * 1、获取远程第三方余额
     * 2、更新转账明细转账前金额
     * 3、获取请求参数
     * 4、发起请求
     * 5、解析返回数据
     * 6、检查订单
     *
     * @return mixed
     * @throws \Exception
     */
    public function transfer()
    {
        $isRemoteRegister = $this->isRemoteRegister();
        # 获取账户余额
        $platformUser = $this->balance($isRemoteRegister);

        $detail = $this->data['detail'];

        # 更新转账前余额
        if ($detail->isIncome()) {
            GamePlatformTransferDetailRepository::setToBeforeBalance($detail, $platformUser->balance);
        } else {
            GamePlatformTransferDetailRepository::setFromBeforeBalance($detail, $platformUser->balance);
            if ($detail->amount > $platformUser->balance) {
                error_response(415, 'balance not enough', 415);
            }
        }

        $request = $this->getTransferRequest($platformUser);

        $response = $this->call($request);

        $detail = $this->analysisTransferResponse($response);

        # 查看状态是否需要检查订单状态
        if ($detail->isNeedCheck()) {
            # 这步中需要将最新的交易明细放入data[detail]中，为检查订单做准备
            $this->data['detail'] = $detail;
            $detail               = $this->check();
        }

        return $detail;
    }

    public function getTransferRequest(GamePlatformUser $platformUser)
    {
        return $this->request;
    }

    public function analysisTransferResponse($response)
    {
        return true;
    }

    /**
     * 检查交易订单
     *
     * 1、获取请求参数
     * 2、发起访问
     * 3、解析返回数据，更新状态
     *
     * @return mixed
     */
    public function check()
    {
        $platformUser = $this->getPlatformUser();

        $request = $this->getCheckRequest($platformUser);

        $response = $this->call($request);

        return $this->analysisCheckResponse($response);
    }

    public function getCheckRequest(GamePlatformUser $platformUser)
    {
        return $this->request;
    }

    public function analysisCheckResponse($response)
    {
        return isset($this->data['detail']) ? $this->data['detail'] : null;
    }

    /**
     * 拉取报表
     *
     * 1、获取请求参数
     * 2、发起访问
     * 3、解析返回数据，第三方数据存入第三方原始表，转化后存入转化后的表
     * 4、返回原始数据和交易数据条数
     */
    public function pull()
    {
        $request = $this->getPullRequest();

        $response = $this->call($request);

        return $this->analysisPullResponse($response);
    }

    public function getPullRequest()
    {
        return $this->request;
    }

    public function analysisPullResponse($response)
    {
        return [
            'origin_total'   => 0,
            'transfer_total' => 0,
        ];
    }

    /**
     * 游戏列表
     */
    public function gameList()
    {
        $request = $this->getGameListRequest();

        $result = $this->call($request);

        $this->analysisGameListResult($result);
    }

    public function getGameListRequest()
    {
        return $this->request;
    }

    public function analysisGameListResult($result)
    {
    }

    /**
     * 踢下线
     *
     * @return boolean
     */
    public function kickOut()
    {
        $platformUser = $this->getPlatformUser();

        $request = $this->getKickOutRequest($platformUser);

        # 如果没有实现这个方法直接返回true
        if (empty($request)) {
            return true;
        }

        $response = $this->call($request);

        return $this->analysisKickOutResponse($response);
    }

    public function getKickOutRequest(GamePlatformUser $platformUser)
    {
        return [];
    }

    public function analysisKickOutResponse($response)
    {
        return true;
    }


    public function call($request)
    {

        $result = call_api(
            $request['url'],
            $request['data'],
            $request['headers'],
            $request['method'],
            $request['data_type'],
            $request['timeout']
        );

        return $result;
    }

    protected function getUrl(string $methodName = 'login')
    {
        $urls = $this->url;
        return isset($urls[$methodName]) ? $urls[$methodName] : '';
    }

    protected function isRemoteRegister()
    {
        return true;
    }

    /**
     * 判断是否是试玩模式，默认是真钱模式
     *
     * @return bool|mixed
     */
    protected function isTry()
    {
        return isset($this->data['is_try']) ? $this->data['is_try'] : false;
    }

    /**
     * 判断是否是PC模式
     *
     * @return bool|mixed
     */
    protected function isPC()
    {
        return isset($this->data['device']) ? UserRepository::isPc($this->data['device']) : true;
    }

    /**
     * 判断是否是Mobile模式
     *
     * @return bool|mixed
     */
    protected function isMobile()
    {
        return isset($this->data['device']) ? UserRepository::isMobile($this->data['device']) : false;
    }

    /**
     * 获取币别前缀
     *
     * @param GamePlatformUser $platformUser
     * @return string
     */
    protected function getPrefix(GamePlatformUser $platformUser)
    {
        return strtolower($platformUser->currency) . '_';
    }
}
