<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Models\Adjustment;
use App\Models\Admin;
use App\Models\AdminRole;
use App\Models\Affiliate;
use App\Models\AffiliateCommission;
use App\Models\AffiliateLink;
use App\Models\Announcement;
use App\Models\Bank;
use App\Models\BankTransaction;
use App\Models\Banner;
use App\Models\Bonus;
use App\Models\BonusGroup;
use App\Models\CompanyBankAccount;
use App\Models\CompanyBankAccountTransaction;
use App\Models\CreativeResource;
use App\Models\CrmBoAdmin;
use App\Models\CrmCallLog;
use App\Models\CrmExcludeUser;
use App\Models\CrmOrder;
use App\Models\CrmResource;
use App\Models\CrmResourceCallLog;
use App\Models\CrmWeeklyReport;
use App\Models\Currency;
use App\Models\Config;
use App\Models\DatabaseNotification;
use App\Models\Game;
use App\Models\GameBetDetail;
use App\Models\GamePlatform;
use App\Models\GamePlatformProduct;
use App\Models\GamePlatformPullReportSchedule;
use App\Models\Language;
use App\Models\MailboxTemplate;
use App\Models\Menu;
use App\Models\Model;
use App\Models\PaymentGroup;
use App\Models\PaymentPlatform;
use App\Models\PgAccount;
use App\Models\ProfileRemark;
use App\Models\Promotion;
use App\Models\PromotionClaimUser;
use App\Models\PromotionType;
use App\Models\Reward;
use App\Models\Remark;
use App\Models\RiskGroup;
use App\Models\TrackingStatistic;
use App\Models\Transaction;
use App\Models\TransferDetail;
use App\Models\Url;
use App\Models\User;
use App\Models\UserBonusPrize;
use App\Models\UserInfo;
use App\Models\UserMessage;
use App\Models\UserRisk;
use App\Models\Vip;
use App\Models\Deposit;
use App\Models\DepositLog;
use App\Models\Withdrawal;
use App\Models\AffiliateAnnouncement;
use Illuminate\Http\Request;

class DropListController extends BackstageController
{

    public $request;

    /**
     * @OA\Get(
     *      path="/backstage/drop_list/{code}",
     *      operationId="backstage.drop_list.index",
     *      tags={"Backstage-平台"},
     *      summary="下拉列表",
     *      @OA\Parameter(
     *         name="code",
     *         in="path",
     *         description="模块code",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(),
     *          ),
     *      ),
     *     @OA\Response(response=401, description="授权不通过"),
     *     @OA\Response(response=422, description="验证错误"),
     *     security={
     *         {"bearer": {}}
     *     }
     *  )
     */
    public function index(Request $request)
    {
        $data = [];
        $this->request = $request;
        $code = to_camel_case($request->route('code'));
        if (method_exists($this, $code)) {
            $data = $this->$code();
        }

        foreach ($data as $key => $value) {
            $data[$key] = transform_list($value);
        }

        return $this->response->array($data);
    }

    public function global()
    {
        $data = [];

        $data['currency']         = Currency::getDropList();
        $data['risk_group_id']    = RiskGroup::getDropList();
        $data['payment_group_id'] = PaymentGroup::getDropList();
        $data['language']         = Language::getDropList();
        $data['vip_id']           = Vip::getDropList();

        return $data;
    }

    protected function user()
    {
        $data = [];

        $gamePlatforms = GamePlatform::getAll()->pluck('code', 'id')->toArray();
        $rewards       = Reward::getAll()->pluck('level', 'id')->toArray();

        $data['currency']         = Currency::getDropList();
        $data['status']           = User::$statuses;
        $data['risk_group_id']    = RiskGroup::getDropList();
        $data['payment_group_id'] = PaymentGroup::getDropList();
        $data['game_platform_id'] = $gamePlatforms;
        $data['reward_id']        = $rewards;
        $data['vip_id']           = Vip::getDropList();
        $data['language']         = Language::getDropList();
        $data['gender']           = UserInfo::$genders;
        $data['odds']             = User::$odds;
        $data['country_code']     = Currency::getAll()->pluck('country_code', 'country_code')->toArray();
        $data['platform_code']    = GamePlatform::getDropList(true);

        return $data;
    }

