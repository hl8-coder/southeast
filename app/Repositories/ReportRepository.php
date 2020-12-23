<?php
namespace App\Repositories;

use App\Models\CompanyBankAccountReport;
use App\Models\PgAccountReport;
use App\Models\UserAccount;
use App\Models\UserPlatformDailyReport;
use App\Models\UserPlatformTotalReport;
use App\Models\UserProductDailyReport;
use App\Models\UserProductTotalReport;
use App\Services\GamePlatformService;
use Illuminate\Support\Facades\DB;

class ReportRepository
{
    public static function getUserPlatformTotalReport($userName)
    {
        $result = [];
        # 先查询出会员平台报表
        $platformReports = UserPlatformTotalReport::query()->where('user_name', $userName)
                            ->where('platform_code', '!=',UserAccount::MAIN_WALLET)
                            ->get();
        $productReports  =  UserProductTotalReport::query()->where('user_name', $userName)->get();

        #获取会员所有平台
        $user = UserRepository::findByName($userName);

        $gamePlatformService = new GamePlatformService();

        foreach ($user->gamePlatformUsers as $key=>$gamePlatformUser) {
            $platformCode = $gamePlatformUser->platform_code;
            $result[$key]['platform_code'] = $platformCode;
            if ($platformReport = $platformReports->where('platform_code', $platformCode)->first()) {
                $result[$key]['transfer_in']        = $platformReport->transfer_in;
                $result[$key]['transfer_out']       = $platformReport->transfer_out;
                $result[$key]['adjustment_in']      = $platformReport->adjustment_in;
                $result[$key]['adjustment_out']     = $platformReport->adjustment_out;
                $result[$key]['promotion']          = $platformReport->promotion;
            } else {
                $result[$key]['transfer_in']        = 0;
                $result[$key]['transfer_out']       = 0;
                $result[$key]['adjustment_in']      = 0;
                $result[$key]['adjustment_out']     = 0;
                $result[$key]['promotion']          = 0;
            }
            $result[$key]['profit']     = $productReports->where('platform_code', $platformCode)->sum('profit');
            $remotePlatformUser         = $gamePlatformService->getGamePlatformUserByUser($user, $platformCode, false);
            $result[$key]['balance']    = $remotePlatformUser->balance;
        }
        
        return $result;
    }

    public static function getUserPlatformReport($data)
    {
        $userName = $data['user_name'];

        $result = [];
        # 先查询出会员平台报表
        $platformBuilder =  UserPlatformDailyReport::query()->where('user_name', $userName);
        $productBuilder  =  UserProductDailyReport::query()->where('user_name', $userName);

        if (isset($data['filter']['start_at'])) {
            $platformBuilder->where('date', '>=', $data['filter']['start_at']);
            $productBuilder->where('date', '>=', $data['filter']['start_at']);
        }

        if (isset($data['filter']['end_at'])) {
            $platformBuilder->where('date', '<=', $data['filter']['end_at']);
            $productBuilder->where('date', '<=', $data['filter']['end_at']);
        }

        $platformReports = $platformBuilder->groupBy('platform_code')->get([
            'platform_code',
            DB::raw('SUM(deposit) as deposit'),
            DB::raw('SUM(withdrawal) as withdrawal'),
            DB::raw('SUM(transfer_in) as transfer_in'),
            DB::raw('SUM(transfer_out) as transfer_out'),
            DB::raw('SUM(adjustment) as adjustment'),
        ]);

        $productReports = $productBuilder->groupBy('platform_code')->get([
            'platform_code',
            DB::raw('SUM(profit) as profit'),
        ]);

        #获取会员所有平台
        $user = UserRepository::findByName($userName);
        $gamePlatformUsers = $user->gamePlatformUsers;
        $gamePlatformCodes = $gamePlatformUsers->pluck('platform_code')->toArray();
        array_unshift($gamePlatformCodes, UserAccount::MAIN_WALLET);

        foreach ($gamePlatformCodes as $key => $gamePlatformCode) {

            $result[$key]['platform_code'] = $gamePlatformCode;

            if (isset($platformReports[$gamePlatformCode])) {
                if (UserAccount::isMainWallet($gamePlatformCode)) {
                    $result[$key]['deposit']      = $platformReports['deposit'];
                    $result[$key]['withdrawal']   = $platformReports['withdrawal'];
                } else {
                    $result[$key]['deposit']      = $platformReports['transfer_in'];
                    $result[$key]['withdrawal']   = $platformReports['transfer_out'];
                }
                $result[$key]['adjustment']   = $platformReports['adjustment'];
            } else {
                $result[$key]['deposit']      = 0;
                $result[$key]['withdrawal']   = 0;
                $result[$key]['adjustment']   = 0;
            }

            if (isset($productReports[$gamePlatformCode])) {
                $result[$key]['profit']      = $productReports['profit'];
            } else {
                $result[$key]['profit']      = 0;
            }

            # 钱包余额
            if (UserAccount::isMainWallet($gamePlatformCode)) {
                $result[$key]['balance']      = $user->account->getAvailableBalance();
            } else {
                $result[$key]['balance']      = $gamePlatformUsers->where('platform_code', $gamePlatformCode)->first()->balance;
            }
        }

        return $result;
    }

    public static function getCompanyBankAccountReports($perPage, $startAt=null, $endAt=null)
    {
        $builder = CompanyBankAccountReport::query();

        if (!empty($startAt)) {
            $builder->where('date', '>=', $startAt);
        }

        if (!empty($endAt)) {
            $builder->where('date', '<=', $endAt);
        }

        return $builder->groupBy('company_bank_account_code')
            ->paginate($perPage, [
                'company_bank_account_code',
                DB::raw('(select opening_balance from company_bank_account_reports order by id asc limit 1) as opening_balance'),
                DB::raw('(select ending_balance from company_bank_account_reports order by id desc limit 1) as ending_balance'),
                DB::raw('SUM(buffer_in) as buffer_in'),
                DB::raw('SUM(buffer_out) as buffer_out'),
                DB::raw('SUM(deposit) as deposit'),
                DB::raw('SUM(withdrawal) as withdrawal'),
                DB::raw('SUM(adjustment) as adjustment'),
                DB::raw('SUM(internal_transfer) as internal_transfer'),
            ]);
    }

    public static function getPgAccountReports($perPage,$startAt=null,$endAt=null)
    {
        $builder = PgAccountReport::query();

        if (!empty($startAt)) {
            $builder->where('date', '>=', $startAt);
        }

        if (!empty($endAt)) {
            $builder->where('date', '<=', $endAt);
        }

        return $builder->paginate($perPage);
    }
}