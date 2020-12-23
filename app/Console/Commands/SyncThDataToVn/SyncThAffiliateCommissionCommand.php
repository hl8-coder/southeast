<?php

namespace App\Console\Commands\SyncThDataToVn;

use App\Imports\SyncThDataToVnImport;
use App\Models\AffiliateCommission;
use App\Models\SyncThLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class SyncThAffiliateCommissionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:sync-th-affiliate-commission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $commissions;

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
        $minPayout = 2000;
        $now = now();
        $this->initThData();

        $memIds = $this->commissions->pluck('agent_id')->unique()->toArray();
        $syncLogs = SyncThLog::query()->whereIn('old_id', $memIds)
                    ->where('is_agent', true)
                    ->where('status', true)
                    ->get();

        $this->info('----------------------开始迁移----------------------');

        $failCount = 0;
        foreach ($syncLogs as $log) {
            if ($user = User::find($log->new_id)) {
                $affiliate = $user->affiliate;
                $affiCommissions = $this->commissions->where('agent_id', $log->old_id);
                foreach ($affiCommissions as $affiCommission) {
                    try {
                        $this->sync($user, $affiliate, $affiCommission, $minPayout, $now);
                        $this->info('迁移代理(' . $affiCommission['agent_id'] . ')分红(' . $affiCommission['id'] . ')成功');
                    } catch (\Exception $e) {
                        $this->info('迁移代理(' . $affiCommission['agent_id'] . ')分红(' . $affiCommission['id'] . ')失败，原因：' . $e->getMessage());
                        $failCount++;
                        continue;
                    }
                }
            }
        }
        $this->info('----------------------结束迁移----------------------');
        $this->info('失败数：' . $failCount);
    }

    public function initThData()
    {
        $import = new SyncThDataToVnImport();
        $path   = app_path() . '/Console/Commands/SyncThDataToVn/';
        $this->commissions  = $this->dealImportData(Excel::toArray($import, $path . 'affiliate_commission.xlsx')[0]);
    }

    public function sync($user, $affiliate, $affiCommission, $minPayout, $now)
    {
        $date = Carbon::parse($affiCommission['year'] . '-' . $affiCommission['month'] . '-' . '01');

        $commission = [];
        $commission['user_id']           = $user->id;
        $commission['user_name']         = $user->name;
        $commission['currency']          = 'THB';
        $commission['affiliate_id']      = $affiliate->id;
        $commission['calculate_setting'] = [
            'tier'      => '3',
            'title'     => 'Comm %Tier 3',
            'value'     => !empty($affiCommission['commission_rate']) ? str_replace('%', '', $affiCommission['commission_rate']) : 40,
            'profit'    => 0,
        ];
        $commission['bank_id']              = null;
        $commission['city']                 = null;
        $commission['branch']               = null;
        $commission['account_no']           = null;
        $commission['account_name']         = null;
        $commission['profit']               = -1 * $affiCommission['total_winlos'];
        $commission['stake']                = $affiCommission['total_stake'];
        $commission['deposit']              = $affiCommission['total_dep'];
        $commission['withdrawal']           = $affiCommission['total_withdraw'];
        $commission['rebate']               = $affiCommission['rebate'];
        $commission['promotion']            = $affiCommission['promotion'];
        $commission['rake']                 = 0;
        $commission['sub_adjustment']       = 0;
        $commission['affiliate_adjustment'] = 0;
        $commission['active_count']         = !empty($affiCommission['active_mem']) ? $affiCommission['active_mem'] : 0;
        $commission['transaction_cost']     = $affiCommission['transaction_cost'];
        $commission['bear_cost']            = $commission['rebate'] + $commission['promotion'];
        $commission['net_loss']             = $affiCommission['net_winlos'];
        $commission['product_cost']         = 0;
        $commission['parent_commission']    = 0;
        $commission['sub_commission']       = 0;
        $commission['sub_commission_percent']        = 0;
        $commission['previous_remain_commission']    = $affiCommission['prev_balance'];
        $commission['remain_commission']    = $affiCommission['com_idr'] < $minPayout ? $affiCommission['com_idr'] : 0;
        $commission['total_commission']     = $affiCommission['com_idr'];
        $commission['payout_commission']    = $affiCommission['com_idr'] >= $minPayout ? $affiCommission['com_idr'] : 0;
        $commission['start_at']             = $date->startOfMonth()->toDateString();
        $commission['end_at']               = $date->endOfMonth()->toDateString();
        $commission['status']               = 3;
        $commission['last_access_at']       = null;
        $commission['last_access_name']     = null;
        $commission['created_at']           = $now;
        $commission['updated_at']           = '2011-11-11 11:11:11';

        AffiliateCommission::query()->create($commission);
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
