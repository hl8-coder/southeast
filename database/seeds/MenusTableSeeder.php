<?php

use Illuminate\Database\Seeder;

class MenusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (app()->environment() != 'local') {
            dd('菜单已经与权限系统绑定，不允许重置，请通过 API or SQL 对菜单做修改！');
        }

        \App\Models\Menu::query()->truncate();

        $aMenusLv1 = [
            [
                'name'        => 'SOP',
                'code'        => 'sop',
                'description' => '目前不知道做什麽用',
                'parent_id'   => null,
                'sort'        => 1,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Members',
                'code'        => 'members',
                'description' => '会员',
                'parent_id'   => null,
                'sort'        => 2,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Wallet Management',
                'code'        => 'wallet_management',
                'description' => '钱包管理',
                'parent_id'   => null,
                'sort'        => 3,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'CRM',
                'code'        => 'crm',
                'description' => 'CRM',
                'parent_id'   => null,
                'sort'        => 4,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Bank Management',
                'code'        => 'bank_management',
                'description' => '银行管理',
                'parent_id'   => null,
                'sort'        => 5,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Payment',
                'code'        => 'payment',
                'description' => 'Payment',
                'parent_id'   => null,
                'sort'        => 8,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Balance Adjustment',
                'code'        => 'balance_adjustment',
                'description' => '帐户调整',
                'parent_id'   => null,
                'sort'        => 9,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'RM Tool',
                'code'        => 'compliance_tools',
                'description' => '风控管理',
                'parent_id'   => null,
                'sort'        => 10,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],

            [
                'name'        => 'Communication Tools',
                'code'        => 'communication_tools',
                'description' => '讯息工具',
                'parent_id'   => null,
                'sort'        => 11,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Marketing Tools',
                'code'        => 'marketing_tools',
                'description' => '市场工具',
                'parent_id'   => null,
                'sort'        => 12,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Product Tool',
                'code'        => 'game_management',
                'description' => '游戏',
                'parent_id'   => null,
                'sort'        => 13,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Reports',
                'code'        => 'reports',
                'description' => '报表',
                'parent_id'   => null,
                'sort'        => 14,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Reward',
                'code'        => 'reward',
                'description' => '奖励',
                'parent_id'   => null,
                'sort'        => 15,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Affiliate',
                'code'        => 'affiliate',
                'description' => '代理',
                'parent_id'   => null,
                'sort'        => 16,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'IT Tool',
                'code'        => 'it',
                'description' => 'it',
                'parent_id'   => null,
                'sort'        => 17,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'System',
                'code'        => 'system',
                'description' => 'system info & setting',
                'parent_id'   => null,
                'sort'        => 18,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv1);

        // SOP 子菜单
        $oMenuLv1 = \App\Models\Menu::where('code', 'sop')->first();

        $aMenusLv2 = [
            [
                'name'        => 'User Listing',
                'code'        => 'sop_user_listing',
                'description' => '目前不知道做什麽用',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'User Role Listing',
                'code'        => 'sop_user_role_listing',
                'description' => '目前不知道做什麽用',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 2,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv2);

        // Members 子菜单
        $oMenuLv1 = \App\Models\Menu::where('code', 'members')->first();

        $aMenusLv2 = [
            [
                'name'        => 'Member Listing (CS)',
                'code'        => 'members_member_listing_cs',
                'description' => '会员列表(客服)',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 1,
                'is_show'     => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Member Listing (VIP)',
                'code'        => 'members_member_listing_vip',
                'description' => '会员列表(客服)',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 2,
                'has_action'  => 1,
                'is_show'     => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Promotion Checking Tool',
                'code'        => 'members_promotion_checking_tool',
                'description' => '优惠确认功具',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 4,
                'has_action'  => 1,
                'is_show'     => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Bank Account Listing',
                'code'        => 'members_bank_account_listing',
                'description' => '优惠确认功具',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 5,
                'has_action'  => 1,
                'is_show'     => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Members Profile',
                'code'        => 'members_profile',
                'description' => '会员详情',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 5,
                'has_action'  => 1,
                'is_show'     => false,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv2);

        // Wallet Management 子菜单
        $oMenuLv1 = \App\Models\Menu::where('code', 'wallet_management')->first();

        $aMenusLv2 = [
            [
                'name'        => 'Member Transfer Management',
                'code'        => 'wallet_management_member_transfer_management',
                'description' => '会员转帐管理',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Product\'s Wallet Status',
                'code'        => 'wallet_management_products_wallet_status',
                'description' => '产品钱包状鲜',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 2,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv2);

        // CRM 子菜单
        $oMenuLv1 = \App\Models\Menu::where('code', 'crm')->first();

        $aMenusLv2 = [
            [
                'name'        => 'Members (TSM)',
                'code'        => 'crm_member_tsm',
                'description' => '会员',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],

        ];

        \App\Models\Menu::insert($aMenusLv2);

        $oMenuLv2 = \App\Models\Menu::where('code', 'crm_member_tsm')->first();

        $aMenusLv3 = [
            [
                'name'        => 'Welcome Case Listing',
                'code'        => 'crm_member_tsm_welcome_case_listing',
                'description' => 'none',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Non Deposit & Retention Listing',
                'code'        => 'crm_member_tsm_non_deposit_retention_listing',
                'description' => '未充值列表',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 2,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Tagging',
                'code'        => 'crm_member_tsm_tagging',
                'description' => '标记',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 3,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv3);

        // Bank Management 子菜单
        $oMenuLv1 = \App\Models\Menu::where('code', 'bank_management')->first();

        $aMenusLv2 = [
            [
                'name'        => 'Bank Access',
                'code'        => 'bank_management_bank_access',
                'description' => '银行地址',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 1,
                'is_show'     => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Bank Management',
                'code'        => 'bank_management_bank_management',
                'description' => '银行管理',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 1,
                'is_show'     => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Account Group Management',
                'code'        => 'bank_management_account_group_management',
                'description' => '帐户群组管理',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 1,
                'is_show'     => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Bank Account Management',
                'code'        => 'bank_management_bank_account_management',
                'description' => '银行帐户管理',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 1,
                'is_show'     => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Bank Account Management detail and history',
                'code'        => 'bank_management_bank_account_management_detail_and_history',
                'description' => '银行帐户管理细节和历史',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 1,
                'is_show'     => false,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Bank Reconciliation Report',
                'code'        => 'bank_management_bank_reconciliation_report',
                'description' => '银行结算报表',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 1,
                'is_show'     => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'PG management',
                'code'        => 'bank_management_payment_platform_management',
                'description' => '支付平台列表',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 1,
                'is_show'     => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv2);

        // Payment 子菜单
        $oMenuLv1 = \App\Models\Menu::where('code', 'payment')->first();

        $aMenusLv2 = [
            [
                'name'        => 'Transaction Search',
                'code'        => 'payment_transaction_search',
                'description' => '交易查询',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Fund Transfers',
                'code'        => 'payment_fund_transfers',
                'description' => '转帐',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 2,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Deposit',
                'code'        => 'payment_deposit',
                'description' => '充值',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 3,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Withdrawal',
                'code'        => 'payment_withdraw',
                'description' => '提领',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 4,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Other',
                'code'        => 'payment_other',
                'description' => '其他',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 5,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ];

        \App\Models\Menu::insert($aMenusLv2);

        $oMenuLv2 = \App\Models\Menu::where('code', 'payment_fund_transfers')->first();

        $aMenusLv3 = [
            [
                'name'        => 'Member Listing',
                'code'        => 'payment_fund_transfers_member_listing',
                'description' => '忘了幹嗎用',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 1,
                'has_action'  => 1,
                'is_show'     => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Cash Flow',
                'code'        => 'payment_fund_transfers_cash_flow',
                'description' => '忘了幹嗎用',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 2,
                'has_action'  => 1,
                'is_show'     => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Account Summary(front)',
                'code'        => 'payment_fund_transfers_account_summary',
                'description' => '转账总览(前端)',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 3,
                'has_action'  => 1,
                'is_show'     => false,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Member Profile(front)',
                'code'        => 'payment_fund_transfers_member_profile',
                'description' => '用户转账总览(前端)',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 4,
                'has_action'  => 1,
                'is_show'     => false,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Transfer Remarks(front)',
                'code'        => 'payment_fund_transfers_remarks',
                'description' => '转账备注(前端)',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 5,
                'has_action'  => 1,
                'is_show'     => false,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Deposit History(front)',
                'code'        => 'payment_fund_transfers_deposit_history',
                'description' => '转账历史(前端)',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 6,
                'has_action'  => 1,
                'is_show'     => false,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Withdrawal History(front)',
                'code'        => 'payment_fund_transfers_withdrawal_history',
                'description' => '提款历史(前端)',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 7,
                'has_action'  => 1,
                'is_show'     => false,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Transfer Total Bet(front)',
                'code'        => 'payment_fund_transfers_total_bet',
                'description' => '投注总览(前端)',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 8,
                'has_action'  => 1,
                'is_show'     => false,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Affiliate Top Up (front)',
                'code'        => 'payment_fund_transfers_affiliates_top_up',
                'description' => '代理充值(前端)',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 9,
                'has_action'  => 1,
                'is_show'     => false,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Auto Rebate Initiate Payout Report (front)',
                'code'        => 'payment_fund_transfers_auto_rebate_report',
                'description' => '返点报表(前端)',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 8,
                'has_action'  => 1,
                'is_show'     => false,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv3);

        $oMenuLv2 = \App\Models\Menu::where('code', 'payment_deposit')->first();

        $aMenusLv3 = [
            [
                'name'        => 'Open Deposits',
                'code'        => 'payment_deposit_open_deposits',
                'description' => '所有充值',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Fast Deposit',
                'code'        => 'payment_deposit_fast_deposit',
                'description' => '网银充值单',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 2,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Gateway',
                'code'        => 'payment_deposit_gateway',
                'description' => '网银以外充值单',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 3,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Txn INQ-VBS',
                'code'        => 'payment_deposit_txn_inq_vbs',
                'description' => '银行交易记录',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 4,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Advance credit',
                'code'        => 'payment_deposit_advance_credit',
                'description' => '忘了幹嗎用',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 4,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv3);

        $oMenuLv2 = \App\Models\Menu::where('code', 'payment_withdraw')->first();

        $aMenusLv3 = [
            [
                'name'        => 'Open Withdrawal',
                'code'        => 'payment_withdraw_open_withdrawal',
                'description' => '忘了幹嗎用',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Bank Transfer',
                'code'        => 'payment_withdraw_bank_transfer',
                'description' => '忘了幹嗎用',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 2,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Process',
                'code'        => 'payment_withdraw_process',
                'description' => '忘了幹嗎用',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 3,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv3);

        $oMenuLv2 = \App\Models\Menu::where('code', 'payment_other')->first();

        $aMenusLv3 = [
            [
                'name'        => 'Rebate Settlement',
                'code'        => 'payment_other_rebate',
                'description' => 'payment 返点审核页面',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv3);

        $oMenuLv2 = \App\Models\Menu::where('code', 'balance_adjustment')->first();

        $aMenusLv3 = [
            [
                'name'        => 'Balance Batch Adjustment',
                'code'        => 'balance_adjustment_balance_batch_adjustment',
                'description' => '批次调整',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Batch Remark Upload',
                'code'        => 'balance_adjustment_batch_remark_upload',
                'description' => '批次备注上传',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 2,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv3);

        // Compliance Tools 子菜单
        $oMenuLv1 = \App\Models\Menu::where('code', 'compliance_tools')->first();

        $aMenusLv2 = [
            [
                'name'        => 'IP Monitor',
                'code'        => 'compliance_tools_ip_monitor_activity',
                'description' => 'IP监控',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Member Enquiry',
                'code'        => 'compliance_tools_member',
                'description' => '会员',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 2,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv2);

        $oMenuLv2 = \App\Models\Menu::where('code', 'compliance_tools_ip_monitor_activity')->first();

        $aMenusLv3 = [
            [
                'name'        => 'Members',
                'code'        => 'compliance_tools_ip_monitor_activity_member',
                'description' => '会员',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Affiliate',
                'code'        => 'compliance_tools_ip_monitor_activity_affiliate',
                'description' => '代理',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 2,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv3);

        $oMenuLv2 = \App\Models\Menu::where('code', 'compliance_tools_member')->first();

        $aMenusLv3 = [
            [
                'name'        => 'Player Winner',
                'code'        => 'compliance_tools_member_player_winner',
                'description' => '赢钱会员',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Player Winner Details',
                'code'        => 'compliance_tools_member_player_winner_details',
                'description' => '赢钱会员详情',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 2,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Player Winner Details Per Date',
                'code'        => 'compliance_tools_member_player_winner_details_per_date',
                'description' => '每日赢钱会员详情',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 3,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Members tag Remark',
                'code'        => 'compliance_tools_member_member_tag_remark',
                'description' => 'i don\'t know',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 4,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Member Risk',
                'code'        => 'compliance_tools_member_risk_category_listing',
                'description' => '风控类别管理',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 5,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Member Profile',
                'code'        => 'compliance_tools_member_member_data_query',
                'description' => '会员资料查询',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 6,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv3);

        // Communication Tools 子菜单
        $oMenuLv1 = \App\Models\Menu::where('code', 'communication_tools')->first();

        $aMenusLv2 = [
            [
                'name'        => 'Announcement',
                'code'        => 'communication_tools_announcement',
                'description' => '公告',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Private Message',
                'code'        => 'communication_tools_private_message',
                'description' => '私人讯息',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 2,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'SMS',
                'code'        => 'communication_tools_sms',
                'description' => '简讯',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 3,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Mailbox Template',
                'code'        => 'communication_tools_mailbox_template',
                'description' => '邮件模板',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 4,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Contact US',
                'code'        => 'communication_tools_contact_us',
                'description' => '联系我们',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 5,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv2);

        // Marketing Tools 子菜单
        $oMenuLv1 = \App\Models\Menu::where('code', 'marketing_tools')->first();

        $aMenusLv2 = [
            [
                'name'        => 'Banner',
                'code'        => 'marketing_tools_banner',
                'description' => 'banner',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Promotion',
                'code'        => 'marketing_tools_promotion',
                'description' => 'promotion',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Bonus Code',
                'code'        => 'marketing_tools_bonus_code',
                'description' => '优惠码',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Auto Rebate',
                'code'        => 'marketing_tools_auto_rebate',
                'description' => 'rebate',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 2,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Refer Friend',
                'code'        => 'marketing_tools_refer_friend',
                'description' => '币别等设定',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv2);

        $oMenuLv2 = \App\Models\Menu::where('code', 'marketing_tools_promotion')->first();

        $aMenusLv3 = [
            [
                'name'        => 'Promotion Type Setting',
                'code'        => 'marketing_tools_promotion_promotion_type_setting',
                'description' => '优惠类型设定',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Promotion Setting',
                'code'        => 'marketing_tools_promotion_promotion_setting',
                'description' => '优惠设定',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 2,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv3);

        $oMenuLv2  = \App\Models\Menu::where('code', 'marketing_tools_refer_friend')->first();// marketing_tools_refer_friend
        $aMenusLv3 = [
            [
                'name'        => 'Currency Setting',
                'code'        => 'marketing_tools_refer_friend_currency_setting',
                'description' => '币别设定',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Refer Friend Listings',
                'code'        => 'marketing_tools_refer_friend_refer_friend_listings',
                'description' => '目前不知道做什麽用',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 2,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Refer Friend Reports',
                'code'        => 'marketing_tools_refer_friend_refer_friend_reports',
                'description' => '目前不知道做什麽用',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 3,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];
        \App\Models\Menu::insert($aMenusLv3);

        $oMenuLv2 = \App\Models\Menu::where('code', 'marketing_tools_bonus_code')->first();

        $aMenusLv3 = [
            [
                'name'        => 'Bonus Group Setting',
                'code'        => 'marketing_tools_bonus_code_bonus_group_setting',
                'description' => '优惠码群组设定',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Bonus Code Setting',
                'code'        => 'marketing_tools_bonus_code_bonus_code_setting',
                'description' => '优惠码设定',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 2,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv3);

        $oMenuLv2 = \App\Models\Menu::where('code', 'marketing_tools_auto_rebate')->first();

        $aMenusLv3 = [
            [
                'name'        => 'Auto Rebate Setting By Products',
                'code'        => 'marketing_tools_auto_rebate_auto_rebate_setting_by_Products',
                'description' => '依产品自动回扣设定',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv3);

        // 游戏 子菜单
        $oMenuLv1 = \App\Models\Menu::where('code', 'game_management')->first();

        $aMenusLv2 = [
            [
                'name'        => 'Game Platforms',
                'code'        => 'game_platforms',
                'description' => '游戏平台',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Game Products',
                'code'        => 'game_products',
                'description' => '游戏产品',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 2,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Games',
                'code'        => 'games',
                'description' => '游戏',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 3,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv2);

        // Reports 子菜单
        $oMenuLv1 = \App\Models\Menu::where('code', 'reports')->first();

        $aMenusLv2 = [
            [
                'name'        => 'Member',
                'code'        => 'reports_member',
                'description' => '会员',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Marketing',
                'code'        => 'reports_marketing',
                'description' => '市场',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 2,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Compliance',
                'code'        => 'reports_compliance',
                'description' => '风控',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 3,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv2);

        $oMenuLv2 = \App\Models\Menu::where('code', 'reports_member')->first();

        $aMenusLv3 = [
            [
                'name'        => 'Case history',
                'code'        => 'reports_member_case_history',
                'description' => '案例记录',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Bet History By Products',
                'code'        => 'reports_member_bet_history_by_products',
                'description' => '产品投注记录',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 2,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Promotion Claim Reports',
                'code'        => 'reports_member_promotion_claim_reports',
                'description' => '优惠索赔报告',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 3,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'CRM Report',
                'code'        => 'reports_member_crm_reports',
                'description' => 'CRM 报表',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 4,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv3);

        $oMenuLv2 = \App\Models\Menu::where('code', 'reports_marketing')->first();

        $aMenusLv3 = [
            [
                'name'        => 'Rebate Computation By Products',
                'code'        => 'reports_marketing_rebate_computation_by_products',
                'description' => '产品回扣记算',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Auto Rebate Initiate Payout Report',
                'code'        => 'reports_marketing_auto_rebate_initiate_payout_report',
                'description' => '自动回扣启动付款报告',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 2,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Promotion Reports',
                'code'        => 'reports_marketing_promotion_reports',
                'description' => '优惠报告',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 3,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Active Member Reports',
                'code'        => 'reports_marketing_active_member_reports',
                'description' => '活跃会员报告',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 4,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Members Activity Reports',
                'code'        => 'reports_marketing_member_activity_reports',
                'description' => '成员活动报告',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 5,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv3);

        $oMenuLv2 = \App\Models\Menu::where('code', 'reports_compliance')->first();

        $aMenusLv3 = [
            [
                'name'        => 'IP Activity',
                'code'        => 'reports_compliance_ip_activity',
                'description' => 'IP活动',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Player Winner',
                'code'        => 'reports_compliance_player_winner',
                'description' => '赢钱会员',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 2,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Player Winner Details',
                'code'        => 'reports_compliance_player_winner_details',
                'description' => '赢钱会员详情',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 3,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Player Winner Details Per Date',
                'code'        => 'reports_compliance_player_winner_details_per_date',
                'description' => '每日赢钱会员详情',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 4,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv3);

        // Reward 子菜单
        $oMenuLv1 = \App\Models\Menu::where('code', 'reward')->first();

        $aMenusLv2 = [
            [
                'name'        => 'Setting',
                'code'        => 'reward_setting',
                'description' => '设定',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Members',
                'code'        => 'reward_members',
                'description' => '会员',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 2,
                'has_action'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv2);

        $oMenuLv2 = \App\Models\Menu::where('code', 'reward_setting')->first();

        $aMenusLv3 = [
            [
                'name'        => 'Currency Setting',
                'code'        => 'reward_setting_currency_setting',
                'description' => '币别设定',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Members profiling (Reward)',
                'code'        => 'reward_setting_member_profiling_reward',
                'description' => '会员奖励详情',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 2,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Reward Category',
                'code'        => 'reward_setting_reward_category',
                'description' => '奖励类型',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 3,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Reward Products',
                'code'        => 'reward_setting_reward_products',
                'description' => '奖励产品',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 4,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv3);

        $oMenuLv2 = \App\Models\Menu::where('code', 'reward_members')->first();

        $aMenusLv3 = [
            [
                'name'        => 'Member Listing (Reward)',
                'code'        => 'reward_members_member_listing_reward',
                'description' => '会员奖励列表',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Reward Adjustment',
                'code'        => 'reward_members_reward_adjustment',
                'description' => '奖励调整',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 2,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Redemption Report',
                'code'        => 'reward_members_redemption_report',
                'description' => '赎回报告',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 3,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv3);

        // Affiliate 子菜单
        $oMenuLv1 = \App\Models\Menu::where('code', 'affiliate')->first();

        $aMenusLv2 = [
            [
                'name'        => 'Affiliate Listing',
                'code'        => 'affiliate_affiliate_listing',
                'description' => '代理列表',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 1,
                'is_show'     => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Affiliate Request List',
                'code'        => 'affiliate_affiliate_request_listing',
                'description' => '代理申请列表',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 2,
                'has_action'  => 1,
                'is_show'     => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Sub Affiliate',
                'code'        => 'affiliate_sub_affiliate',
                'description' => '下级代理',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 3,
                'has_action'  => 1,
                'is_show'     => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Fund Managerment',
                'code'        => 'affiliate_fund_management',
                'description' => '分红管理',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 4,
                'has_action'  => 1,
                'is_show'     => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Announcement',
                'code'        => 'affiliate_announcement',
                'description' => '公告',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 5,
                'has_action'  => 1,
                'is_show'     => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Promotion',
                'code'        => 'affiliate_promotion',
                'description' => '优惠',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 5,
                'has_action'  => 1,
                'is_show'     => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Affiliate Report',
                'code'        => 'affiliate_affiliate_report',
                'description' => '代理报表',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 6,
                'has_action'  => 1,
                'is_show'     => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Creative Resources',
                'code'        => 'affiliate_creative_resources',
                'description' => '资源列表',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 7,
                'has_action'  => 1,
                'is_show'     => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Tracking Statistics',
                'code'        => 'affiliate_tracking_statistics',
                'description' => '列表点击',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 8,
                'has_action'  => 1,
                'is_show'     => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Affiliate Mailbox Template',
                'code'        => 'affiliate_mailbox_template',
                'description' => '代理邮件模板',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 9,
                'has_action'  => 1,
                'is_show'     => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'affiliate profile(front)',
                'code'        => 'affiliate_profile',
                'description' => '代理简介(front)',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 10,
                'has_action'  => 1,
                'is_show'     => false,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'affiliate summary(front)',
                'code'        => 'affiliate_summary',
                'description' => '代理总览(front)',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 11,
                'has_action'  => 1,
                'is_show'     => false,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'affiliate member detail(front)',
                'code'        => 'affiliate_member_detail',
                'description' => '代理会员信息(front)',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 12,
                'has_action'  => 1,
                'is_show'     => false,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'summary of comm(front)',
                'code'        => 'summary_of_comm',
                'description' => '不知道什么意思(front)',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 13,
                'has_action'  => 1,
                'is_show'     => false,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'affiliate payout report(front)',
                'code'        => 'affiliate_payout_report',
                'description' => '代理支付报表(front)',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 14,
                'has_action'  => 1,
                'is_show'     => false,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Affiliate Fund Transfers',
                'code'        => 'affiliate_fund_transfers',
                'description' => '转帐',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 10,
                'has_action'  => 1,
                'is_show'     => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Deposit History',
                'code'        => 'affiliate_deposit_history',
                'description' => '充值历史 前端需要',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 1,
                'has_action'  => 1,
                'is_show'     => false,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Affiliates Top Up',
                'code'        => 'affiliate_top_up',
                'description' => '余额调整 前端需要',
                'parent_id'   => $oMenuLv2->id,
                'sort'        => 3,
                'has_action'  => 1,
                'is_show'     => false,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv2);

        # Affiliate End

        // IT 子菜单
        $oMenuLv1 = \App\Models\Menu::where('code', 'it')->first();

        $aMenusLv2 = [
            [
                'name'        => 'Game Report Schedules',
                'code'        => 'it_game_report_schedules',
                'description' => '游戏报表拉取排程',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'User Product Daily Reports',
                'code'        => 'user_product_daily_reports',
                'description' => '游戏产品日报表',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 2,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Language Setting',
                'code'        => 'language_setting',
                'description' => '语言设置',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 3,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Currency Setting',
                'code'        => 'currency_setting',
                'description' => '币别设置',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 4,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Config Setting',
                'code'        => 'config_setting',
                'description' => '系统配置',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 5,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Changing Config Setting',
                'code'        => 'changing_config_setting',
                'description' => '系统高频配置（无缓存）',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 5,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
            [
                'name'        => 'Domain Management',
                'code'        => 'domain_management',
                'description' => '域名管理',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 6,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),

            ],
        ];

        \App\Models\Menu::insert($aMenusLv2);

        // System 子菜单
        $oMenuLv1  = \App\Models\Menu::where('code', 'system')->first();
        $aMenusLv2 = [
            [
                'name'        => 'Admin Management',
                'code'        => 'system_admin_management',
                'description' => '后台管理员列表',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'CRM BO Admin Management',
                'code'        => 'system_bo_admin_management',
                'description' => 'crm bo 管理员',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Admin Role Management',
                'code'        => 'system_admin_role_management',
                'description' => '管理员角色',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Menu Management',
                'code'        => 'system_menu_management',
                'description' => '系统菜单',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Action Management',
                'code'        => 'system_action_management',
                'description' => '操作设置管理',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Routes Management',
                'code'        => 'system_routes_management',
                'description' => '路由管理',
                'parent_id'   => $oMenuLv1->id,
                'sort'        => 1,
                'has_action'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ];

        \App\Models\Menu::insert($aMenusLv2);
    }

}