    protected function action()
    {
        $data['menu_id'] = Menu::all()->pluck('name', 'id');
        $data['method']  = ['GET' => 'GET', 'POST' => 'POST', 'DELETE' => 'DELETE', 'PUT' => 'PUT', 'PATCH' => 'PATCH'];
        return $data;
    }

    protected function admin()
    {
        $data               = [];
        $data['language']   = Language::getDropList();
        $data['admin_role'] = AdminRole::all()->pluck('name', 'id');
        $data['status']     = Admin::$statuses;
        return $data;
    }


    public function bankTransaction()
    {
        $data = [];

        $accounts = CompanyBankAccount::getAll()->where('type', CompanyBankAccount::TYPE_DEPOSIT)
            ->where('status', CompanyBankAccount::STATUS_ACTIVE);
        if (!empty($this->request->currency)) {
            $accounts = $accounts->where('currency', $this->request->currency);
        }
        $accounts = $accounts->pluck('code', 'code')->toArray();

        $data['status']          = BankTransaction::$statuses;
        $data['status']["all"]   = "ALL";
        $data['currency']        = Currency::getDropList();
        $data['fund_in_account'] = $accounts;
        $data['housekeep']       = BankTransaction::$housekeeps;

        return $data;
    }

    public function profileRemark()
    {
        $data = [];

        $data['category_id'] = ProfileRemark::$categories;

        return $data;
    }

    public function announcement()
    {
        $data = [];

        $data['show_type'] = Announcement::$showTypes;
        $data['category']  = Announcement::$categories;
        $data['status']    = Model::$booleanStatusesDropList;
        $data['pop_up']    = Model::$booleanDropList;
        $data['is_game']   = Model::$booleanDropList;
        $data['currency']  = Currency::getDropList();
        $data['language']  = Language::getDropList();
        $data['is_agent']  = Announcement::$booleanDropList;
        $data['content_type'] = Announcement::$contentTypes;

        return $data;
    }

    public function deposit()
    {
        $fundInAccount   = PaymentPlatform::getAll()->pluck('code', 'code')->toArray();
        $gatewaryAccount = PaymentPlatform::where('payment_type', '<>', PaymentPlatform::PAYMENT_TYPE_BANKCARD)->get()->pluck('name', "id")->toArray();

        $data = [];

        $data['payment_type']           = PaymentPlatform::$paymentTypes;
        $data['online_banking_channel'] = PaymentPlatform::$onlineBankingChannels;
        $data['status']                 = Deposit::$statues;
        $data['status']["all"]          = "ALL";
        $data['tag']                    = Deposit::$tags;
        $data['hold_reason']            = Deposit::$holdReasons;
        $data['reject_reason']          = Deposit::$rejectReasons;
        $data['currency']               = Currency::getDropList();;
        $data['fund_in_account']     = $fundInAccount;
        $data['payment_platform_id'] = $gatewaryAccount;
        $data['auto_refresh']        = Config::$autoRefreshes;
        $data['deposit_log_type']    = DepositLog::$types;

        return $data;
    }

    public function crmOrder()
    {
        $data                     = [];
        $data['user_status']      = User::$statuses;
        $data['currency']         = Currency::getDropList();
        $data['risk_group_id']    = RiskGroup::getDropList();
        $data['payment_group_id'] = PaymentGroup::getDropList();
        $data['status']           = CrmOrder::$status;
        $data['call_status']      = CrmOrder::$call_statuses;
        $data['call_log_status']  = CrmCallLog::$call_statuses;
        $data['deposit']          = CrmOrder::$booleanDropList;
        $data['type']             = CrmOrder::$type;
        $data['channel']          = CrmCallLog::$channel;
        $data['purpose']          = CrmCallLog::$purpose;
        $data['prefer_product']   = CrmCallLog::$prefer_product;
        $data['source']           = CrmCallLog::$source;
        $data['prefer_bank']      = CrmCallLog::$prefer_bank;
        $data['reason']           = CrmCallLog::$reason;
        $data['bo_admin']         = CrmBoAdmin::query()->where('status', true)->where('on_duty', true)->get();
        return $data;
    }

