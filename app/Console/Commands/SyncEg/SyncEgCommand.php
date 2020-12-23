<?php

namespace App\Console\Commands\SyncEg;

use App\Models\Affiliate;
use App\Models\Bank;
use App\Models\SyncThLog;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\UserBankAccount;
use App\Models\UserInfo;
use App\Repositories\GamePlatformUserRepository;
use App\Repositories\UserRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncEgCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:sync-eg {--is_agent=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $syncLog;
    protected $egUserModel;
    protected $egUserInfoModel;
    protected $egUserAccountModel;
    protected $egUserBankAccountModel;
    protected $egBankModel;
    protected $egAffiliateModel;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->egUserModel = (new User())->setConnection('eg');
        $this->egUserInfoModel = (new UserInfo())->setConnection('eg');
        $this->egUserAccountModel = (new UserAccount())->setConnection('eg');
        $this->egUserBankAccountModel = (new UserBankAccount())->setConnection('eg');
        $this->egBankModel = (new Bank())->setConnection('eg');
        $this->egAffiliateModel = (new Affiliate())->setConnection('eg');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $isAgent = (bool)$this->option('is_agent');

        if ($isAgent) {
            $egUsers = $this->egUserModel->where('is_agent', true)
                ->orderBy('parent_id_list')
                ->get();
        } else {
            $egUsers = $this->egUserModel->where('is_agent', false)
                ->whereIn('status', [1,3,5])
                ->where(function($query) {
                    $query->whereNotNull('first_deposit_at')
                        ->orWhereRaw("id in (select DISTINCT user_id from user_product_daily_reports where stake>0)");
                })
                ->whereRaw("id in (select DISTINCT user_id from user_login_logs where created_at >='2019-12-20 00:00:00')")
                ->get();
        }

        $this->info('总共需要迁移 ' . $egUsers->count() . '条');
        $this->info('----------------------开始迁移----------------------');

        foreach ($egUsers as $key => $egUser) {

            $this->info('迁移：' . ($key+1));

            # 如果迁移过了直接进入下一次
            if ($this->isSynced($egUser->id, $isAgent)) {
                $this->info($egUser->name . ' 已迁移');
                continue;
            }

            $egUserInfo = $this->egUserInfoModel->where('user_id', $egUser->id)->first();
            $this->initSyncLog($egUser, $egUserInfo, $isAgent);

            try {
                DB::transaction(function() use ($isAgent, $egUser, $egUserInfo) {
                    $this->sync($isAgent, $egUser, $egUserInfo);
                });
            } catch (\Exception $e) {
                $this->info('迁移 ' . $egUser->name . ' 失败，失败原因：' . $e->getMessage());

                # 判断是否已有迁移失败记录，如果存在则不记录
                if (!$this->isSynced($egUser->id, $isAgent, false)) {
                    $this->syncLog['remark'] = $e->getMessage();
                    SyncThLog::query()->create($this->syncLog);
                }
                continue;
            }

            $this->info('迁移 ' . $egUser->name . ' 成功。');
            $this->syncLog['status'] = true;
            SyncThLog::query()->create($this->syncLog);

        }

        $this->info('----------------------结束迁移----------------------');
        $failCount = SyncThLog::query()->where('is_agent', $isAgent)->where('status', 0)->count();
        $this->info('失败数：' . $failCount);

    }

    public function sync($isAgent, $egUser, $egUserInfo)
    {
        # 初始化内容
        if ($this->checkEmailIsExist($egUserInfo->email, $isAgent)) {
            $egUserInfo->email = $this->findNewEmail($egUserInfo->email, $isAgent);
            $egUser->status = User::STATUS_LOCKED;
        }

        if ($this->checkPhoneIsExist($egUserInfo->phone, $isAgent)) {
            $egUserInfo->phone = $this->findNewPhone($egUserInfo->phone, $isAgent);
            $egUser->status = User::STATUS_LOCKED;
        }

        if ($this->checkAffiliateCodeIsExist($egUser->affiliate_code)) {
            $egUser->affiliate_code = UserRepository::findAvailableAffiliateCode();
            $egUser->status = User::STATUS_LOCKED;
        }

        if ($this->checkUserNameIsExist($egUser->name, $isAgent)) {
            $egUser->name = $this->findNewName($egUser->name, $isAgent);
            $egUser->status = User::STATUS_BLOCKED;
        }

        $this->syncLog['new_name']              = $egUser->name;
        $this->syncLog['new_email']             = $egUserInfo->email;
        $this->syncLog['new_phone']             = $egUserInfo->phone;
        $this->syncLog['new_affiliate_code']    = $egUser->affiliate_code;

        $parentId       = null;
        $parentIdList   = '';
        $parentName     = '';
        $parentNameList = '';
        $parentCode     = '';
        # 获取上级id
        if (!empty($egUser->parent_id) && $parent = $this->getParent($egUser->parent_id)) {
            $this->syncLog['old_parent_id'] = $egUser->parent_id;
            if (!empty($parent)) {
                $this->syncLog['new_parent_id'] = $parent->id;
                $parentCode     = $parent->affiliate_code;
                $parentId       = $parent->id;
                $parentIdList   = !empty($parent->parent_id_list) ? $parent->parent_id_list . ',' . $parentId : $parentId;
                $parentName     = $parent->name;
                $parentNameList = !empty($parent->parent_name_list) ? $parent->parent_name_list . ',' . $parent->name : $parent->name;
            }
        }

        $egUserArray = $egUser->makeVisible(['fund_password', 'password'])->toArray();
        unset($egUserArray['id']);

        $egUserArray['referral_code']       = UserRepository::findAvailableReferralCode();
        $egUserArray['affiliate_code']      = $isAgent ? $egUser->affiliate_code : null;
        $egUserArray['affiliated_code']     = $parentCode;
        $egUserArray['parent_id']           = $parentId;
        $egUserArray['parent_id_list']      = $parentIdList;
        $egUserArray['parent_name']         = $parentName;
        $egUserArray['parent_name_list']    = $parentNameList;
        $egUserArray['updated_at']          = '2019-11-11 11:11:11';

        $hl8User = User::query()->create($egUserArray);
        $this->syncLog['new_id'] = $hl8User->id;

        # userInfo
        $egUserInfo->user_id     = $hl8User->id;
        $egUserInfo->describe    = 'eg_migrate';
        $egUserInfo->updated_at  = '2019-11-11 11:11:11';
        $egUserInfoArray = $egUserInfo->toArray();
        unset($egUserInfoArray['id']);
        UserInfo::query()->create($egUserInfoArray);

        # 拼接user_accounts数据
        $egUserAccount = $this->egUserAccountModel->where('user_id', $egUser->id)->first();
        $egUserAccount->user_id     = $hl8User->id;
        $egUserAccount->updated_at  = '2019-11-11 11:11:11';
        $egUserAccountArray = $egUserAccount->toArray();
        unset($egUserAccountArray['id']);
        UserAccount::query()->create($egUserAccountArray);

        # user_bank_accounts
        $egUserBankAccounts = $this->egUserBankAccountModel->where('user_id', $egUser->id)->whereNull('deleted_at')->get();
        foreach ($egUserBankAccounts as $egUserBankAccount)  {
            $egBank = $this->egBankModel->find($egUserBankAccount->bank_id);
            if (empty($egBank)) {
                continue;
            }
            $bank = Bank::query()->where('code', $egBank->code)->first();
            if (empty($bank)) {
                continue;
            }
            $egUserBankAccount->user_id = $hl8User->id;
            $egUserBankAccount->bank_id = $bank->id;
            $egUserBankAccountArray = $egUserBankAccount->toArray();
            unset($egUserBankAccountArray['id']);
            UserBankAccount::query()->create($egUserBankAccountArray);
        }

        # 代理数据
        if ($isAgent) {
            $egAffiliate = $this->egAffiliateModel->where('user_id', $egUser->id)->first();
            $egAffiliate->code          = $egUser->affiliate_code;
            $egAffiliate->refer_by_code = $parentCode;
            $egAffiliate->user_id       = $hl8User->id;
            $egAffiliate->updated_at    = '2019-11-11 11:11:11';
            $egAffiliateArray = $egAffiliate->toArray();
            unset($egAffiliateArray['id']);
            Affiliate::query()->create($egAffiliateArray);
        } else {
            // 建立所有会员第三方钱包, 且代理不需第三方钱包
            GamePlatformUserRepository::userRegisterAllPlatform($hl8User);
        }
    }

    public function isSynced($memId, $isAgent, $status=true)
    {
        $log = SyncThLog::query()->where('old_id', $memId)->where('is_agent', $isAgent)->where('status', $status)->first();
        return !empty($log);
    }

    # 初始化日志资料
    public function initSyncLog($egUser, $egUserInfo, $isAgent)
    {
        $this->syncLog = [];
        $this->syncLog['old_id']                = $egUser->id;
        $this->syncLog['old_parent_id']         = null;
        $this->syncLog['new_parent_id']         = null;
        $this->syncLog['old_name']              = $egUser->name;
        $this->syncLog['new_name']              = '';
        $this->syncLog['old_email']             = $egUserInfo->email;
        $this->syncLog['new_email']             = '';
        $this->syncLog['old_phone']             = $egUserInfo->phone;
        $this->syncLog['new_phone']             = '';
        $this->syncLog['old_affiliate_code']    = $egUser->affiliate_code;
        $this->syncLog['new_affiliate_code']    = null;
        $this->syncLog['is_agent']              = $isAgent;
        $this->syncLog['status']                = false;
    }

    public function getParent($oldId)
    {
        $log = SyncThLog::query()->where('old_id', $oldId)->where('is_agent', true)->where('status', true)->first();
        if ($log) {
            return UserRepository::find($log->new_id);
        } else {
            return null;
        }
    }

    # 检查用户名是否已经存在于越南系统
    public function checkUserNameIsExist($name, $isAgent)
    {
        $user = User::where('name',$name)->where('is_agent', $isAgent)->first();

        return !empty($user);
    }

    public function findNewName($name, $isAgent)
    {
        do {
            $name = str_random(10) . '__' . $name;
        } while (User::where('name', $name)->where('is_agent', $isAgent)->exists());

        return $name;
    }

    // 检查手机号是否已经存在于越南系统
    public function checkPhoneIsExist($phone, $isAgent)
    {
        if (empty($phone)) {
            return false;
        }
        $user = UserInfo::where('phone',$phone)->where('is_agent',$isAgent)->first();

        return !empty($user);
    }

    public function findNewPhone($phone, $isAgent)
    {
        do {
            $phone = str_random(10) . '__' . $phone;
        } while (UserInfo::where('phone', $phone)->where('is_agent', $isAgent)->exists());

        return $phone;
    }

    // 检查email是否已经存在于越南系统
    public function checkEmailIsExist($email, $isAgent)
    {
        if (empty($email)) {
            return false;
        }

        $user = UserInfo::where('email',$email)->where('is_agent',$isAgent)->first();

        return !empty($user);
    }

    public function checkAffiliateCodeIsExist($code)
    {
        if (empty($code)) {
            return false;
        }

        $user = User::where('affiliate_code', $code)->first();

        return !empty($user);
    }

    public function findNewEmail($email, $isAgent)
    {
        do {
            $email = str_random(10) . '__' . $email;
        } while (UserInfo::where('email', $email)->where('is_agent', $isAgent)->exists());

        return $email;
    }
}
