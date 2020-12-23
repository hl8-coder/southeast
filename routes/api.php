<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

# 前端接口
$api->version('v1', [
    'namespace'  => 'App\Http\Controllers\Api',
    'middleware' => ['serializer:array', 'bindings', 'change-language', 'check-device'],
], function ($api) {

    # 授权相关
    $api->group([
        'middleware' => ['api.throttle', 'api-log', 'cors'],
        'limit'      => config('api.rate_limits.access.limit'),
        'expires'    => config('api.rate_limits.access.expires'),
    ], function ($api) {
        # 注册
        $api->post('users', 'UsersController@store')->name('api.users.store');
        # 登录
        $api->post('authorizations', 'AuthorizationsController@store')->name('api.authorizations.store');
        # 刷新token
        $api->put('authorizations/current', 'AuthorizationsController@update')->name('api.authorizations.update');
        # 删除token
        $api->delete('authorizations/current', 'AuthorizationsController@destroy')->name('api.authorizations.destroy');
        #找回密码
        $api->patch('user/forget_password', 'UsersController@forgetPassword')->name('api.users.forget_password');
        #访问记录
        $api->post('access_logs', 'AccessLogsController@log')->name('api.access_logs.store');
        #换取Token
        $api->get('exchange/code', 'AuthorizationsController@exchangeCode')->name('api.exchange.code');
        # token
        $api->get('token', 'UserTokenController@index')->name('api.user.token.index');
    });

    # 游客访问
    $api->group([
        'middleware' => [],
        //        'middleware' => ['api.throttle'],
        //        'limit' => config('api.rate_limits.access.limit'),
        //        'expires' => config('api.rate_limits.access.expires'),
    ], function ($api) {
        # IP识别地理位置返回语言
        $api->get('auth/language', 'AuthorizationsController@authLanguage')->name('api.authorization.auth_language');

        # 检查会员信息是否已存在
        $api->patch('users/check_field_unique', 'UsersController@checkFieldUnique')->name('api.users.check_field_unique');

        # 获取币别信息
        $api->get('currencies/current', 'CurrenciesController@findCurrency')->name('api.currency.current');

        # 币别
        $api->get('currencies', 'CurrenciesController@index')->name('api.currency.index');

        # 游戏
        $api->get('games', 'GamesController@index')->name('api.games.index');
        $api->get('games/no_slot', 'GamesController@noSlotIndex')->name('api.games.no_slot');
        $api->get('games/invalid_bet', 'GamesController@invalidBetIndex')->name('api.games.invalid_bet');
        $api->get('games/hot', 'GamesController@hotIndex')->name('api.games.hot_index');
        $api->get('games/sub_menu', 'GamesController@subMenu')->name('api.games.sub_menu');
        # 试玩游戏
        $api->post('games/{game}/try_login', 'GamesController@tryLogin')->name('api.games.try_login');
        # 游戏产品列表
        $api->get('game_platform_products', 'GamePlatformProductsController@index')->name('api.game_platforms.index');
        # 奖池
        $api->get('game_platforms/jackpot', 'GamePlatformsController@getJackpot')->name('api.game_platforms.jackpot');

        # 新闻
        $api->resource('news', 'NewsController', ['only' => ['index', 'show']]);

        # 公告
        $api->get('announcements', 'AnnouncementsController@index')->name('api.announcements.index');

        # 轮播图
        $api->get('banners', 'BannersController@index')->name('api.banners.index');

        # 广告
        $api->get('advertisements', 'AdvertisementsController@index')->name('api.advertisements.index');

        # 系统配置
        $api->get('configs', 'ConfigsController@index')->name('api.configs.index');

        # 下拉列表
        $api->get('drop_list/{code}', 'DropListController@index')->name('api.drop_list.index');

        # 优惠类型
        $api->get('promotion_types', 'PromotionTypesController@index')->name('api.promotions_types.index');
        $api->get('promotion_types/{code}/promotions', 'PromotionsController@index')->name('api.promotions.index');
        $api->get('promotions', 'PromotionsController@allIndex')->name('api.promotions.all_index');
        $api->get('promotions/{promotion}', 'PromotionsController@show')->name('api.promotions.show');

        # 所有充值方式
        $api->get('payment_platforms/all', 'PaymentPlatformsController@all')->name('api.payment_platforms.all');

        # 首页菜单控制与显示
        $api->get('home', 'HomeController@home')->name('api.home.home');

    });

    # 需要授权但是不需要检查是否强制修改密码
    $api->group([
        'middleware' => ['api.auth'],
    ], function ($api) {
        # 获取登录会员信息
        $api->get('user', 'UsersController@me')->name('api.users.show');
        # 第三方风控 登录
        $api->post('fraud_force/login', 'FraudForceController@login')->name('api.fraud_force.login');
        # 第三方风控 登录
        $api->post('fraud_force/register', 'FraudForceController@register')->name('api.fraud_force.register');
        # 修改密码
        $api->patch('user/password', 'UsersController@changePassword')->name('api.users.password');
        # 会员余额
        $api->get('user/balance', 'UsersController@getBalance')->name('api.users.balance');
        # 充值渠道菜单
        $api->get('payment_platforms/menu', 'PaymentPlatformsController@menu')->name('api.payment_platforms.menu');
    });

    # 访问相关
    $api->group([
        'middleware' => ['api.auth', 'api-log'],
        //        'middleware' => ['api.throttle', 'api.auth', 'check-need-change-password'],
        //        'limit'      => config('api.rate_limits.access.limit'),
        //        'expires'    => config('api.rate_limits.access.expires'),
    ], function ($api) {
        # 更新会员信息
        $api->patch('user', 'UsersController@update')->name('api.users.update');

        # 会员领取取验证奖励
        $api->post('user/claim_verify_prize', 'UsersController@claimVerifyPrize')->name('api.users.claim_verify_prize');

        # vip
        $api->get('banks', 'BanksController@index')->name('api.banks.index');

        # vip
        $api->get('vips', 'VipsController@index')->name('api.vips.index');
        # 积分等级
        $api->get('rewards', 'RewardsController@index')->name('api.rewards.index');

        # 会员银行卡相关
        $api->resource('user_bank_accounts', 'UserBankAccountsController', ['only' => ['index', 'store', 'update']]);

        # 会员Mpay相关
        $api->resource('user_mpay_numbers', 'UserMpayNumbersController', ['only' => ['index', 'store', 'update']]);

        # 会员提现
        $api->resource('withdrawals', 'WithdrawalsController', ['only' => ['index', 'show', 'store', 'destroy']]);

        # 充值渠道
        $api->resource('payment_platforms', 'PaymentPlatformsController', ['only' => ['index']]);

        # 充值-会员银行卡渠道
        $api->get('deposits/company_bank_accounts', 'DepositsController@companyBankAccounts')->name('api.deposits.company_bank_accounts.index');

        # 会员充值
        $api->resource('deposits', 'DepositsController', ['only' => ['index', 'store']]);

        # 消息通知
        $api->get('user/notifications', 'NotificationsController@index')->name('api.notifications.index');
        $api->patch('user/notifications/read', 'NotificationsController@read')->name('api.notifications.read');
        $api->delete('user/notifications', 'NotificationsController@destroy')->name('api.notifications.delete');
        # 回复消息
        $api->patch('notifications/{notification}/reply', 'NotificationsController@reply')->name('api.notifications.reply');

        # 图片上传
        $api->post('images', 'ImagesController@store')->name('api.images.store');

        # 游戏
        $api->post('games/{game}/login', 'GamesController@login')->name('api.games.login');
        $api->get('game_platforms/wallets', 'GamePlatformsController@getWallets')->name('backstage.game_platforms.wallets');
        $api->get('game_platforms/{code}/balance', 'GamePlatformsController@balance')->name('api.game_platforms.balance');
        $api->post('game_platforms/transfer', 'GamePlatformsController@transfer')->name('api.game_platforms.transfer');

        # 语言
        $api->get('languages', 'LanguagesController@index')->name('api.languages.index');

        # 优惠
        $api->post('promotions/{promotion}/claim', 'PromotionsController@claim')->name('api.promotions.claim');

        # 红利
        $api->get('bonuses', 'BonusesController@index')->name('api.bonuses.index');

        # History
        # 充值提领历程
        $api->get('history/deposit_withdrawal', 'HistoryController@depositWithdrawal')->name('api.history.deposit_withdrawal.index');

        # 钱包转帐历程
        $api->get('history/fund_transfer', 'HistoryController@fundTransfer')->name('api.history.fund_transfer.index');

        # 调整历程
        $api->get('history/adjustment', 'HistoryController@adjustment')->name('api.history.adjustment.index');

        # 优惠历程
        $api->get('history/promotion_claim', 'HistoryController@promotionClaim')->name('api.history.promotion_claim.index');

        # 返点历程
        $api->get('history/rebate', 'HistoryController@rebate')->name('api.history.rebate.index');

        # 发送手机验证码
        $api->get('user/send_phone_code', 'VerificationController@sendPhoneCode')->name('api.send_phone_code');

        # 发送邮箱验证码
        $api->get('user/send_email_code', 'VerificationController@sendEmailCode')->name('api.send_email_code');

        # 校验验证码
        $api->post('user/verify_code', 'VerificationController@verificationCode')->name('api.verify_code');

        # nexmo
        $api->get('user/send_nexmo_code', 'VerificationController@sendNexmoCode')->name('api.send_nexmo_code');

        # nexmo
        $api->get('banks/maintenance', 'BanksController@maintenanceIndex')->name('api.banks.maintenance.index');


    });
});