    public function crmAdmin()
    {
        $data            = [];
        $data['status']  = CrmBoAdmin::$statuses;
        $data['on_duty'] = CrmBoAdmin::$onDuty;
        $data['admin']   = Admin::all()->pluck('name', 'id');
        return $data;
    }

    public function crmResource()
    {
        $data                    = [];
        $data['status']          = CrmResource::$status;
        $data['call_status']     = CrmResource::$call_statuses;
        $data['call_log_status'] = CrmResourceCallLog::$call_statuses;
        $data['channel']         = CrmResourceCallLog::$channel;
        $data['purpose']         = CrmResourceCallLog::$purpose;
        $data['prefer_product']  = CrmResourceCallLog::$prefer_product;
        $data['source']          = CrmResourceCallLog::$source;
        $data['prefer_bank']     = CrmResourceCallLog::$prefer_bank;
        $data['reason']          = CrmResourceCallLog::$reason;
        $data['bo_admin']        = CrmBoAdmin::query()->where('status', true)->where('on_duty', true)->get();
        return $data;
    }

    public function crmReport()
    {
        $data         = [];
        $data['type'] = CrmWeeklyReport::$type;
        return $data;
    }

    public function crmExcludeUser()
    {
        $data                 = [];
        $data['is_affiliate'] = CrmExcludeUser::$booleanDropList;
        $data['status']       = CrmExcludeUser::$booleanStatusesDropList;
        return $data;
    }

    public function useBonusPrize()
    {
        $data           = [];
        $data['status'] = UserBonusPrize::$statuses;
        return $data;
    }

    public function bonus()
    {
        $data = [];

        $data['currency']                = Currency::getDropList();
        $data['status']                  = Model::$booleanStatusesDropList;
        $data['bonus_group_id']          = BonusGroup::getDropList();
        $data['product_code']            = GamePlatformProduct::getDropList();
        $data['category']                = Bonus::$categories;
        $data['type']                    = Bonus::$types;
        $data['cycle']                   = Bonus::$cycles;
        $data['user_type']               = Bonus::$userTypes;
        $data['risk_group_ids']          = RiskGroup::getDropList();
        $data['payment_group_ids']       = PaymentGroup::getDropList();
        $data['is_claim']                = Model::$booleanDropList;
        $data['is_auto_hold_withdrawal'] = Model::$booleanDropList;
        $data['language']                = Language::getDropList();

        return $data;
    }

    public function rebate()
    {
        $data = [];

        $data['risk_group_id']  = RiskGroup::getDropList();
        $data['vip_id']         = Vip::getDropList();
        $data['status']         = Model::$booleanStatusesDropList;
        $data['is_manual_send'] = Model::$booleanDropList;
        $data['currency']       = Currency::getDropList();
        $data['product_code']   = GamePlatformProduct::getDropList();

        return $data;
    }

    public function withdrawal()
    {
        $data = [];

        $companyBankAccountCodes = CompanyBankAccount::getWithdrawalTypeAccount()->pluck('code', 'code')->toArray();

        $data['status']                    = Withdrawal::$statuses;
        $data['company_bank_account_code'] = $companyBankAccountCodes;
        $data['hold_reason']               = Withdrawal::$holdReasons;
        $data['reject_reason']             = Withdrawal::$rejectReasons;
        $data['escalate_reason']           = Withdrawal::$escalateReasons;
        $data['currency']                  = Currency::getDropList();

        return $data;
    }

    public function remark()
    {
        $data = [];

        $data['type']         = Remark::$types;
        $data['category']     = Remark::$categories;
        $data['sub_category'] = Remark::$subCategoriesRelated;
        return $data;
    }

