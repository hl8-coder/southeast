<?php

use App\Models\Config;
use Illuminate\Database\Seeder;

class ConfigsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('configs')->truncate();

        $configs = [
            [
                'code'          => 'operation_id',
                'name'          => '品牌',
                'group'         => Config::GROUP_USER,
                'remarks'       => '品牌',
                'is_front_show' => false,
                'type'          => 'string',
                'value'         => 'eg',
            ],
            [
                'code'          => 'default_password',
                'name'          => '初始密码',
                'group'         => Config::GROUP_USER,
                'remarks'       => '下级开户、重置密码的默认密码',
                'is_front_show' => false,
                'type'          => 'string',
                'value'         => '123qwe',
            ],
            [
                'code'          => 'default_fund_password',
                'name'          => '初始资金密码',
                'group'         => Config::GROUP_USER,
                'remarks'       => '下级开户、重置密码的默认资金密码',
                'is_front_show' => false,
                'type'          => 'string',
                'value'         => '123qwe',
            ],
            [
                'code'          => 'default_payment_group_id',
                'name'          => '初始支付组别',
                'group'         => Config::GROUP_BANK,
                'remarks'       => '注册会员默认支付组别id',
                'is_front_show' => false,
                'type'          => 'integer',
                'value'         => 1,
            ],
            [
                'code'          => 'default_risk_group_id',
                'name'          => '初始风控等级id',
                'group'         => Config::GROUP_USER,
                'remarks'       => '注册会员默认风控组别id',
                'is_front_show' => false,
                'type'          => 'integer',
                'value'         => 1,
            ],
            [
                'code'          => 'default_vip_id',
                'name'          => '初始Vip id',
                'group'         => Config::GROUP_USER,
                'remarks'       => '注册会员默认Vip id',
                'is_front_show' => false,
                'type'          => 'integer',
                'value'         => 1,
            ],
            [
                'code'          => 'default_reward_id',
                'name'          => '初始积分等级id',
                'group'         => Config::GROUP_USER,
                'remarks'       => '注册会员默认积分等级id',
                'is_front_show' => false,
                'type'          => 'integer',
                'value'         => null,
            ],
            [
                'code'          => 'user_bank_account_limit',
                'name'          => '会员银行卡上限',
                'group'         => Config::GROUP_BANK,
                'remarks'       => '会员银行卡上限',
                'is_front_show' => false,
                'type'          => 'integer',
                'value'         => 5,
            ],
            [
                'code'          => 'user_mpay_number_limit',
                'name'          => '会员MPay上限',
                'group'         => Config::GROUP_BANK,
                'remarks'       => '会员MPay上限',
                'is_front_show' => false,
                'type'          => 'integer',
                'value'         => 2,
            ],
            [
                'code'          => 'game_platform_transfer_check_limit',
                'name'          => '第三方转账检查次数限制',
                'group'         => Config::GROUP_FUNCTION,
                'remarks'       => '第三方转账检查次数限制',
                'is_front_show' => false,
                'type'          => 'integer',
                'value'         => 3,
            ],
            [
                'code'          => 'affiliate_commission_limit',
                'name'          => '代理奖励预设盈亏限制',
                'group'         => Config::GROUP_AFFILIATE,
                'remarks'       => '代理奖励预设盈亏限制, JSON 格式',
                'is_front_show' => false,
                'type'          => 'integer',
                'value'         => '[{"tier":1,"title":"Comm %Tier 1","value":"40","profit":0},{"tier":2,"title":"Comm %Tier 2","value":"40","profit":0},{"tier":3,"title":"Comm %Tier 3","value":"40","profit":0}]',
            ],
            [
                'code'          => 'child_commission_percent',
                'name'          => '返上级代理佣金百分比',
                'group'         => Config::GROUP_AFFILIATE,
                'remarks'       => '返上级代理佣金百分比',
                'is_front_show' => false,
                'type'          => 'integer',
                'value'         => '10',
            ],
            [
                'code'          => 'product_fee_percent',
                'name'          => '产品手续费百分比',
                'group'         => Config::GROUP_AFFILIATE,
                'remarks'       => '产品手续费百分比',
                'is_front_show' => false,
                'type'          => 'integer',
                'value'         => '12',
            ],
            [
                'code'          => 'deposit_fee_percent',
                'name'          => '充值手续费百分比',
                'group'         => Config::GROUP_BANK,
                'remarks'       => '充值手续费百分比',
                'is_front_show' => false,
                'type'          => 'integer',
                'value'         => '1',
            ],
            [
                'code'          => 'withdrawal_fee_percent',
                'name'          => '提领手续费百分比',
                'group'         => Config::GROUP_BANK,
                'remarks'       => '提领手续费百分比',
                'is_front_show' => false,
                'type'          => 'integer',
                'value'         => '1',
            ],
            [
                'code'          => 'active_count_min_limit',
                'name'          => '最低活跃人数限制',
                'group'         => Config::GROUP_AFFILIATE,
                'remarks'       => '最低活跃人数限制，未达到的话不给奖金',
                'is_front_show' => false,
                'type'          => 'integer',
                'value'         => '5',
            ],
            [
                'code'          => 'commission_clear_limit',
                'name'          => '代理分红清零限制',
                'group'         => Config::GROUP_AFFILIATE,
                'remarks'       => '代理分红清零限制，低于则清0',
                'is_front_show' => false,
                'type'          => 'integer',
                'value'         => '-2000',
            ],
            [
                'code'          => 'max_login_fail_times',
                'name'          => '系统登录失败次数限制',
                'group'         => Config::GROUP_FUNCTION,
                'remarks'       => '系统登录失败次数限制',
                'is_front_show' => false,
                'type'          => 'integer',
                'value'         => '5',
            ],
            [
                'code'          => 'auto_assign_daily_retention',
                'name'          => 'auto assign daily retention',
                'group'         => Config::GROUP_FUNCTION,
                'remarks'       => '自动分配新用户到CRM系统',
                'is_front_show' => false,
                'type'          => 'boolean',
                'value'         => 1,
            ],
            [
                'code'          => 'auto_assign_no_deposit_users',
                'name'          => 'auto assign no deposit users',
                'group'         => Config::GROUP_FUNCTION,
                'remarks'       => '自动分配超过七天没有充值的用户到CRM',
                'is_front_show' => false,
                'type'          => 'boolean',
                'value'         => 1,
            ],
            [
                'code'          => 'auto_assign_7_days_retention',
                'name'          => 'auto assign 7 days retention',
                'group'         => Config::GROUP_FUNCTION,
                'remarks'       => '自动分配超过七天没有登陆的用户到CRM（有充值记录）',
                'is_front_show' => false,
                'type'          => 'boolean',
                'value'         => 1,
            ],
            [
                'code'          => 'auto_assign_30_days_retention',
                'name'          => 'auto assign 30 days retention',
                'group'         => Config::GROUP_FUNCTION,
                'remarks'       => '自动分配超过三十天没有登陆的用户到CRM（有充值记录）',
                'is_front_show' => false,
                'type'          => 'boolean',
                'value'         => 1,
            ],
        ];

        Config::insert($configs);
    }
}
