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
    'namespace'     => 'App\Http\Controllers\Api\Affiliate',
    'middleware'    => ['serializer:array', 'bindings', 'change-language'],
], function($api) {

    $api->group([
        'middleware' => 'api.throttle',
        'limit'      => config('api.rate_limits.access.limit'),
        'expires'    => config('api.rate_limits.access.expires'),
    ], function($api) {
        # 注册
        $api->post('affiliates', 'AffiliatesController@store')->name('api.affiliates.store');
        # 登录
        $api->post('affiliate/authorizations', 'AuthorizationsController@affiliateStore')->name('api.affiliate.authorizations.store');
        # 忘记密码
        $api->patch('affiliates/forget_password', 'AffiliatesController@forgetPassword')->name('api.affiliates.forget_password');
        # 获取公告
        $api->get('affiliate/announcements', 'AffiliateAnnouncementsController@index')->name('api.affiliates.announcements.index');
        # 添加反馈 Feedback
        $api->post('affiliate/feedback', 'FeedbackController@store')->name('api.affiliate.feedback.store');
        # 获取图片验证码
        $api->post('affiliate/captcha', 'CaptchaController@store')->name('api.affiliate.captcha.store');
        # 联系方式
        $api->get('affiliate/contact_us', 'ContactUsController@index')->name('api.affiliates.contact_us.index');
        #
        $api->get('affiliate/random_show', 'AffiliatesController@randomShow')->name('api.affiliates.random_show');
    });

    # 授权相关
    $api->group([
        'middleware' => ['api.auth'],
    ], function($api) {
        # 获取代理详情
        $api->get('affiliate', 'AffiliatesController@me')->name('api.affiliates.me');
        # 获取代理详情
        $api->patch('affiliate', 'AffiliatesController@updateProfile')->name('api.affiliates.update');
        # 邀请函模板
        $api->get('affiliate/invite_email_template', 'InviteSubAffiliatesController@inviteEmailTemplate')->name('api.affiliates.invite_email_template');
        # 发送邀请函
        $api->post('affiliate/send_invite_email', 'InviteSubAffiliatesController@sendInviteEmail')->name('api.affiliate.send_invite_email');
        # 资源列表
        $api->get('affiliate/creative_resources', 'CreativeResourcesController@index')->name('api.affiliate.creative_resources');
        # 添加 TrackingStatistic
        $api->post('affiliate/tracking_statistics', 'CreativeResourcesController@store')->name('backstage.affiliate.tracking_statistics.store');
        # 资源点击列表
        $api->get('affiliate/tracking_statistic_logs', 'TrackingStatisticsController@index')->name('api.affiliate.tracking_statistic_logs');
        # 团队管理
        $api->get('affiliate/down_line_managements', 'DownlineManagementController@index')->name('api.affiliate.down_line_managements');
        # 代理转账
        $api->get('affiliate/fund_managements', 'FundManagementsController@index')->name('api.affiliate.fund_managements.index');
        # 产品详情
        $api->get('affiliate/game_platform_product_details/{detail}', 'GamePlatformProductDetailsController@index')->name('api.affiliate.game_platform_product_details');
        # 代理转账
        $api->post('affiliate/transfer/{user}', 'AffiliatesController@transfer')->name('api.affiliates.transfer');
        # 代理银行卡
        $api->get('affiliate/user_bank_accounts', 'AffiliatesController@affiliateBank')->name('api.affiliates.user_bank_accounts.index');
        # 代理添加银行卡
        $api->post('affiliate/user_bank_accounts/store', 'AffiliatesController@storeBank')->name('api.affiliates.user_bank_accounts.store');
        # 代理修改银行卡
        $api->post('affiliate/user_bank_accounts/{user_bank_account}/update', 'AffiliatesController@updateBank')->name('api.affiliates.user_bank_accounts.update');
        # 代理删除银行卡
        $api->delete('affiliate/user_bank_accounts/{user_bank_account}/delete', 'AffiliatesController@destroyUserBankAccount')->name('api.affiliates.user_bank_accounts.destroy');
        # Report CommissionSummary
        $api->get('affiliate/commission_summary', 'ReportsController@commissionSummaryReport')->name('api.affiliate.commission_summary');
        # Report MemberProfileSummary
        $api->get('affiliate/member_profile_summary', 'ReportsController@memberProfileSummaryReport')->name('api.affiliate.member_profile_summary');
        $api->get('affiliate/member_profile_summary_export', 'ReportsController@memberProfileSummaryReportExport')->name('api.affiliate.member_profile_summary_export');
        # Report PaymentReport
        $api->get('affiliate/payment_report', 'ReportsController@paymentReport')->name('api.affiliate.payment_report');
        $api->get('affiliate/payment_report/export', 'ReportsController@paymentReportExport')->name('api.affiliate.payment_report.export');
        # Report Company Win/Loss Based On Products
        $api->get('affiliate/company_product_report', 'ReportsController@companyProductReport')->name('api.affiliate.company_product_report');
        # Report Company Win/Loss Based On Products Detail
        $api->get('affiliate/company_product_report/{product}', 'ReportsController@companyProductDetailReport')->name('api.affiliate.company_product_report.detail');
        # Report Company Win/Loss By Provider
        $api->get('affiliate/company_provider_report', 'ReportsController@companyProviderReport')->name('api.affiliate.company_provider_report');
        # 费用 Report expenses
        $api->get('affiliate/get_tracking_statistics', 'CreativeResourcesController@getTrackingStatistic')->name('api.affiliate.get_tracking_statistics');
        # Affiliate Link
        $api->get('affiliate/affiliate_links', 'AffiliateLinksController@index')->name('api.affiliate.affiliate_links.index');

    });
});