    public function adjustment()
    {
        $data = [];

        $data['type']               = Adjustment::$types;
        $data['category']           = Adjustment::$userCategories;
        $data['affiliate_category'] = Adjustment::$affiliateCategory;
        $data['status']             = Adjustment::$statuses;
        $data['platform_code']      = GamePlatform::getDropList(true);

        return $data;
    }

    public function companyBankAccount()
    {
        $data = [];

        $data['bank_id']          = Bank::getDropList();
        $data['payment_group_id'] = PaymentGroup::getDropList();
        $data['type']             = CompanyBankAccount::$types;
        $data['status']           = CompanyBankAccount::$statuses;
        $data['remark']           = CompanyBankAccount::$remarks;
        $data['is_income']        = CompanyBankAccount::$isIncomes;
        $data['account_id']       = CompanyBankAccount::getDropList();
        $data['currency']         = Currency::getDropList();
        $data['otp']              = CompanyBankAccount::$otps;
        $data['app_related']      = CompanyBankAccount::$appRelates;
        $data['reason']           = CompanyBankAccountTransaction::$reasons;

        return $data;
    }

    public function companyBankAccountTransaction()
    {
        $data = [];

        $data['company_bank_account_code'] = CompanyBankAccount::getCodeDropList();

        return $data;
    }

    public function bank()
    {
        $data = [];

        $data['status']   = Model::$booleanStatusesDropList;
        $data['language'] = Language::getDropList();
        $data['currency'] = Currency::getDropList();
        $data['is_auto_deposit'] = Model::$booleanDropList;

        return $data;
    }

    public function userMessage()
    {
        $data = [];

        $data['category']      = UserMessage::$categories;
        $data['member_status'] = User::$statuses;

        return $data;
    }

    public function notification()
    {
        $data = [];

        $data['category']      = DatabaseNotification::$categories;
        $data['member_status'] = User::$statuses;
        $data['currency']      = Currency::getDropList();

        return $data;
    }

    public function gamePlatforms()
    {
        $data = [];

        $data['status']        = Model::$booleanStatusesDropList;
        $data['platform_code'] = GamePlatform::getDropListContainMainWallet();


        return $data;
    }

    public function gamePlatformProduct()
    {
        $data = [];

        $data['platform_code'] = GamePlatform::getDropList(true);
        $data['type']          = GamePlatformProduct::$types;
        $data['status']        = Model::$booleanStatusesDropList;
        $data['device']        = User::$devices;
        $data['currency']      = Currency::getDropList();
        $data['language']      = Language::getDropList();
        $data['img']           = GamePlatformProduct::$imgFields;

        return $data;
    }

    public function gamePlatformPullReportSchedule()
    {
        $data = [];

        $data['platform_code'] = GamePlatform::getDropList(true);
        $data['status']        = GamePlatformPullReportSchedule::$statuses;

        return $data;
    }

    public function game()
    {
        $data = [];

        $data['platform_code'] = GamePlatform::getDropList(true);
        $data['product_code']  = GamePlatformProduct::getDropList();
        $data['type']          = GamePlatformProduct::$types;
        $data['status']        = Model::$booleanStatusesDropList;
        $data['device']        = User::$devices;
        $data['currency']      = Currency::getDropList();
        $data['language']      = Language::getDropList();
        $data['img']           = Game::$imgFields;

        return $data;
    }

    public function accessLog()
    {
        $data = [];

        $data['status']   = Model::$booleanStatusesDropListSuccessFailed;
        $data['currency'] = Currency::getDropList();

        return $data;
    }

    public function memberDepositDevice()
    {
        $data = [];

        $data['status']   = Model::$booleanStatusesDropListSuccessFailed;
        $data['currency'] = Currency::getDropList();
        $data['device']   = Model::$deviceDropList;

        return $data;
    }

    public function promotionType()
    {
        $data             = [];
        $data['status']   = Model::$booleanStatusesDropList;
        $data['currency'] = Currency::getDropList();
        $data['language'] = Language::getDropList();

        return $data;
    }

