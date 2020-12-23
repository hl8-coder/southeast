<?php

use Illuminate\Database\Seeder;

class ActionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $class            = new \ReflectionClass(ActionsTableSeeder::class);
        $privateFunctions = $class->getMethods(ReflectionMethod::IS_PRIVATE);

        $menuCode     = \App\Models\Menu::where('has_action', true)->pluck('code')->toArray();
        $menuCodeFlip = array_flip($menuCode);
        foreach ($privateFunctions as $function) {
            $functionName = $function->name;
            if (!isset($menuCodeFlip[$functionName])) {
                // menu 没有的code
                dd($functionName);
            }
            if (!in_array($functionName, $menuCode)) {
                dd($functionName);
            }
            $this->$functionName();
        }

    }


    # sop
    private function sop_user_listing()
    {
    }

    private function sop_user_role_listing()
    {
    }


    # members
    # 会员列表(客服)
    private function members_member_listing_cs()
    {
        $oMenu = \App\Models\Menu::where('code', 'members_member_listing_cs')->first();

        $aActions = [
            // 列表(搜寻)
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user view',
                'action'        => 'users.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/users',
                'drop_list_url' => '/api/backstage/drop_list/user',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // 更改状态
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'change user status',
                'action'        => 'backstage.users.update_status',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/users/{user}/status',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // 重置密码
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'reset password',
                'action'        => 'backstage.users.reset_password',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/users/{user}/reset_password',
                'drop_list_url' => '',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // 更改会员第三方钱包状态
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'wallet change status',
                'action'        => 'backstage.game_platform_user.balance_status',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/game_platform_users/{game_platform_user}/balance_status',
                'drop_list_url' => '',
                'sort'          => 4,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];

        \App\Models\Action::insert($aActions);
    }

    private function members_member_listing_vip()
    {
    }

    # 会员优惠确认工具
    private function members_promotion_checking_tool()
    {
        $oMenu = \App\Models\Menu::where('code', 'members_promotion_checking_tool')->first();

        $aActions = [
            // 列表(搜寻)
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user bonus prize view',
                'action'        => 'backstage.user_bonus_prize.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/user_bonus_prizes/user_index',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/user_bonus_prizes/{user_bonus_prize} delete
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user bonus prize delete',
                'action'        => 'backstage.user_bonus_prize.delete',
                'method'        => 'DELETE',
                'url'           => '/api/backstage/user_bonus_prizes/{user_bonus_prize}',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];

        \App\Models\Action::insert($aActions);
    }

    private function members_bank_account_listing()
    {
        $oMenu = \App\Models\Menu::where('code', 'members_bank_account_listing')->first();

        $aActions = [
            // /api/backstage/user_bank_accounts get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user bank account view',
                'action'        => 'backstage.user_bank_account.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/user_bank_accounts',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/user_bank_accounts/{user_bank_account} delete
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user bank account delete',
                'action'        => 'backstage.user_bank_account.delete',
                'method'        => 'DELETE',
                'url'           => '/api/backstage/user_bank_accounts/{user_bank_account}',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/user_bank_accounts/{user_bank_account}/status  patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user bank account status',
                'action'        => 'backstage.user_bank_account.status',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/user_bank_accounts/{user_bank_account}/status',
                'drop_list_url' => '',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];

        \App\Models\Action::insert($aActions);
    }
    # 会员详情
    private function members_profile()
    {
        $oMenu = \App\Models\Menu::where('code', 'members_profile')->first();

        $aActions = [
            // 备注列表
            // /api/backstage/users/{user}/profile_remarks get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'profile remark view',
                'action'        => 'backstage.profile_remarks.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/users/{user}/profile_remarks',
                'drop_list_url' => '/api/backstage/drop_list/profileRemark',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // 添加备注
            // /api/backstage/users/{user}/profile_remarks post
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'profile remark add',
                'action'        => 'backstage.profile_remarks.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/users/{user}/profile_remarks',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // 会员详情
            // /api/backstage/users/{user} get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user detail',
                'action'        => 'users.show',
                'method'        => 'GET',
                'url'           => '/api/backstage/users/{user}',
                'drop_list_url' => '/api/backstage/drop_list/user',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // 更改状态
            // /api/backstage/users/{user}/status patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user change status',
                'action'        => 'backstage.users.update_status',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/users/{user}/status',
                'drop_list_url' => '',
                'sort'          => 4,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // 重置密码
            // /api/backstage/users/{user}/reset_password  patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user reset password',
                'action'        => 'backstage.users.reset_password',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/users/{user}/reset_password',
                'drop_list_url' => '',
                'sort'          => 5,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // 重置会员安全问题
            // /api/backstage/users/{user}/reset_security_question patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user reset security question',
                'action'        => 'backstage.users.reset_security_question',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/users/{user}/reset_security_question',
                'drop_list_url' => '',
                'sort'          => 5,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // 更新会员信息
            // /api/backstage/users/{user}  patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user update',
                'action'        => 'users.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/users/{user}',
                'drop_list_url' => '',
                'sort'          => 6,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // 会员修改信息记录列表
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user audit view',
                'action'        => 'backstage.users.audit',
                'method'        => 'GET',
                'url'           => '/api/backstage/users/{user}/audit',
                'drop_list_url' => '',
                'sort'          => 7,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // 修改会员风控组别
            // /api/backstage/users/{user}/risk_group patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user change risk group',
                'action'        => 'backstage.users.update_risk_group',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/users/{user}/risk_group',
                'drop_list_url' => '',
                'sort'          => 8,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // 修改会员支付组别
            // /api/backstage/users/{user}/payment_group patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user change payment group',
                'action'        => 'backstage.users.update_payment_group',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/users/{user}/payment_group',
                'drop_list_url' => '',
                'sort'          => 9,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // 修改会员积分组别
            // /api/backstage/users/{user}/reward patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user change reward',
                'action'        => 'backstage.users.update_reward',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/users/{user}/reward',
                'drop_list_url' => '',
                'sort'          => 9,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // 验证邮箱
            // /api/backstage/users/{user}/verify_email patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user verify email',
                'action'        => 'backstage.users.verify_email',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/users/{user}/verify_email',
                'drop_list_url' => '',
                'sort'          => 9,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/users/{user}/verify_phone patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user verify phone',
                'action'        => 'backstage.users.verify_phone',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/users/{user}/verify_phone',
                'drop_list_url' => '',
                'sort'          => 9,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];

        \App\Models\Action::insert($aActions);
    }


    # wallet_management
    private function wallet_management_member_transfer_management()
    {
        $oMenu = \App\Models\Menu::where('code', 'wallet_management_member_transfer_management')->first();

        $aActions = [
            // /api/backstage/game_platform_transfer_details get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'game platform transfer details',
                'action'        => 'backstage.game_platform_transfer_details.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/game_platform_transfer_details',
                'drop_list_url' => '/api/backstage/drop_list/game_platforms',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/game_platforms/transfer post
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'game_platforms transfer',
                'action'        => 'backstage.game_platforms.transfer',
                'method'        => 'POST',
                'url'           => '/api/backstage/game_platforms/transfer',
                'drop_list_url' => '/api/backstage/drop_list/game_platforms',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];

        \App\Models\Action::insert($aActions);
    }

    private function wallet_management_products_wallet_status()
    {

    }


    # crm_member_tsm
    // CRM Welcome列表
    private function crm_member_tsm_welcome_case_listing()
    {
        $oMenu = \App\Models\Menu::where('code', 'crm_member_tsm_welcome_case_listing')->first();

        $aActions = [
            // api/backstage/crm_orders/welcome get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'crm_orders welcome',
                'action'        => 'backstage.crm_orders.welcome',
                'method'        => 'GET',
                'url'           => '/api/backstage/crm_orders/welcome',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/admins get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'admin index',
                'action'        => 'backstage.admin.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/admins',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/crm_orders/excel_report get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'crm_orders excel_report',
                'action'        => 'backstage.crm_orders.excel_report',
                'method'        => 'GET',
                'url'           => '/api/backstage/crm_orders/excel_report',
                'drop_list_url' => '',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/crm_orders/update_bo post
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'crm_orders update_bo',
                'action'        => 'backstage.crm_orders.update_bo',
                'method'        => 'POST',
                'url'           => '/api/backstage/crm_orders/update_bo',
                'drop_list_url' => '',
                'sort'          => 4,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/crm_orders/delete_bo post
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'crm_orders delete_bo',
                'action'        => 'backstage.crm_orders.delete_bo',
                'method'        => 'POST',
                'url'           => '/api/backstage/crm_orders/delete_bo',
                'drop_list_url' => '',
                'sort'          => 5,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/crm_orders/{crm_order}/welcome patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'crm_orders welcome update',
                'action'        => 'backstage.crm_orders.welcome.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/crm_orders/{crm_order}/welcome',
                'drop_list_url' => '',
                'sort'          => 6,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/users/{user}/status patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'crm_orders users status update',
                'action'        => 'backstage.users.status.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/users/{user}/status',
                'drop_list_url' => '',
                'sort'          => 7,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];

        \App\Models\Action::insert($aActions);
    }

    private function crm_member_tsm_non_deposit_retention_listing()
    {
        $oMenu = \App\Models\Menu::where('code', 'crm_member_tsm_non_deposit_retention_listing')->first();

        $aActions = [
            // api/backstage/crm_orders/welcome get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'admins index',
                'action'        => 'backstage.admins.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/admins',
                'drop_list_url' => '/api/backstage/drop_list/crm_order',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/crm_orders/retention get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'crm_orders retention index',
                'action'        => 'backstage.crm_orders.retention.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/crm_orders/retention',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/crm_orders/excel_report get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'crm_orders retention index',
                'action'        => 'backstage.crm_orders.retention.excel_report',
                'method'        => 'GET',
                'url'           => '/api/backstage/crm_orders/excel_report',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/crm_orders/excel_report get
            // [
            //     'menu_id'       => $oMenu->id,
            //     'name'          => 'crm_orders retention index',
            //     'action'        => 'backstage.crm_orders.retention.index',
            //     'method'        => 'GET',
            //     'url'           => '/api/backstage/crm_orders/retention ',
            //     'drop_list_url' => '',
            //     'sort'          => 2,
            //     'created_at'    => now(),
            //     'updated_at'    => now(),
            // ],
        ];
        \App\Models\Action::insert($aActions);
    }

    private function crm_member_tsm_tagging()
    {
        $oMenu = \App\Models\Menu::where('code', 'crm_member_tsm_tagging')->first();

        $aActions = [
            // /api/backstage/crm_bo_admins/index get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'crm_orders admins index',
                'action'        => 'backstage.crm_bo_admins.admins.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/crm_bo_admins',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/admins get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'admins index',
                'action'        => 'backstage.admins',
                'method'        => 'GET',
                'url'           => '/api/backstage/admins',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/crm_bo_admins/store post
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'crm_bo_admins store',
                'action'        => 'backstage.crm_bo_admins.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/crm_bo_admins',
                'drop_list_url' => '',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/crm_orders/auto_add post
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'crm_orders auto_add',
                'action'        => 'backstage.crm_orders.auto_add',
                'method'        => 'POST',
                'url'           => '/api/backstage/crm_orders/auto_add',
                'drop_list_url' => '',
                'sort'          => 4,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }


    # bank_management
    private function bank_management_bank_access()
    {
    }

    private function bank_management_bank_management()
    {
        $oMenu = \App\Models\Menu::where('code', 'bank_management_bank_management')->first();

        $aActions = [
            // /api/backstage/banks get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'banks index',
                'action'        => 'backstage.banks.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/banks',
                'drop_list_url' => '/api/backstage/drop_list/bank',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/banks POST
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'banks store',
                'action'        => 'backstage.banks.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/banks',
                'drop_list_url' => '/api/backstage/drop_list/bank',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/banks/{bank} patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'banks update',
                'action'        => 'backstage.banks.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/banks/{bank}',
                'drop_list_url' => '/api/backstage/drop_list/bank',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    private function bank_management_account_group_management()
    {
        $oMenu    = \App\Models\Menu::where('code', 'bank_management_account_group_management')->first();
        $aActions = [
            // /api/backstage/payment_groups get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'payment_groups index',
                'action'        => 'backstage.payment_groups.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/payment_groups',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/payment_groups post
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'payment_groups store',
                'action'        => 'backstage.payment_groups.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/payment_groups',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/payment_groups/{payment_group} patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'payment update',
                'action'        => 'backstage.payment_groups.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/payment_groups/{payment_group}',
                'drop_list_url' => '',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    private function bank_management_bank_account_management()
    {
        $oMenu    = \App\Models\Menu::where('code', 'bank_management_bank_account_management')->first();
        $aActions = [
            // /api/backstage/company_bank_accounts get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'company_bank_accounts index',
                'action'        => 'backstage.company_bank_accounts.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/company_bank_accounts',
                'drop_list_url' => '/api/backstage/drop_list/company_bank_account',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/drop_list/company_bank_account

            // /api/backstage/company_bank_accounts/{company_bank_account} get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'company_bank_accounts detail',
                'action'        => 'backstage.company_bank_accounts.detail',
                'method'        => 'GET',
                'url'           => '/api/backstage/company_bank_accounts/{company_bank_account}',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/company_bank_accounts/adjust patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'company_bank_accounts adjust update',
                'action'        => 'backstage.company_bank_accounts.adjust.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/company_bank_accounts/adjust',
                'drop_list_url' => '',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/company_bank_accounts/internal_transfer patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'company_bank_accounts internal_transfer update',
                'action'        => 'backstage.company_bank_accounts.internal_transfer.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/company_bank_accounts/internal_transfer',
                'drop_list_url' => '',
                'sort'          => 4,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/company_bank_accounts/buffer_transfer patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'company_bank_accounts buffer_transfer update',
                'action'        => 'backstage.company_bank_accounts.buffer_transfer.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/company_bank_accounts/buffer_transfer',
                'drop_list_url' => '',
                'sort'          => 5,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/company_bank_accounts post
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'company_bank_accounts store',
                'action'        => 'backstage.company_bank_accounts.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/company_bank_accounts',
                'drop_list_url' => '',
                'sort'          => 6,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # Bank Management > bank_management_bank_account_management_detail_and_history 前端需求
    private function bank_management_bank_account_management_detail_and_history()
    {
        $oMenu    = \App\Models\Menu::where('code', 'bank_management_bank_account_management_detail_and_history')->first();
        $aActions = [
            // /api/backstage/company_bank_account_transactions get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'company_bank_account_transactions index',
                'action'        => 'backstage.company_bank_account_transactions.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/company_bank_account_transactions',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/company_bank_accounts/{company_bank_account} get patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'company_bank_accounts detail',
                'action'        => 'backstage.company_bank_accounts.detail',
                'method'        => 'GET',
                'url'           => '/api/backstage/company_bank_accounts/{company_bank_account}',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'company_bank_accounts update',
                'action'        => 'backstage.company_bank_accounts.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/company_bank_accounts/{company_bank_account}',
                'drop_list_url' => '',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/company_bank_accounts/{company_bank_account}/remarks get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'company_bank_accounts remarks index',
                'action'        => 'backstage.company_bank_accounts.remarks.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/company_bank_accounts/{company_bank_account}/remarks',
                'drop_list_url' => '',
                'sort'          => 4,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    private function bank_management_bank_reconciliation_report()
    {
        $oMenu    = \App\Models\Menu::where('code', 'bank_management_bank_reconciliation_report')->first();
        $aActions = [
            // /api/backstage/company_bank_accounts/reports get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'company_bank_accounts reports',
                'action'        => 'backstage.company_bank_accounts.reports',
                'method'        => 'GET',
                'url'           => '/api/backstage/company_bank_accounts/reports',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    private function bank_management_payment_platform_management()
    {
        $oMenu    = \App\Models\Menu::where('code', 'bank_management_payment_platform_management')->first();
        $aActions = [
            // /api/backstage/payment_platforms get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'payment_platforms index',
                'action'        => 'backstage.payment_platforms.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/payment_platforms',
                'drop_list_url' => '/api/backstage/drop_list/payment_platform',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/payment_platforms post
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'payment_platforms store',
                'action'        => 'backstage.payment_platforms.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/payment_platforms',
                'drop_list_url' => '/api/backstage/drop_list/payment_platform',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/payment_platforms/{payment_platform} patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'payment_platforms update',
                'action'        => 'backstage.payment_platforms.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/payment_platforms/{payment_platform}',
                'drop_list_url' => '/api/backstage/drop_list/payment_platform',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }


    # payment
    # payment > Transaction Search
    private function payment_transaction_search()
    {
        $oMenu    = \App\Models\Menu::where('code', 'payment_transaction_search')->first();
        $aActions = [
            // /api/backstage/user_account_transactions get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user_account_transactions index',
                'action'        => 'backstage.user_account_transactions.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/user_account_transactions',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # payment > Fund Transfer > Member Listing
    private function payment_fund_transfers_member_listing()
    {
        $oMenu    = \App\Models\Menu::where('code', 'payment_fund_transfers_member_listing')->first();
        $aActions = [
            // /api/backstage/drop_list/adjustment get
            // /api/backstage/adjustments get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'adjustments index',
                'action'        => 'backstage.adjustments.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/adjustments',
                'drop_list_url' => '/api/backstage/drop_list/adjustment',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/users/{user_name}/adjustments post
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'adjustments store',
                'action'        => 'backstage.adjustments.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/users/{user_name}/adjustments',
                'drop_list_url' => '/api/backstage/drop_list/adjustment',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/adjustments/{adjustment} delete
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'adjustments delete',
                'action'        => 'backstage.adjustments.delete',
                'method'        => 'DELETE',
                'url'           => '/api/backstage/adjustments/{adjustment}',
                'drop_list_url' => '',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/adjustments/{adjustment}/approve patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'adjustments update',
                'action'        => 'backstage.adjustments.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/adjustments/{adjustment}/approve',
                'drop_list_url' => '',
                'sort'          => 4,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # 前端要求：start
    #payment > Fund Transfer > payment_fund_transfers_account_summary
    private function payment_fund_transfers_account_summary()
    {
        $oMenu    = \App\Models\Menu::where('code', 'payment_fund_transfers_account_summary')->first();
        $aActions = [
            // /api/backstage/user_platform_total_reports get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user_platform_total_reports index',
                'action'        => 'backstage.user_platform_total_reports.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/user_platform_total_reports',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/user_main_wallet_total_report get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user_main_wallet_total_report index',
                'action'        => 'backstage.user_main_wallet_total_report.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/user_main_wallet_total_report',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/user_product_total_reports get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user_product_total_reports index',
                'action'        => 'backstage.user_product_total_reports.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/user_product_total_reports',
                'drop_list_url' => '',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    #payment > Fund Transfer > payment_fund_transfers_member_profile
    private function payment_fund_transfers_member_profile()
    {
        $oMenu    = \App\Models\Menu::where('code', 'payment_fund_transfers_member_profile')->first();
        $aActions = [
            // /api/backstage/user/get_user_by_name get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'get_user_by_name index',
                'action'        => 'backstage.get_user_by_name.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/user/get_user_by_name',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);

    }

    #payment > Fund Transfer > payment_fund_transfers_remarks
    private function payment_fund_transfers_remarks()
    {
        $oMenu    = \App\Models\Menu::where('code', 'payment_fund_transfers_remarks')->first();
        $aActions = [
            // /api/backstage/remarks get post
            // /api/backstage/drop_list/remark get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'remarks index',
                'action'        => 'backstage.remarks.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/remarks',
                'drop_list_url' => '/api/backstage/drop_list/remark',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'remarks store',
                'action'        => 'backstage.remarks.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/remarks',
                'drop_list_url' => '/api/backstage/drop_list/remark',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    #payment > Fund Transfer > payment_fund_transfers_deposit_history
    private function payment_fund_transfers_deposit_history()
    {
        $oMenu    = \App\Models\Menu::where('code', 'payment_fund_transfers_deposit_history')->first();
        $aActions = [
            // /api/backstage/deposits get
            // /api/backstage/drop_list/deposit get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'deposit index',
                'action'        => 'backstage.deposit.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/deposits',
                'drop_list_url' => '/api/backstage/drop_list/deposit',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    #payment > Fund Transfer > payment_fund_transfers_withdrawal_history
    private function payment_fund_transfers_withdrawal_history()
    {
        $oMenu    = \App\Models\Menu::where('code', 'payment_fund_transfers_withdrawal_history')->first();
        $aActions = [
            // /api/backstage/withdrawals get
            // /api/backstage/drop_list/withdrawal get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'withdrawals index',
                'action'        => 'backstage.withdrawals.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/withdrawals',
                'drop_list_url' => '/api/backstage/drop_list/withdrawal',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    #payment > Fund Transfer > payment_fund_transfers_total_bet
    private function payment_fund_transfers_total_bet()
    {
        $oMenu    = \App\Models\Menu::where('code', 'payment_fund_transfers_total_bet')->first();
        $aActions = [
            // /api/backstage/user_product_reports get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user_product_reports index',
                'action'        => 'backstage.user_product_reports.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/user_product_reports',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    #payment > Fund Transfer > payment_fund_transfers_affiliates_top_up
    private function payment_fund_transfers_affiliates_top_up()
    {
    }
    # 前端要求：end

    #payment > Fund Transfer > Cash Flow
    private function payment_fund_transfers_cash_flow()
    {
    }

    #payment > Deposit > Open Deposit
    private function payment_deposit_open_deposits()
    {
        $oMenu    = \App\Models\Menu::where('code', 'payment_deposit_open_deposits')->first();
        $aActions = [
            // /api/backstage/drop_list/deposit get
            // /api/backstage/deposits/open_deposit get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'deposits open_deposit index',
                'action'        => 'backstage.deposits.open_deposit.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/deposits/open_deposit',
                'drop_list_url' => '/api/backstage/drop_list/deposit',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    #payment > Deposit > Fast Deposit
    private function payment_deposit_fast_deposit()
    {
        $oMenu    = \App\Models\Menu::where('code', 'payment_deposit_fast_deposit')->first();
        $aActions = [
            // /api/backstage/drop_list/deposit get
            // /api/backstage/deposits/fast_deposit get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'deposits fast_deposit index',
                'action'        => 'backstage.deposits.fast_deposit.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/deposits/fast_deposit',
                'drop_list_url' => '/api/backstage/drop_list/deposit',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    #payment > Deposit > Gateway
    private function payment_deposit_gateway()
    {
        $oMenu    = \App\Models\Menu::where('code', 'payment_deposit_gateway')->first();
        $aActions = [
            // /api/backstage/drop_list/deposit get
            // /api/backstage/deposits/gateway get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'deposits gateway index',
                'action'        => 'backstage.deposits.gateway.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/deposits/gateway',
                'drop_list_url' => '/api/backstage/drop_list/deposit',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    #payment > Deposit > Txn INQ-VBS
    private function payment_deposit_txn_inq_vbs()
    {
        $oMenu    = \App\Models\Menu::where('code', 'payment_deposit_txn_inq_vbs')->first();
        $aActions = [
            // /api/backstage/drop_list/bank_transaction get
            // /api/backstage/bank_transactions get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'bank_transactions index',
                'action'        => 'backstage.bank_transactions.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/bank_transactions',
                'drop_list_url' => '/api/backstage/drop_list/bank_transaction',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/bank_transactions/{bank_transaction}/audit get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'bank_transactions audit index',
                'action'        => 'backstage.bank_transactions.audit.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/bank_transactions/{bank_transaction}/audit',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/bank_transactions/{bank_transaction}/credit patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'bank_transactions update',
                'action'        => 'backstage.bank_transactions.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/bank_transactions/{bank_transaction}/credit',
                'drop_list_url' => '',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/bank_transactions/{bank_transaction} delete
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'bank_transactions delete',
                'action'        => 'backstage.bank_transactions.delete',
                'method'        => 'DELETE',
                'url'           => '/api/backstage/bank_transactions/{bank_transaction}',
                'drop_list_url' => '',
                'sort'          => 4,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    #payment > Deposit > Advance credit
    private function payment_deposit_advance_credit()
    {
        $oMenu    = \App\Models\Menu::where('code', 'payment_deposit_advance_credit')->first();
        $aActions = [
            // /api/backstage/drop_list/deposit get
            // /api/backstage/deposits/advance_credit get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'deposits advance_credit index',
                'action'        => 'backstage.deposits.advance_credit.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/deposits/advance_credit',
                'drop_list_url' => '/api/backstage/drop_list/deposit',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/deposits/{deposit}/lose patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'deposit update',
                'action'        => 'backstage.deposits.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/deposits/{deposit}/lose',
                'drop_list_url' => '/api/backstage/drop_list/deposit',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    #payment > Withdrawal > Open Withdrawal
    private function payment_withdraw_open_withdrawal()
    {
        $oMenu    = \App\Models\Menu::where('code', 'payment_withdraw_open_withdrawal')->first();
        $aActions = [
            // /api/backstage/withdrawals/open get
            // /api/backstage/drop_list/withdrawal get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'withdrawals open index',
                'action'        => 'backstage.withdrawals.open.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/withdrawals/open',
                'drop_list_url' => '/api/backstage/drop_list/withdrawal',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # payment > Withdrawal > Bank Transfer
    private function payment_withdraw_bank_transfer()
    {
        $oMenu    = \App\Models\Menu::where('code', 'payment_withdraw_bank_transfer')->first();
        $aActions = [];
        // \App\Models\Action::insert($aActions);
    }

    # payment > Withdrawal > Process
    private function payment_withdraw_process()
    {
        $oMenu    = \App\Models\Menu::where('code', 'payment_withdraw_process')->first();
        $aActions = [];
        // \App\Models\Action::insert($aActions);
    }

    # payment > Other > Rebate Settlement
    private function payment_other_rebate()
    {
        $oMenu    = \App\Models\Menu::where('code', 'payment_other_rebate')->first();
        $aActions = [
            // /api/backstage/drop_list/user_rebate_prize get
            // /api/backstage/payment/user_rebate_prizes get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'payment user_rebate_prizes index',
                'action'        => 'backstage.payment.user_rebate_prizes.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/payment/user_rebate_prizes',
                'drop_list_url' => '/api/backstage/drop_list/user_rebate_prize',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/user_rebate_prizes/payment_send patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'payment user_rebate_prizes payment_send update',
                'action'        => 'backstage.payment.user_rebate_prizes.payment_send.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/user_rebate_prizes/payment_send',
                'drop_list_url' => '/api/backstage/drop_list/user_rebate_prize',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }


    # balance adjustment
    private function balance_adjustment_balance_batch_adjustment()
    {
    }

    private function balance_adjustment_batch_remark_upload()
    {
    }

    # RM tool
    # rm tool > ip monitor > member
    private function compliance_tools_ip_monitor_activity_member()
    {
        $oMenu    = \App\Models\Menu::where('code', 'compliance_tools_ip_monitor_activity_member')->first();
        $aActions = [
            // /api/backstage/user_login_logs get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user_login_logs index',
                'action'        => 'backstage.user_login_logs.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/user_login_logs',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/user_login_logs/by_ip get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user_login_logs by_ip index',
                'action'        => 'backstage.user_login_logs.by_ip.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/user_login_logs/by_ip',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # rm tool > ip monitor > Affiliate
    private function compliance_tools_ip_monitor_activity_affiliate()
    {
        $oMenu    = \App\Models\Menu::where('code', 'compliance_tools_ip_monitor_activity_affiliate')->first();
        $aActions = [
            // /api/backstage/affiliate_login_logs get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'affiliate_login_logs index',
                'action'        => 'backstage.affiliate_login_logs.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/affiliate_login_logs',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # rm tool > Member Enquiry > Player Winner
    private function compliance_tools_member_player_winner()
    {
        $oMenu    = \App\Models\Menu::where('code', 'compliance_tools_member_player_winner')->first();
        $aActions = [
            // /api/backstage/drop_list/user_risk_summary get
            // /api/backstage/rmtools/user_product_report get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'rmtools user_product_report index',
                'action'        => 'backstage.rmtools.user_product_report.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/rmtools/user_product_report',
                'drop_list_url' => '/api/backstage/drop_list/user_risk_summary',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/rmtools/user_product_report/export get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'rmtools user_product_report export',
                'action'        => 'backstage.rmtools.user_product_report.export',
                'method'        => 'GET',
                'url'           => '/api/backstage/rmtools/user_product_report/export',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # rm tool > Member Enquiry > Player Winner Details
    private function compliance_tools_member_player_winner_details()
    {
        $oMenu    = \App\Models\Menu::where('code', 'compliance_tools_member_player_winner_details')->first();
        $aActions = [
            // /api/backstage/drop_list/user_risk_summary get
            // /api/backstage/rmtools/user_product_report_detail get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'rmtools user_product_report_detail index',
                'action'        => 'backstage.rmtools.user_product_report_detail.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/rmtools/user_product_report_detail',
                'drop_list_url' => '/api/backstage/drop_list/user_risk_summary',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # rm tool > Member Enquiry > Player Winner Details Per Date
    private function compliance_tools_member_player_winner_details_per_date()
    {
        $oMenu    = \App\Models\Menu::where('code', 'compliance_tools_member_player_winner_details_per_date')->first();
        $aActions = [
            // /api/backstage/drop_list/user_risk_summary get
            // /api/backstage/rmtools/user_product_report_detail_daily get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'rmtools user_product_report_detail_daily index',
                'action'        => 'backstage.rmtools.user_product_report_detail_daily.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/rmtools/user_product_report_detail_daily',
                'drop_list_url' => '/api/backstage/drop_list/user_risk_summary',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # rm tool > Member Enquiry > Members tag Remark
    private function compliance_tools_member_member_tag_remark()
    {
        $oMenu    = \App\Models\Menu::where('code', 'compliance_tools_member_member_tag_remark')->first();
        $aActions = [
            // /api/backstage/drop_list/user_risk_summary get
            // /api/backstage/rmtools/user_risk_summary get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'rmtools user_risk_summary index',
                'action'        => 'backstage.rmtools.user_risk_summary.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/rmtools/user_risk_summary',
                'drop_list_url' => '/api/backstage/drop_list/user_risk_summary',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # rm tool > Member Enquiry > Members Risk
    private function compliance_tools_member_risk_category_listing()
    {
        $oMenu    = \App\Models\Menu::where('code', 'compliance_tools_member_risk_category_listing')->first();
        $aActions = [
            // /api/backstage/drop_list/risk_category_listing get
            // /api/backstage/risk_category_listing get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'rmtools risk_category_listing index',
                'action'        => 'backstage.rmtools.risk_category_listing.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/risk_category_listing',
                'drop_list_url' => '/api/backstage/drop_list/risk_category_listing',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/risk_category_listing/{user} get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'rmtools risk_category_listing detail',
                'action'        => 'backstage.rmtools.risk_category_listing.detail',
                'method'        => 'GET',
                'url'           => '/api/backstage/risk_category_listing/{user}',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/risk_category_listing post
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'rmtools risk_category_listing store',
                'action'        => 'backstage.rmtools.risk_category_listing.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/risk_category_listing',
                'drop_list_url' => '',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # rm tool > Member Enquiry > Members Profile
    private function compliance_tools_member_member_data_query()
    {

        $oMenu    = \App\Models\Menu::where('code', 'compliance_tools_member_member_data_query')->first();
        $aActions = [
            // /api/backstage/member_data_query get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'rmtools member_data_query index',
                'action'        => 'backstage.rmtools.member_data_query.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/member_data_query',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/member_data_query/account_summary get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'rmtools account_summary index',
                'action'        => 'backstage.rmtools.account_summary.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/member_data_query/account_summary',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/member_data_query/account_summary_by_month get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'rmtools account_summary_by_month index',
                'action'        => 'backstage.rmtools.account_summary_by_month.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/member_data_query/account_summary_by_month',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }



    # communication tool
    # communication tool > Announcement
    private function communication_tools_announcement()
    {
        $oMenu    = \App\Models\Menu::where('code', 'communication_tools_announcement')->first();
        $aActions = [
            // /api/backstage/announcements get post
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'announcements index',
                'action'        => 'backstage.announcements.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/announcements',
                'drop_list_url' => '/api/backstage/drop_list/announcement',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/announcements get post
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'announcements store',
                'action'        => 'backstage.announcements.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/announcements',
                'drop_list_url' => '/api/backstage/drop_list/announcement',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/announcements/{announcement} patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'announcements update',
                'action'        => 'backstage.announcements.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/announcements/{announcement}',
                'drop_list_url' => '/api/backstage/drop_list/announcement',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # communication tool > Private Message
    private function communication_tools_private_message()
    {
        $oMenu    = \App\Models\Menu::where('code', 'communication_tools_private_message')->first();
        $aActions = [
            // /api/backstage/drop_list/notification get
            // /api/backstage/notifications get post
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'notifications index',
                'action'        => 'backstage.notifications.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/notifications',
                'drop_list_url' => '/api/backstage/drop_list/notification',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'notifications store',
                'action'        => 'backstage.notifications.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/notifications',
                'drop_list_url' => '/api/backstage/drop_list/notification',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/user_messages/excel/download get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user_messages excel download',
                'action'        => 'backstage.user_messages.excel.download',
                'method'        => 'GET',
                'url'           => '/api/backstage/user_messages/excel/download',
                'drop_list_url' => '',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/notifications/{notificationMessage} get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'notifications one',
                'action'        => 'backstage.notifications.one',
                'method'        => 'GET',
                'url'           => '/api/backstage/notifications/{notificationMessage}',
                'drop_list_url' => '',
                'sort'          => 4,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/notifications/{notification}/detail get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'notifications detail',
                'action'        => 'backstage.notifications.detail',
                'method'        => 'GET',
                'url'           => '/api/backstage/notifications/{notification}/detail',
                'drop_list_url' => '',
                'sort'          => 5,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # communication tool > sms
    private function communication_tools_sms()
    {
        $oMenu    = \App\Models\Menu::where('code', 'communication_tools_sms')->first();
        $aActions = [
            // /api/backstage/drop_list/user_messag get
            // /api/backstage/user_messages get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user_messages index',
                'action'        => 'backstage.user_messages.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/user_messages',
                'drop_list_url' => '/api/backstage/drop_list/user_message',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/user_messages post
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user_messages store',
                'action'        => 'backstage.user_messages.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/user_messages',
                'drop_list_url' => '/api/backstage/drop_list/user_message',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/user_messages/{userMessage} GET
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user_messages detail',
                'action'        => 'backstage.user_messages.detail',
                'method'        => 'GET',
                'url'           => '/api/backstage/user_messages/{userMessage}',
                'drop_list_url' => '/api/backstage/drop_list/user_message',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # communication tool > Mailbox Template
    private function communication_tools_mailbox_template()
    {
        $oMenu    = \App\Models\Menu::where('code', 'communication_tools_mailbox_template')->first();
        $aActions = [
            // /api/backstage/mailbox_templates get post
            // /api/backstage/drop_list/mailbox_template get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'mailbox_templates index',
                'action'        => 'backstage.mailbox_templates.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/mailbox_templates',
                'drop_list_url' => '/api/backstage/drop_list/mailbox_template',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'mailbox_templates store',
                'action'        => 'backstage.mailbox_templates.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/mailbox_templates',
                'drop_list_url' => '/api/backstage/drop_list/mailbox_template',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],

            // /api/backstage/mailbox_templates/{mailbox_template} patch delete
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'mailbox_templates update',
                'action'        => 'backstage.mailbox_templates.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/mailbox_templates/{mailbox_template}',
                'drop_list_url' => '',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'mailbox_templates delete',
                'action'        => 'backstage.mailbox_templates.delete',
                'method'        => 'DELETE',
                'url'           => '/api/backstage/mailbox_templates/{mailbox_template}',
                'drop_list_url' => '',
                'sort'          => 4,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # communication tool > Contact US
    private function communication_tools_contact_us()
    {
        $oMenu    = \App\Models\Menu::where('code', 'communication_tools_contact_us')->first();
        $aActions = [
            // /api/backstage/contact_information get post
            // /api/backstage/drop_list/contact_us get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'contact_information index',
                'action'        => 'backstage.contact_information.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/contact_information',
                'drop_list_url' => '/api/backstage/drop_list/contact_us',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'contact_information store',
                'action'        => 'backstage.contact_information.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/contact_information',
                'drop_list_url' => '/api/backstage/drop_list/contact_us',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/contact_information/{information} patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'contact_information update',
                'action'        => 'backstage.contact_information.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/contact_information/{information}',
                'drop_list_url' => '',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }


    # marketing tool
    # marketing tool > Banner
    private function marketing_tools_banner()
    {
        $oMenu    = \App\Models\Menu::where('code', 'marketing_tools_banner')->first();
        $aActions = [
            // /api/backstage/banners get post
            // /api/backstage/drop_list/banner get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'banners index',
                'action'        => 'backstage.banners.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/banners',
                'drop_list_url' => '/api/backstage/drop_list/banner',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'banners store',
                'action'        => 'backstage.banners.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/banners',
                'drop_list_url' => '/api/backstage/drop_list/banner',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'banners update',
                'action'        => 'backstage.banners.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/banners/{banner}',
                'drop_list_url' => '/api/backstage/drop_list/banner',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/images post
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'image store',
                'action'        => 'backstage.image.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/images',
                'drop_list_url' => '',
                'sort'          => 4,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # marketing tool > Promotion > Promotion Type Setting
    private function marketing_tools_promotion_promotion_type_setting()
    {
        $oMenu    = \App\Models\Menu::where('code', 'marketing_tools_promotion_promotion_type_setting')->first();
        $aActions = [
            // /api/backstage/promotion_types get post
            // /api/backstage/promotion_types/{promotion_type} patch
            // /api/backstage/drop_list/promotion_type get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'promotion_types index',
                'action'        => 'backstage.promotion_types.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/promotion_types',
                'drop_list_url' => '/api/backstage/drop_list/promotion_type',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'promotion_types store',
                'action'        => 'backstage.promotion_types.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/promotion_types',
                'drop_list_url' => '/api/backstage/drop_list/promotion_type',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'promotion_types update',
                'action'        => 'backstage.promotion_types.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/promotion_types/{promotion_type}',
                'drop_list_url' => '/api/backstage/drop_list/promotion_type',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/images post
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'image store',
                'action'        => 'backstage.image.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/images',
                'drop_list_url' => '',
                'sort'          => 4,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # marketing tool > Promotion > Promotion Setting
    private function marketing_tools_promotion_promotion_setting()
    {
        $oMenu    = \App\Models\Menu::where('code', 'marketing_tools_promotion_promotion_setting')->first();
        $aActions = [
            // /api/backstage/promotions get post
            // /api/backstage/promotions/{promotion} patch
            // /api/backstage/drop_list/promotion get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'promotions index',
                'action'        => 'backstage.promotions.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/promotions',
                'drop_list_url' => '/api/backstage/drop_list/promotion',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'promotions store',
                'action'        => 'backstage.promotions.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/promotions',
                'drop_list_url' => '/api/backstage/drop_list/promotion',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'promotions update',
                'action'        => 'backstage.promotions.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/promotions/{promotion}',
                'drop_list_url' => '/api/backstage/drop_list/promotion',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/images post
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'image store',
                'action'        => 'backstage.image.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/images',
                'drop_list_url' => '',
                'sort'          => 4,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }


    private function marketing_tools_refer_friend_currency_setting()
    {
    }

    private function marketing_tools_refer_friend_refer_friend_listings()
    {
    }

    private function marketing_tools_refer_friend_refer_friend_reports()
    {
    }

    # marketing tool > Bonus Code > Bonus Group Setting
    private function marketing_tools_bonus_code_bonus_group_setting()
    {
        $oMenu    = \App\Models\Menu::where('code', 'marketing_tools_bonus_code_bonus_group_setting')->first();
        $aActions = [
            // /api/backstage/bonus_groups get post
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'bonus_groups index',
                'action'        => 'backstage.bonus_groups.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/bonus_groups',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'bonus_groups store',
                'action'        => 'backstage.bonus_groups.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/bonus_groups',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # marketing tool > Bonus Code > Bonus Code Setting
    private function marketing_tools_bonus_code_bonus_code_setting()
    {
        $oMenu    = \App\Models\Menu::where('code', 'marketing_tools_bonus_code_bonus_code_setting')->first();
        $aActions = [
            // /api/backstage/bonuses get post
            // /api/backstage/bonuses/{bonus} patch
            // /api/backstage/drop_list/bonus get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'bonuses index',
                'action'        => 'backstage.bonuses.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/bonuses',
                'drop_list_url' => '/api/backstage/drop_list/bonus',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'bonuses store',
                'action'        => 'backstage.bonuses.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/bonuses',
                'drop_list_url' => '/api/backstage/drop_list/bonus',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'bonuses index',
                'action'        => 'backstage.bonuses.bonus.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/bonuses/{bonus}',
                'drop_list_url' => '/api/backstage/drop_list/bonus',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'bonuses store',
                'action'        => 'backstage.bonuses.bonus.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/bonuses/{bonus}',
                'drop_list_url' => '/api/backstage/drop_list/bonus',
                'sort'          => 4,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/bonuses/excel/download get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'bonuses excel download',
                'action'        => 'backstage.bonuses.excel.download',
                'method'        => 'GET',
                'url'           => '/api/backstage/bonuses/excel/download',
                'drop_list_url' => '',
                'sort'          => 4,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # marketing tool > Auto Rebate > Auto Rebate Setting By Products
    private function marketing_tools_auto_rebate_auto_rebate_setting_by_Products()
    {
        $oMenu    = \App\Models\Menu::where('code', 'marketing_tools_auto_rebate_auto_rebate_setting_by_Products')->first();
        $aActions = [
            // /api/backstage/drop_list/rebate get
            // /api/backstage/rebates get post
            // /api/backstage/rebates/{rebate} patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'rebates index',
                'action'        => 'backstage.rebates.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/rebates',
                'drop_list_url' => '/api/backstage/drop_list/rebate',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'rebates store',
                'action'        => 'backstage.rebates.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/rebates',
                'drop_list_url' => '/api/backstage/drop_list/rebate',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'rebates update',
                'action'        => 'backstage.rebates.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/rebates/{rebate}',
                'drop_list_url' => '/api/backstage/drop_list/rebate',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }


    # game management
    # game management > Game Platforms
    private function game_platforms()
    {
        $oMenu    = \App\Models\Menu::where('code', 'game_platforms')->first();
        $aActions = [
            // /api/backstage/game_platforms get
            // /api/backstage/drop_list/game_platforms get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'game_platforms index',
                'action'        => 'backstage.game_platforms.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/game_platforms',
                'drop_list_url' => '/api/backstage/drop_list/game_platforms',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # game management > Game Products
    private function game_products()
    {
        $oMenu    = \App\Models\Menu::where('code', 'game_products')->first();
        $aActions = [
            // /api/backstage/game_platform_products get
            // /api/backstage/game_platform_products/{game_platform_product} patch
            // /api/backstage/drop_list/game_platform_product get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'game_platform_products index',
                'action'        => 'backstage.game_platform_products.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/game_platform_products',
                'drop_list_url' => '/api/backstage/drop_list/game_platform_product',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'game_platform_products update',
                'action'        => 'backstage.game_platform_products.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/game_platform_products/{game_platform_product}',
                'drop_list_url' => '/api/backstage/drop_list/game_platform_product',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # game management > Games
    private function games()
    {
        $oMenu    = \App\Models\Menu::where('code', 'games')->first();
        $aActions = [
            // /api/backstage/games get post
            // /api/backstage/games/{game} patch delete
            // /api/backstage/drop_list/game
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'games index',
                'action'        => 'backstage.games.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/games',
                'drop_list_url' => '/api/backstage/drop_list/game',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'games store',
                'action'        => 'backstage.games.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/games',
                'drop_list_url' => '/api/backstage/drop_list/game',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'games update',
                'action'        => 'backstage.games.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/games/{game}',
                'drop_list_url' => '/api/backstage/drop_list/game',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'games delete',
                'action'        => 'backstage.games.delete',
                'method'        => 'DELETE',
                'url'           => '/api/backstage/games/{game}',
                'drop_list_url' => '/api/backstage/drop_list/game',
                'sort'          => 4,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }


    # reports
    # reports > Member > Case history
    private function reports_member_case_history()
    {
        $oMenu    = \App\Models\Menu::where('code', 'reports_member_case_history')->first();
        $aActions = [
            [
                'menu_id'       => $oMenu->id,
                'name'          => '',
                'action'        => 'backstage..index',
                'method'        => 'GET',
                'url'           => '',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        // \App\Models\Action::insert($aActions);
    }

    # reports > Member > Bet History By Products
    private function reports_member_bet_history_by_products()
    {
        $oMenu    = \App\Models\Menu::where('code', 'reports_member_bet_history_by_products')->first();
        $aActions = [
            // /api/backstage/drop_list/game_bet_detail get
            // /api/backstage/game_bet_details get
            // /api/backstage/game_bet_details/excel get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'game_bet_details index',
                'action'        => 'backstage.game_bet_details.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/game_bet_details',
                'drop_list_url' => '/api/backstage/drop_list/game_bet_detail',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'game_bet_details excel',
                'action'        => 'backstage.game_bet_details.excel',
                'method'        => 'GET',
                'url'           => '/api/backstage/game_bet_details/excel',
                'drop_list_url' => '/api/backstage/drop_list/game_bet_detail',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # reports > Member > Promotion Claim Reports
    private function reports_member_promotion_claim_reports()
    {
        $oMenu    = \App\Models\Menu::where('code', 'reports_member_promotion_claim_reports')->first();
        $aActions = [
            // /api/backstage/drop_list/promotion_claim_user get
            // /api/backstage/promotion_claim_users get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'promotion_claim_users index',
                'action'        => 'backstage.promotion_claim_users.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/promotion_claim_users',
                'drop_list_url' => '/api/backstage/drop_list/promotion_claim_user',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # reports > Member > CRM Report
    private function reports_member_crm_reports()
    {
        $oMenu    = \App\Models\Menu::where('code', 'reports_member_crm_reports')->first();
        $aActions = [
            // /api/backstage/crm_orders/report get
            // /api/backstage/crm_orders/report_user get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'crm_report index',
                'action'        => 'backstage.crm_orders.report',
                'method'        => 'GET',
                'url'           => '/api/backstage/crm_orders/report',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'crm_report_user index',
                'action'        => 'backstage.crm_orders.report_user',
                'method'        => 'GET',
                'url'           => '/api/backstage/crm_orders/report_user',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # reports > Marketing > Rebate Computation By Products
    private function reports_marketing_rebate_computation_by_products()
    {
        $oMenu    = \App\Models\Menu::where('code', 'reports_marketing_rebate_computation_by_products')->first();
        $aActions = [
            // /api/backstage/drop_list/rebate_computation_report get
            // /api/backstage/rebate_computation_reports get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'rebate_computation_reports index',
                'action'        => 'backstage.rebate_computation_reports.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/rebate_computation_reports',
                'drop_list_url' => '/api/backstage/drop_list/rebate_computation_report',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # reports > Marketing > Auto Rebate Initiate Payout Report
    private function reports_marketing_auto_rebate_initiate_payout_report()
    {
        $oMenu    = \App\Models\Menu::where('code', 'reports_marketing_auto_rebate_initiate_payout_report')->first();
        $aActions = [
            // /api/backstage/drop_list/user_rebate_prize get
            // /api/backstage/marketing/user_rebate_prizes get
            // /api/backstage/user_rebate_prizes/marketing_send patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'marketing user_rebate_prizes index',
                'action'        => 'backstage.marketing.user_rebate_prizes.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/marketing/user_rebate_prizes',
                'drop_list_url' => '/api/backstage/drop_list/user_rebate_prize',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user_rebate_prizes marketing_send update',
                'action'        => 'backstage.user_rebate_prizes.marketing_send.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/user_rebate_prizes/marketing_send',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # reports > Marketing > Promotion Reports
    private function reports_marketing_promotion_reports()
    {
        $oMenu    = \App\Models\Menu::where('code', 'reports_marketing_promotion_reports')->first();
        $aActions = [
            // /api/backstage/drop_list/user_bonus_prize get
            // /api/backstage/user_bonus_prizes/report_index get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user_bonus_prizes report_index index',
                'action'        => 'backstage.user_bonus_prizes.report_index.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/user_bonus_prizes/report_index',
                'drop_list_url' => '/api/backstage/drop_list/user_bonus_prize',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # reports > Marketing > Active Member Report
    private function reports_marketing_active_member_reports()
    {
        $oMenu    = \App\Models\Menu::where('code', 'reports_marketing_active_member_reports')->first();
        $aActions = [
            // /api/backstage/drop_list/active_user_report get
            // /api/backstage/active_user_report get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'active_user_report report',
                'action'        => 'backstage.active_user_report.report',
                'method'        => 'GET',
                'url'           => '/api/backstage/active_user_report',
                'drop_list_url' => '/api/backstage/drop_list/active_user_report',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/active_user_report_by_affiliate get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'active_user_report_by_affiliate report',
                'action'        => 'backstage.active_user_report_by_affiliate.report',
                'method'        => 'GET',
                'url'           => '/api/backstage/active_user_report_by_affiliate',
                'drop_list_url' => '/api/backstage/drop_list/active_user_report',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/active_user_report_by_product get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'active_user_report_by_product report',
                'action'        => 'backstage.active_user_report_by_product.report',
                'method'        => 'GET',
                'url'           => '/api/backstage/active_user_report_by_product',
                'drop_list_url' => '/api/backstage/drop_list/active_user_report',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # reports > Marketing > Member Activity Report
    private function reports_marketing_member_activity_reports()
    {
    }

    # reports > Compliance >
    private function reports_compliance_ip_activity()
    {
    }

    # reports > Compliance >
    private function reports_compliance_player_winner()
    {
    }

    # reports > Compliance >
    private function reports_compliance_player_winner_details()
    {
    }

    # reports > Compliance >
    private function reports_compliance_player_winner_details_per_date()
    {
    }


    # reward_setting
    private function reward_setting_currency_setting()
    {
    }

    private function reward_setting_member_profiling_reward()
    {
    }

    private function reward_setting_reward_category()
    {
    }

    private function reward_setting_reward_products()
    {
    }

    private function reward_members_member_listing_reward()
    {
    }

    private function reward_members_reward_adjustment()
    {
    }

    private function reward_members_redemption_report()
    {
    }


    # affiliate
    # Affiliate > Affiliate Listing
    private function affiliate_affiliate_listing()
    {
        $oMenu    = \App\Models\Menu::where('code', 'affiliate_affiliate_listing')->first();
        $aActions = [
            // /api/backstage/drop_list/active_user_report get
            // /api/backstage/active_user_report get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'active_user_report report',
                'action'        => 'backstage.active_user_report.report',
                'method'        => 'GET',
                'url'           => '/api/backstage/active_user_report',
                'drop_list_url' => '/api/backstage/drop_list/active_user_report',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # Affiliate > Affiliate Request List
    private function affiliate_affiliate_request_listing()
    {
        $oMenu    = \App\Models\Menu::where('code', 'affiliate_affiliate_request_listing')->first();
        $aActions = [
            // /api/backstage/affiliates/requests get
            // /api/backstage/drop_list/affiliate_request get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'affiliates requests',
                'action'        => 'backstage.affiliates.requests',
                'method'        => 'GET',
                'url'           => '/api/backstage/affiliates/requests',
                'drop_list_url' => '/api/backstage/drop_list/affiliate_request',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/affiliates/{affiliate}/request_approve patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'affiliates request_approve',
                'action'        => 'backstage.affiliates.request_approve',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/affiliates/{affiliate}/request_approve',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/affiliates/{affiliate}/request_reject patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'affiliates request_reject',
                'action'        => 'backstage.affiliates.request_reject',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/affiliates/{affiliate}/request_reject',
                'drop_list_url' => '',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # Affiliate > Sub Affiliate
    private function affiliate_sub_affiliate()
    {
        $oMenu    = \App\Models\Menu::where('code', 'affiliate_sub_affiliate')->first();
        $aActions = [
            // /api/backstage/affiliates/subs get
            // /api/backstage/drop_list/affiliate get
            // /api/backstage/currencies get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'affiliates subs index',
                'action'        => 'backstage.affiliates.subs.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/affiliates/subs',
                'drop_list_url' => '/api/backstage/drop_list/affiliate',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'currencies index',
                'action'        => 'backstage.currencies.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/currencies',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # Affiliate > Fund Management
    private function affiliate_fund_management()
    {
        $oMenu    = \App\Models\Menu::where('code', 'affiliate_fund_management')->first();
        $aActions = [
            // /api/backstage/affiliates/funds get
            // /api/backstage/drop_list/affiliate get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'affiliates funds index',
                'action'        => 'backstage.affiliates.funds.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/affiliates/funds',
                'drop_list_url' => '/api/backstage/drop_list/affiliate',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # Affiliate > Announcement
    private function affiliate_announcement()
    {
        $oMenu    = \App\Models\Menu::where('code', 'affiliate_announcement')->first();
        $aActions = [
            // /api/backstage/drop_list/affiliate_announcement get
            // /api/backstage/affiliate_announcements get post
            // /api/backstage/affiliate_announcements/{affiliate_announcement} patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'affiliate_announcements index',
                'action'        => 'backstage.affiliate_announcements.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/affiliate_announcements',
                'drop_list_url' => '/api/backstage/drop_list/affiliate_announcement',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'affiliate_announcements store',
                'action'        => 'backstage.affiliate_announcements.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/affiliate_announcements',
                'drop_list_url' => '/api/backstage/drop_list/affiliate_announcement',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'affiliate_announcements update',
                'action'        => 'backstage.affiliate_announcements.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/affiliate_announcements/{affiliate_announcement}',
                'drop_list_url' => '/api/backstage/drop_list/affiliate_announcement',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # Affiliate > Promotion
    private function affiliate_promotion()
    {
        $oMenu    = \App\Models\Menu::where('code', 'affiliate_promotion')->first();
        $aActions = [
            // /api/backstage/promotions get post
            // /api/backstage/promotions/{promotion} patch
            // /api/backstage/drop_list/promotion get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'promotions index',
                'action'        => 'backstage.promotions.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/promotions',
                'drop_list_url' => '/api/backstage/drop_list/promotion',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'promotions store',
                'action'        => 'backstage.promotions.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/promotions',
                'drop_list_url' => '/api/backstage/drop_list/promotion',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'promotions update',
                'action'        => 'backstage.promotions.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/promotions/{promotion}',
                'drop_list_url' => '/api/backstage/drop_list/promotion',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # Affiliate > Affiliate Report
    private function affiliate_affiliate_report()
    {
        $oMenu    = \App\Models\Menu::where('code', 'affiliate_affiliate_report')->first();
        $aActions = [
            // /api/backstage/affiliates/commissions/release patch
            // /api/backstage/drop_list/affiliate get
            // /api/backstage/affiliates/commissions/pending/download get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'commissions release store',
                'action'        => 'backstage.commissions.release.store',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/affiliates/commissions/release',
                'drop_list_url' => '/api/backstage/drop_list/affiliate',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'commissions release download',
                'action'        => 'backstage.commissions.pending.download',
                'method'        => 'GET',
                'url'           => '/api/backstage/affiliates/commissions/pending/download',
                'drop_list_url' => '/api/backstage/drop_list/affiliate',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # Affiliate > Creative Resources
    private function affiliate_creative_resources()
    {
        $oMenu    = \App\Models\Menu::where('code', 'affiliate_creative_resources')->first();
        $aActions = [
            // /api/backstage/affiliate/creative_resources get post patch delete
            // /api/backstage/drop_list/creative_resource get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'creative_resources index',
                'action'        => 'backstage.creative_resources.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/affiliate/creative_resources',
                'drop_list_url' => '/api/backstage/drop_list/creative_resource',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'creative_resources store',
                'action'        => 'backstage.creative_resources.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/affiliate/creative_resources',
                'drop_list_url' => '/api/backstage/drop_list/creative_resource',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'creative_resources update',
                'action'        => 'backstage.creative_resources.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/affiliate/creative_resources/{resource}',
                'drop_list_url' => '/api/backstage/drop_list/creative_resource',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'creative_resources delete',
                'action'        => 'backstage.creative_resources.delete',
                'method'        => 'DELETE',
                'url'           => '/api/backstage/affiliate/creative_resources/{resource}',
                'drop_list_url' => '/api/backstage/drop_list/creative_resource',
                'sort'          => 4,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # Affiliate > Tracking Statistics
    private function affiliate_tracking_statistics()
    {
        $oMenu    = \App\Models\Menu::where('code', 'affiliate_tracking_statistics')->first();
        $aActions = [
            // /api/backstage/tracking_statistic get
            // /api/backstage/drop_list/tracking_statistic get
            // /api/backstage/tracking_statistic/{statistic} get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'tracking_statistic index',
                'action'        => 'backstage.tracking_statistic.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/tracking_statistic',
                'drop_list_url' => '/api/backstage/drop_list/tracking_statistic',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'tracking_statistic detail',
                'action'        => 'backstage.tracking_statistic.detail',
                'method'        => 'GET',
                'url'           => '/api/backstage/tracking_statistic/{statistic}',
                'drop_list_url' => '/api/backstage/drop_list/tracking_statistic',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # Affiliate > Affiliate Mailbox Template
    private function affiliate_mailbox_template()
    {
        $oMenu    = \App\Models\Menu::where('code', 'affiliate_mailbox_template')->first();
        $aActions = [
            // /api/backstage/drop_list/mailbox_template get
            // /api/backstage/affiliate/mailbox_templates get post
            // /api/backstage/affiliate/mailbox_templates/{mailbox_template} delete
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'affiliate mailbox_templates index',
                'action'        => 'backstage.affiliate.mailbox_templates.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/affiliate/mailbox_templates',
                'drop_list_url' => '/api/backstage/drop_list/mailbox_template',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'affiliate mailbox_templates store',
                'action'        => 'backstage.affiliate.mailbox_templates.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/affiliate/mailbox_templates',
                'drop_list_url' => '/api/backstage/drop_list/mailbox_template',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'affiliate mailbox_templates delete',
                'action'        => 'backstage.affiliate.mailbox_templates.delete',
                'method'        => 'DELETE',
                'url'           => '/api/backstage/mailbox_templates/{mailbox_template}',
                'drop_list_url' => '/api/backstage/drop_list/mailbox_template',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }
    # 前端要求：start
    # Affiliate > affiliate_profile
    private function affiliate_profile()
    {
        $oMenu    = \App\Models\Menu::where('code', 'affiliate_profile')->first();
        $aActions = [
            // /api/backstage/affiliates/{affiliate} get
            // /api/backstage/affiliates/{affiliate} patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'affiliate detail',
                'action'        => 'backstage.affiliate.detail',
                'method'        => 'GET',
                'url'           => '/api/backstage/affiliates/{affiliate}',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'affiliate update',
                'action'        => 'backstage.affiliate.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/affiliates/{affiliate}',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/affiliates/{affiliate}/remarks get post
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'affiliate remarks index',
                'action'        => 'backstage.affiliate.remarks.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/affiliates/{affiliate}/remarks',
                'drop_list_url' => '',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'affiliate remarks store',
                'action'        => 'backstage.affiliate.remarks.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/affiliates/{affiliate}/remarks',
                'drop_list_url' => '',
                'sort'          => 4,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/affiliate/bank/history/{affiliate} get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'affiliate bank history index',
                'action'        => 'backstage.affiliate.bank.history.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/affiliate/bank/history/{affiliate}',
                'drop_list_url' => '',
                'sort'          => 5,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # Affiliate > affiliate_summary
    private function affiliate_summary()
    {
        $oMenu    = \App\Models\Menu::where('code', 'affiliate_summary')->first();
        $aActions = [
            // /api/backstage/drop_list/affiliate_commissions get
            // /api/backstage/affiliates/{affiliate} get
            // /api/backstage/affiliates/{affiliate}/profit_info get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'affiliate detail',
                'action'        => 'backstage.affiliate.detail',
                'method'        => 'GET',
                'url'           => '/api/backstage/affiliates/{affiliate}',
                'drop_list_url' => '/api/backstage/drop_list/affiliate_commissions',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'affiliate profit_info detail',
                'action'        => 'backstage.affiliate.profit_info.detail',
                'method'        => 'GET',
                'url'           => '/api/backstage/affiliates/{affiliate}/profit_info',
                'drop_list_url' => '/api/backstage/drop_list/affiliate_commissions',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # Affiliate > affiliate_member_detail
    private function affiliate_member_detail()
    {
        $oMenu    = \App\Models\Menu::where('code', 'affiliate_member_detail')->first();
        $aActions = [
            // /api/backstage/affiliates/{affiliate}/sub_users get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'affiliate sub_users index',
                'action'        => 'backstage.affiliate.sub_users.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/affiliates/{affiliate}/sub_users',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # Affiliate > summary_of_comm
    private function summary_of_comm()
    {
        $oMenu    = \App\Models\Menu::where('code', 'summary_of_comm')->first();
        $aActions = [
            // /api/backstage/affiliates/{affiliate}/commissions get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'affiliate commissions index',
                'action'        => 'backstage.affiliate.commissions.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/affiliates/{affiliate}/commissions',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # Affiliate > affiliate_payout_report
    private function affiliate_payout_report()
    {
        $oMenu    = \App\Models\Menu::where('code', 'affiliate_payout_report')->first();
        $aActions = [
            // /api/backstage/drop_list/affiliate get
            // /api/backstage/affiliates/commissions/payout get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'affiliate commissions payout index',
                'action'        => 'backstage.affiliate.commissions.payout.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/affiliates/commissions/payout',
                'drop_list_url' => '/api/backstage/drop_list/affiliate',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }
    # 前端要求：end


    # it
    # it > Game Report Schedules
    private function it_game_report_schedules()
    {
        $oMenu    = \App\Models\Menu::where('code', 'it_game_report_schedules')->first();
        $aActions = [
            // /api/backstage/game_platform_pull_report_schedules get patch
            // /api/backstage/drop_list/gamePlatformPullReportSchedule get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'game_platform_pull_report_schedules index',
                'action'        => 'backstage.game_platform_pull_report_schedules.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/game_platform_pull_report_schedules',
                'drop_list_url' => '/api/backstage/drop_list/gamePlatformPullReportSchedule',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'game_platform_pull_report_schedules update',
                'action'        => 'backstage.game_platform_pull_report_schedules.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/game_platform_pull_report_schedules/{schedule}',
                'drop_list_url' => '/api/backstage/drop_list/gamePlatformPullReportSchedule',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # it > User Product Daily Reports
    private function user_product_daily_reports()
    {
        $oMenu    = \App\Models\Menu::where('code', 'user_product_daily_reports')->first();
        $aActions = [
            // /api/backstage/user_product_daily_reports get
            // /api/backstage/drop_list/user_product_daily_report get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'user_product_daily_reports index',
                'action'        => 'backstage.user_product_daily_reports.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/user_product_daily_reports',
                'drop_list_url' => '/api/backstage/drop_list/user_product_daily_report',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # it > Language Setting
    private function language_setting()
    {
        $oMenu    = \App\Models\Menu::where('code', 'language_setting')->first();
        $aActions = [
            // /api/backstage/languages get patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'languages index',
                'action'        => 'backstage.languages.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/languages',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'languages update',
                'action'        => 'backstage.languages.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/languages/{language}',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # it > Game Currency Setting
    private function currency_setting()
    {
        $oMenu    = \App\Models\Menu::where('code', 'currency_setting')->first();
        $aActions = [
            // /api/backstage/currencies get patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'currencies index',
                'action'        => 'backstage.currencies.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/currencies',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'currencies update',
                'action'        => 'backstage.currencies.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/currencies/{currency}',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # it > Game Config Setting
    private function config_setting()
    {
        $oMenu    = \App\Models\Menu::where('code', 'config_setting')->first();
        $aActions = [
            // /api/backstage/configs get patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'configs index',
                'action'        => 'backstage.configs.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/configs',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'configs update',
                'action'        => 'backstage.configs.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/configs/{config}',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # it > Changing Config Setting
    private function changing_config_setting()
    {
        $oMenu    = \App\Models\Menu::where('code', 'changing_config_setting')->first();
        $aActions = [
            // /api/backstage/changing_configs get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'changing_configs index',
                'action'        => 'backstage.changing_configs.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/changing_configs',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    # it > Domain Management
    private function domain_management()
    {
        $oMenu    = \App\Models\Menu::where('code', 'domain_management')->first();
        $aActions = [
            // /api/backstage/domain_management get post patch delete
            // /api/backstage/drop_list/domain_management get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'domain_management index',
                'action'        => 'backstage.domain_management.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/domain_management',
                'drop_list_url' => '/api/backstage/drop_list/domain_management',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'domain_management store',
                'action'        => 'backstage.domain_management.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/domain_management',
                'drop_list_url' => '/api/backstage/drop_list/domain_management',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'domain_management update',
                'action'        => 'backstage.domain_management.update',
                'method'        => 'POST',
                'url'           => '/api/backstage/domain_management/{url}',
                'drop_list_url' => '/api/backstage/drop_list/domain_management',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'domain_management delete',
                'action'        => 'backstage.domain_management.delete',
                'method'        => 'DELETE',
                'url'           => '/api/backstage/domain_management/{url}',
                'drop_list_url' => '/api/backstage/drop_list/domain_management',
                'sort'          => 4,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }


    # system

    private function system_admin_role_management()
    {
        $oMenu    = \App\Models\Menu::where('code', 'system_admin_role_management')->first();
        $aActions = [
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'system admin role index',
                'action'        => 'backstage.system.admin_role.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/admin_roles',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'system admin role update',
                'action'        => 'backstage.system.admin_role.update',
                'method'        => 'POST',
                'url'           => '/api/backstage/admin_roles',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    private function system_action_management()
    {
        $oMenu    = \App\Models\Menu::where('code', 'system_action_management')->first();
        $aActions = [
            // /api/backstage/drop_list/action
            // /api/backstage/actions get post patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'system admin role index',
                'action'        => 'backstage.system.admin_role.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/admin_roles',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'system admin role update',
                'action'        => 'backstage.system.admin_role.update',
                'method'        => 'POST',
                'url'           => '/api/backstage/admin_roles',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }

    private function system_routes_management()
    {
        $oMenu    = \App\Models\Menu::where('code', 'system_routes_management')->first();
        $aActions = [
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'system routes index',
                'action'        => 'backstage.system.routes.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/routes',
                'drop_list_url' => '',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'system routes update',
                'action'        => 'backstage.system.routes.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/routes/update',
                'drop_list_url' => '',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }
    private function system_admin_management()
    {
        $oMenu    = \App\Models\Menu::where('code', 'system_admin_role_management')->first();
        $aActions = [
            // /api/backstage/drop_list/admin get
            // /api/backstage/admins get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'system admin index',
                'action'        => 'backstage.system.admin.index',
                'method'        => 'GET',
                'url'           => '/api/backstage/admins',
                'drop_list_url' => '/api/backstage/drop_list/admin',
                'sort'          => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/admins post
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'system admin store',
                'action'        => 'backstage.system.admin.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/admins',
                'drop_list_url' => '/api/backstage/drop_list/admin',
                'sort'          => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/admins/change/password patch
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'system admin update',
                'action'        => 'backstage.system.admin.update',
                'method'        => 'PATCH',
                'url'           => '/api/backstage/admins/change/password',
                'drop_list_url' => '',
                'sort'          => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/admin get
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'system admin me',
                'action'        => 'backstage.system.admin.me',
                'method'        => 'GET',
                'url'           => '/api/backstage/admin',
                'drop_list_url' => '',
                'sort'          => 4,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/admins/{admin} delete
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'system admin delete',
                'action'        => 'backstage.system.admin.delete',
                'method'        => 'DELETE',
                'url'           => '/api/backstage/admin/{admin}',
                'drop_list_url' => '',
                'sort'          => 5,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            // /api/backstage/admins/{admin}/admin_roles post
            [
                'menu_id'       => $oMenu->id,
                'name'          => 'system admin action store',
                'action'        => 'backstage.system.admin.action.store',
                'method'        => 'POST',
                'url'           => '/api/backstage/admins/{admin}/admin_roles',
                'drop_list_url' => '',
                'sort'          => 6,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        \App\Models\Action::insert($aActions);
    }
    private function system_bo_admin_management()
    {
    }
    private function system_menu_management()
    {
    }
}
