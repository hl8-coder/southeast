<?php

namespace App\Console\Commands\SyncThDataToVn;

use App\Imports\SyncThDataToVnImport;
use App\Models\Affiliate;
use App\Models\Bank;
use App\Models\SyncThLog;
use App\Models\User;
use App\Models\Config;
use App\Models\UserAccount;
use App\Models\UserBankAccount;
use App\Models\UserInfo;
use App\Repositories\GamePlatformUserRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SyncThCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:sync-th {--is_agent=} {--page=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步代理基础信息';

    protected $th_oracle_connect;

    protected $ThUsers;
    protected $ThUserBankAccounts;
    protected $syncLog;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $isAgent = $this->option('is_agent');
        $page    = $this->option('page');
        $this->initThData($isAgent, $page);

        # 添加银行数据
        $this->insertBankData();

        $this->info('总共需要迁移 ' . $this->ThUsers->count() . '条');
        $this->info('----------------------开始迁移----------------------');

        $now = now();
        $successNum = 1;

        foreach ($this->ThUsers as $thUser) {

            $this->info('迁移：' . $successNum);
            $successNum++;

            # 如果迁移过了直接进入下一次
            if ($this->isSynced($thUser['mem_id'], $isAgent)) {
                $this->info($thUser['username'] . ' 已迁移');
                continue;
            }

            # 初始化内容
            $this->initSyncLog($thUser, $isAgent);

            try {
                DB::transaction(function() use ($isAgent, $thUser, $now) {
                    $this->sync($isAgent, $thUser, $now);
                });
            } catch (\Exception $e) {
                $this->info('迁移 ' . $thUser['username'] . ' 失败，失败原因：' . $e->getMessage());

                # 判断是否已有迁移失败记录，如果存在则不记录
                if (!$this->isSynced($thUser['mem_id'], $isAgent, false)) {
                    $this->syncLog['remark'] = $e->getMessage();
                    SyncThLog::query()->create($this->syncLog);
                }
                continue;
            }
            $this->info('迁移 ' . $thUser['username'] . ' 成功。');
            $this->syncLog['status'] = true;
            SyncThLog::query()->create($this->syncLog);

        }

        $this->info('----------------------结束迁移----------------------');
        $failCount = SyncThLog::query()->where('is_agent', $isAgent)->where('status', 0)->count();
        $this->info('失败数：' . $failCount);

    }

    public function initThData($isAgent, $page)
    {
        $import = new SyncThDataToVnImport();
        $path   = app_path() . '/Console/Commands/SyncThDataToVn/';
        $prefix = $isAgent ? 'agent_' : '';
//        $page = !empty($page) ? $page . '_' : '';
        $this->ThUsers                = $this->dealImportData(Excel::toArray($import, $path . $prefix  . 'member.xlsx')[0]);
        $this->ThUserBankAccounts     = $this->dealImportData(Excel::toArray($import, $path . 'member_bank_account.xlsx')[0]);
    }

    public function insertBankData()
    {
        $bankCodes = $this->ThUserBankAccounts->pluck('bank_code')->unique();
        $banks = [];
        foreach ($bankCodes as $bankCode) {
            $bank = [];
            $bank['name']       = $bankCode;
            $bank['code']       = $bankCode;
            $bank['currency']   = 'THB';
            $banks[] = $bank;
        }
        batch_insert('banks', $banks, true);
    }

    public function isSynced($memId, $isAgent, $status=true)
    {
        $log = SyncThLog::query()->where('old_id', $memId)->where('is_agent', $isAgent)->where('status', $status)->first();
        return !empty($log);
    }

    public function sync($isAgent, $thUser, $now)
    {

        $userStatus = $this->getUserStatusMapping($thUser['status']);

        if ($this->checkUserNameIsExist($thUser['username'], $isAgent)) {
            $thUser['username'] = $this->findNewName($thUser['username'], $isAgent);
            $userStatus = User::STATUS_BLOCKED;
        }

        if ($this->checkEmailIsExist($thUser['email'], $isAgent)) {
            $thUser['email'] = $this->findNewEmail($thUser['email'], $isAgent);
            $userStatus = User::STATUS_LOCKED;
        }

        if ($this->checkPhoneIsExist($thUser['phone'], $isAgent)) {
            $thUser['phone'] = $this->findNewPhone($thUser['phone'], $isAgent);
            $userStatus = User::STATUS_LOCKED;
        }

        if (in_array($thUser['username'], ['zodiacgum', 'malachi429', 'launchgame1'])) {
            $userStatus = User::STATUS_BLOCKED;
        }

        $this->syncLog['new_name']  = $thUser['username'];
        $this->syncLog['new_email'] = $thUser['email'];
        $this->syncLog['new_phone'] = $thUser['phone'];

        $parentId       = null;
        $parentIdList   = '';
        $parentName     = '';
        $parentNameList = '';
        $parentCode     = '';
        # 获取上级id
        if (!empty($thUser['ma_id']) && $parent = $this->getParent($thUser['ma_id'])) {
            $this->syncLog['old_parent_id'] = $thUser['ma_id'];
            if (!empty($parent)) {
                $this->syncLog['new_parent_id'] = $parent->id;
                $parentCode     = $parent->affiliate_code;
                $parentId       = $parent->id;
                $parentIdList   = !empty($parent->parent_id_list) ? $parent->parent_id_list . ',' . $parentId : $parentId;
                $parentName     = $parent->name;
                $parentNameList = !empty($parent->parent_name_list) ? $parent->parent_name_list . ',' . $parent->name : $parent->name;
            }
        }

        # 拼接user数据
        $userData = [];
        $userData['currency']                   = 'THB';
        $userData['language']                   = 'th';
        $userData['name']                       = $thUser['username'];
        $userData['password']                   = '';
        $userData['fund_password']              = $thUser['password'];
        $userData['vip_id']                     = Config::findValue('default_vip_id', null);
        $userData['reward_id']                  = Config::findValue('default_reward_id', null);
        $userData['risk_group_id']              = Config::findValue('default_risk_group_id', null);
        $userData['payment_group_id']           = Config::findValue('default_thb_payment_group_id', null);
        $userData['is_agent']                   = $isAgent;
        $userData['is_need_change_password']    = false;
        $userData['referral_code']              = UserRepository::findAvailableReferralCode();
        $userData['referrer_code']              = '';
        $userData['notification_count']         = 0;
        $userData['status']                     = $userStatus;
        $userData['odds']                       = User::ODDS_MALAY;
        $userData['security_question']          = null;
        $userData['security_question_answer']   = '';
        $userData['affiliate_code']             = $isAgent ? $thUser['affiliate_code'] : null;
        $userData['affiliated_code']            = $parentCode;
        $userData['parent_id']                  = $parentId;
        $userData['parent_id_list']             = $parentIdList;
        $userData['parent_name']                = $parentName;
        $userData['parent_name_list']           = $parentNameList;
        $userData['is_test']                    = false;
        $userData['created_at']                 = Carbon::parse($thUser['created_at'])->toDateTimeString();
        $userData['updated_at']                 = '2011-11-11 11:11:11';
        $userData['first_deposit_at']           = $thUser['is_deposited'] == 'Y' ? $now : null;

        $user = User::query()->create($userData);

        $this->syncLog['new_id'] = $user->id;

        # 拼接user_info
        $userInfoData = [];
        $userInfoData['is_agent']                   = $isAgent;
        $userInfoData['user_id']                    = $user->id;
        $userInfoData['full_name']                  = !empty($thUser['bank_acct_name']) ? $thUser['bank_acct_name'] : '';
        $userInfoData['gender']                     = $this->getGenderMapping($thUser['gender']);
        $userInfoData['address']                    = '';
        $userInfoData['email']                      = $thUser['email'];
        $userInfoData['email_verified_at']          = $thUser['email_verified'] == 'Y' ? $now : null;
        $userInfoData['country_code']               = '+66';
        $userInfoData['phone']                      = $thUser['phone'];
        $userInfoData['other_contact']              = '';
        $userInfoData['phone_verified_at']          = $thUser['phone_verified'] == 'Y' ? $now : null;
        $userInfoData['bank_account_verified_at']   = $thUser['is_bankacct_verified'] == 'Y' ? $now : null;
        $userInfoData['claimed_verify_prize_at']    = null;
        $userInfoData['profile_verified_at']        = null;
        $userInfoData['birth_at']                   = Carbon::parse($thUser['birth'])->toDateTimeString();
        $userInfoData['avatar']                     = '';
        $userInfoData['describe']                   = 'migrate';
        $userInfoData['register_url']               = '';
        $userInfoData['register_ip']                = '';
        $userInfoData['last_login_ip']              = '';
        $userInfoData['last_login_at']              = null;
        $userInfoData['old_token']                  = '';
        $userInfoData['zip_code']                   = $thUser['post_code'];
        $userInfoData['city']                       = '';
        $userInfoData['web_url']                    = '';
        $userInfoData['created_at']                 = Carbon::parse($thUser['created_at'])->toDateTimeString();
        $userInfoData['updated_at']                 = '2011-11-11 11:11:11';
        UserInfo::query()->create($userInfoData);

        # 拼接user_accounts数据
        $userAccountData = [];
        $userAccountData['user_id']                 = $user->id;
        $userAccountData['total_balance']           = $thUser['balance'];
        $userAccountData['freeze_balance']          = 0;
        $userAccountData['total_point_balance']     = 0;
        $userAccountData['created_at']              = Carbon::parse($thUser['created_at'])->toDateTimeString();
        $userAccountData['updated_at']              = '2011-11-11 11:11:11';
        UserAccount::query()->create($userAccountData);

        # 会员银行卡
        $thUserBankAccounts = $this->ThUserBankAccounts->where('mem_id', $thUser['mem_id']);
        foreach ($thUserBankAccounts as $thUserBankAccount)  {
            $bank = Bank::query()->where('code', $thUserBankAccount['bank_code'])->where('currency', 'THB')->first();
            $userBankAccount = [];
            $userBankAccount['user_id']         = $user->id;
            $userBankAccount['bank_id']         = $bank->id;
            $userBankAccount['is_preferred']    = false;
            $userBankAccount['province']        = !empty($thUserBankAccount['state']) ? $thUserBankAccount['state'] : '';
            $userBankAccount['city']            = !empty($thUserBankAccount['city']) ? $thUserBankAccount['city'] : '';
            $userBankAccount['branch']          = !empty($thUserBankAccount['branch']) ? $thUserBankAccount['branch'] : '';
            $userBankAccount['account_name']    = !empty($thUserBankAccount['acct_name']) ? $thUserBankAccount['acct_name'] : '';
            $userBankAccount['status']          = $this->getBankStatusMapping($thUserBankAccount['status']);
            $userBankAccount['last_used_at']    = null;
            $userBankAccount['created_at']      = Carbon::parse($thUserBankAccount['updated_at'])->toDateTimeString();
            $userBankAccount['updated_at']      = '2011-11-11 11:11:11';
            $userBankAccount['deleted_at']      = null;
            UserBankAccount::query()->create($userBankAccount);
        }

        # 代理数据
        if ($isAgent) {
            $affiliateData = [];
            $affiliateData['user_id']                   = $user->id;
            $affiliateData['code']                      = $thUser['affiliate_code'];
            $affiliateData['refer_by_code']             = $parentCode;
            $affiliateData['is_fund_open']              = false;
            $affiliateData['commission_setting']        = Config::findByCodeFromCache('affiliate_commission_limit');
            $affiliateData['cs_status']                 = Affiliate::CS_STATUS_APPROVED;
            $affiliateData['cs_cycles']                 = Affiliate::CS_CYCLE_ONE_MONTH;
            $affiliateData['cs_status_last_updated_at'] = $now;
            $affiliateData['cs_last_updated_name']      = 'system';
            $affiliateData['is_become_user']            = false;
            $affiliateData['total_member']              = 0;  # 后续更新
            $affiliateData['active_member']             = 0;
            $affiliateData['new_sign_count']            = 0;
            $affiliateData['new_sign_deposit_count']    = 0;
            $affiliateData['sub_active_member']         = 0;
            $affiliateData['sub_active_member']         = 0;
            $affiliateData['click']                     = 0;
            $affiliateData['web_url']                   = '';
            $affiliateData['redirect_page']             = '';
            $affiliateData['admin_name']                = 'system';
            $affiliateData['created_at']                = $now;
            $affiliateData['updated_at']                = '2011-11-11 11:11:11';

            Affiliate::query()->create($affiliateData);
        } else {
            // 建立所有会员第三方钱包, 且代理不需第三方钱包
            GamePlatformUserRepository::userRegisterAllPlatform($user);
        }
    }

    # 初始化日志资料
    public function initSyncLog($thUser, $isAgent)
    {
        $this->syncLog = [];
        $this->syncLog['old_id']          = $thUser['mem_id'];
        $this->syncLog['old_parent_id']   = null;
        $this->syncLog['new_parent_id']   = null;
        $this->syncLog['old_name']        = $thUser['username'];
        $this->syncLog['new_name']        = $thUser['username'];
        $this->syncLog['old_email']       = $thUser['email'];
        $this->syncLog['new_email']       = $thUser['email'];
        $this->syncLog['old_phone']       = $thUser['phone'];
        $this->syncLog['new_phone']       = $thUser['phone'];
        $this->syncLog['is_agent']        = $isAgent;
        $this->syncLog['status']          = false;
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

    public function findNewEmail($email, $isAgent)
    {
        do {
            $email = str_random(10) . '__' . $email;
        } while (UserInfo::where('email', $email)->where('is_agent', $isAgent)->exists());

        return $email;
    }

    public function getUserStatusMapping($user_status_th)
    {
        $user_status_mapping = array(
            'A' => 1,
            'I' => 4,
            'S' => 2,
            'R' => 2,
            'H' => 2,
            'F' => 2,
            'B' => 2,
            'Y' => 2,
            'N' => 2,
            'N1'=> 2,
            'V' => 2,
            'L' => 2,
        );

        // 1active(正常活跃状态) 2blocked(强制退出该用户) 3locked(输入错误5次密码锁定) 4inactive(连续30天未登录)
        return !empty($user_status_mapping[$user_status_th]) ? $user_status_mapping[$user_status_th]:2;
    }

    /**
     * 泰国-越南性别映射关系
     *
     * @param $gender
     * @return string
     */
    public function getGenderMapping($gender)
    {
        return !empty($gender_mapping[$gender]) ? $gender_mapping[$gender] : "male";
    }

    /**
     * 泰-越南 银行卡状态映射关系.
     *
     * @param $th_bank_status
     * @return mixed|string
     */
    public function getBankStatusMapping($th_bank_status)
    {
        $bank_status_mapping = array(
            'A' => UserBankAccount::STATUS_ACTIVE,
            'S' => UserBankAccount::STATUS_INACTIVE,
            'D' => UserBankAccount::STATUS_DELETED,
        );

        return !empty($bank_status_mapping[$th_bank_status]) ? $bank_status_mapping[$th_bank_status] : 1;
    }

    public function dealImportData($rows)
    {
        $data   = [];
        $fields = [];
        foreach ($rows as $key => $row) {
            if ($key == 0) {
                $fields = $row;
            } else {
                $tmp = [];
                foreach ($fields as $k=> $field) {
                    $tmp[strtolower($field)] = $row[$k];
                }
                $data[] = $tmp;
            }
        }
        return collect($data);
    }

}