    public function promotion()
    {
        $data                        = [];
        $data['promotion_type_code'] = PromotionType::getDropList();
        $data['status']              = Model::$booleanStatusesDropList;
        $data['currency']            = Currency::getDropList();
        $data['language']            = Language::getDropList();
        $data['related_type']        = Promotion::$relatedTypes;
        $data['is_can_claim']        = Model::$booleanDropList;

        return $data;
    }

    public function banner()
    {
        $data                = [];
        $data['currency']    = Currency::getDropList();
        $data['show_type']   = Banner::$showTypes;
        $data['target_type'] = Banner::$targetTypes;
        $data['position']    = Banner::$positions;
        $data['is_agent']    = Banner::$is_agent;
        $data['status']      = Model::$booleanStatusesDropList;
        $data['language']    = Language::getDropList();

        return $data;
    }

    public function affiliate()
    {
        $data             = [];
        $data['currency'] = Currency::getDropList();
        $data['status']   = User::$affiliateStatuses;
        $data['language'] = Language::getDropList();

        return $data;
    }

    public function userBonusPrize()
    {
        $data                 = [];
        $data['product_code'] = GamePlatformProduct::getDropList();
        $data['currency']     = Currency::getDropList();
        return $data;
    }

    public function userRebatePrize()
    {
        $data                  = [];
        $data['product_code']  = GamePlatformProduct::getDropList();
        $data['vip_id']        = Vip::getDropList();
        $data['risk_group_id'] = RiskGroup::getDropList();
        $data['marketing_initiate_payout'] = [
            '1' => 'YES',
            '0' => 'NO',
        ];
        $data['payment_initiate_payout'] = [
            '1' => 'YES',
            '0' => 'NO',
        ];
        return $data;
    }

    public function promotionClaimUser()
    {
        $data             = [];
        $data['currency'] = Currency::getDropList();
        $data['status']   = PromotionClaimUser::$frontStatuses;
        return $data;
    }

    public function rebateComputationReport()
    {
        $data                 = [];
        $data['product_code'] = GamePlatformProduct::getDropList();
        return $data;
    }

    public function affiliateCommissions()
    {
        $data                 = [];
        $data['product_type'] = GamePlatformProduct::$types;
        $data['currency']     = Currency::getDropList();
        $data['status']       = AffiliateCommission::$statuses;
        return $data;
    }

    public function affiliateAnnouncement()
    {
        $data = [];

        $data['category'] = AffiliateAnnouncement::$categories;
        $data['status']   = Model::$booleanStatusesDropList;
        $data['currency'] = Currency::getDropList();
        $data['language'] = Language::getDropList();
        $data['pop_up']   = Model::$booleanDropList;

        return $data;
    }

    public function gameBetDetail()
    {
        $data = [];

        $data['user_currency']   = Currency::getDropList();
        $data['product_code']    = GamePlatformProduct::getDropList();
        $data['status']          = GameBetDetail::$statuses;
        $data['platform_status'] = GameBetDetail::$platformStatuses;

        return $data;
    }

    public function mailboxTemplate()
    {
        $data = [];

        $data['type']     = MailboxTemplate::$types;
        $data['currency'] = Currency::getDropList();
        $data['language'] = Language::getDropList();

        return $data;
    }

    public function paymentPlatform()
    {
        $data['status']              = PaymentPlatform::$booleanStatusesDropList;
        $data['is_fee']              = PaymentPlatform::$booleanDropList;
        $data['currencies']          = Currency::getDropList();
        $data['payment_type']        = PaymentPlatform::$paymentTypes;
        $data['request_type']        = PaymentPlatform::$requestTypes;
        $data['is_need_type_amount'] = PaymentPlatform::$booleanDropList;
        $data['show_type']           = PaymentPlatform::$showTypes;

        return $data;
    }

    public function userProductDailyReport()
    {
        $data['product_code'] = GamePlatformProduct::getDropList();
        return $data;
    }

    public function creativeResource()
    {
        $data['type']          = CreativeResource::$type;
        $data['size']          = remove_null(CreativeResource::$size);
        $data['group']         = CreativeResource::$group;
        $data['tracking_name'] = TrackingStatistic::getDropList();
        $data['currency']      = Currency::getDropList();
        $data['device']        = User::$devices;
        return $data;
    }

    public function activeUserReport()
    {
        $data['currency'] = Currency::getDropList();
        return $data;

    }

    public function contactUs()
    {
        $data['currency'] = Currency::getDropList();
        $data['language'] = Language::getDropList();
        return $data;
    }

    public function riskCategoryListing()
    {
        $data['currency']  = Currency::getDropList();
        $data['behaviour'] = UserRisk::$behaviour;
        $data['risk']      = UserRisk::$risk;
        return $data;
    }

    public function affiliateRequest()
    {
        $data              = [];
        $data['currency']  = Currency::getDropList();
        $data['cs_status'] = Affiliate::$csStatuses;
        return $data;
    }

    public function trackingStatistic()
    {
        $data             = [];
        $data['currency'] = Currency::getDropList();
        $data['status']   = User::$affiliateStatuses;
        return $data;
    }

    public function domainManagement()
    {
        $data             = [];
        $data['currency'] = Currency::getDropList();
        $data['type']     = Url::$type;
        $data['platform'] = Url::$platform;
        $data['device']   = User::$devices;
        return $data;
    }

    public function userRiskSummary()
    {
        $data                     = [];
        $data['currency']         = Currency::getDropList();
        $data['product_code']     = GamePlatformProduct::getDropList();
        $data['risk_group_id']    = RiskGroup::getDropList();
        $data['payment_group_id'] = PaymentGroup::getDropList();
        $data['deposit']          = Model::$booleanDropList;
        return $data;
    }

    public function paymentPlatformChannel()
    {
        $data                           = [];
        $data['payment_type']           = PaymentPlatform::$paymentTypes;
        $data['online_banking_channel'] = PaymentPlatform::$onlineBankingChannels;
        return $data;
    }

    public function manualDepositBank()
    {
        $data         = [];
        $currency     = request()->header('currency');
        $data['bank'] = Bank::getFrontDropList($currency);
        return $data;
    }

    public function transferDetail()
    {
        $data           = [];
        $data['status'] = TransferDetail::$statuses;
        return $data;
    }

    public function affiliateLink()
    {
        $data             = [];
        $data['type']     = AffiliateLink::$type;
        $data['platform'] = AffiliateLink::$platform;
        $data['status']   = AffiliateLink::$status;
        $data['currency'] = Currency::getDropList();
        $data['language'] = Language::getDropList();
        return $data;
    }

    public function pgAccountManagement()
    {
        $data = [];

        $data['status'] = PgAccount::$statuses;

        $data['account_id'] = PgAccount::where('status', PgAccount::STATUS_ACTIVE)->pluck('payment_platform_code', 'id')->toArray();

        $data['bank'] = CompanyBankAccount::where('status',CompanyBankAccount::STATUS_ACTIVE)->pluck('code', 'id')->toArray();

        $data['is_income'][0] = 'Debit';
        $data['is_income'][1] = 'Credit';

        $data['otp']  = PgAccount::$otps;


        return $data;
    }


    public function riskGroupRules()
    {
        $data = [];
        $data['rules'] = RiskGroup::$ruleLists;
        return $data;
    }

    public function riskGroup()
    {
        $data = [];
        $riskGroups =  RiskGroup::getAll();
        if ($riskGroups){
            foreach ($riskGroups as $riskGroup){
                $data['risk_group'][$riskGroup->id] = $riskGroup;
            }
        }else{
            $data['risk_group'] = [];
        }
        return $data;
    }

    public function transaction()
    {
        $data               = [];
        $data['is_income']  = Transaction::$isIncomes;
        $data['type_group'] = Transaction::$typeGroups;
        return $data;
    }
}
