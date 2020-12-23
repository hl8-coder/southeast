<?php

namespace App\Console\Commands;

use App\Models\Game;
use App\Models\GameBetDetail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigrateGameBetDetailToHistoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:migrate-game_bet-detail-to-history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '迁移不会再进行更新操作的游戏数据到历史表';

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
     *
     * //TODO 每次迁移15天前的数据 注意!!!!!!! 被迁移的游戏数据需要保证不会再更新  而且  从第三方厂商的api 在15天后 不会再获取到15天前的数据!!!!!!!!
     * 注意:只能迁移不会再执行更新的数据到历史数据表中.
     *
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        // 判断是否打开迁移.
        if(!config('app')['is_migrate_game_detail_to_history']) {
            Log::stack(['command_migrate_game_data'])->info("date:" . date('Y-m-d H:i:s') . " 迁移配置未开启");
            return;
        }

        $where = array(
            'status' => GameBetDetail::STATUS_SUCCESS,
            ['created_at','<=',date('Y-m-d H:i:s',strtotime('-15 days'))]
        );


        // 获取本次需要迁移的最大最小id
        $minId = GameBetDetail::where($where)->min('id');

        $maxId = GameBetDetail::where($where)->max('id');

        if (!$maxId) {
            Log::stack(['command_migrate_game_data'])->info("date:" . date('Y-m-d H:i:s') . " 不存在待迁移的数据");
            return;
        }

        if (!$minId) {
            $minId = 0;
        }
        // 获取待迁移的总条数.
        $count = GameBetDetail::where('status','=',GameBetDetail::STATUS_SUCCESS)->where("id",'>',$minId)->where('id','<=',$maxId)->count();

        $size = 5000; // 每次处理条数.
        $totalPage = ceil($count/$size); // 处理次数.

        Log::stack(['command_migrate_game_data'])->info('date:' . date('Y-m-d H:i:s') . ' 待迁移数据条数:' . $count . " 每次迁移条数:" . $size . " 累计迁移次数:" . $totalPage . " minId".$minId. " maxId:".$maxId);

        $lastMaxId = 1; // 用于调控范围,避免有异常数据无法迁移导致 后续数据迁移问题.

        for ($page = 1; $page <= $totalPage;$page++) {

            $gameBetDetailList = array();

            $offset = ($page - 1) * $size;// 因为内部执行删除操作  不用offset.

            $list = GameBetDetail::where('status','=',GameBetDetail::STATUS_SUCCESS)->where('id','>',$lastMaxId)->where('id','<=',$maxId)->limit($size)->get();

            if (!empty($list)) {
                foreach ($list as $info) {
                    $info = $info->toArray();
                    // 数据处理  避免异常数据插入导致异常.
                    $info['platform_code'] = !empty($info['platform_code']) ? $info['platform_code'] : '';
                    $info['product_code'] = !empty($info['product_code']) ? $info['product_code'] : '';
                    $info['platform_currency'] = !empty($info['platform_currency']) ? $info['platform_currency'] : '';
                    $info['order_id'] = !empty($info['order_id']) ? $info['order_id'] : '';
                    $info['game_type'] = !empty($info['game_type']) ? $info['game_type'] : null;
                    $info['game_code'] = !empty($info['game_code']) ? $info['game_code'] : '';
                    $info['game_name'] = !empty($info['game_name']) ? $info['game_name'] : '';
                    $info['user_name'] = !empty($info['user_name']) ? $info['user_name'] : '';
                    $info['issue'] = !empty($info['issue']) ? $info['issue'] : '';
                    $info['stake'] = !empty($info['stake']) ? $info['stake'] : 0;
                    $info['bet'] = !empty($info['bet']) ? $info['bet'] : 0;
                    $info['prize'] = !empty($info['prize']) ? $info['prize'] : 0;
                    $info['profit'] = !empty($info['profit']) ? $info['profit'] : 0;
                    $info['odds'] = !empty($info['odds']) ? $info['odds'] : null;
                    $info['after_balance'] = !empty($info['after_balance']) ? $info['after_balance'] : 0;
                    $info['bet_at'] = !empty($info['bet_at']) ? $info['bet_at'] : null;
                    $info['payout_at'] = !empty($info['payout_at']) ? $info['payout_at'] : NULL;
                    $info['user_stake'] = !empty($info['user_stake']) ? $info['user_stake'] : 0;
                    $info['multiple'] = !empty($info['multiple']) ? $info['multiple'] : 0;
                    $info['money_unit'] = !empty($info['money_unit']) ? $info['money_unit'] : '';
                    $info['bet_info'] = !empty($info['bet_info']) ? $info['bet_info'] : '';
                    $info['win_info'] = !empty($info['win_info']) ? $info['win_info'] : '';
                    $info['user_prize_group'] = !empty($info['user_prize_group']) ? $info['user_prize_group'] : '';
                    $info['available_bet'] = !empty($info['available_bet']) ? $info['available_bet'] : 0;
                    $info['available_profit'] = !empty($info['available_profit']) ? $info['available_profit'] : 0;
                    $info['available_rebate_bet'] = !empty($info['available_rebate_bet']) ? $info['available_rebate_bet'] : 0;
                    $info['jpc'] = !empty($info['jpc']) ? $info['jpc'] : 0;
                    $info['jpw'] = !empty($info['jpw']) ? $info['jpw'] : 0;
                    $info['jpw_jpc'] = !empty($info['jpw_jpc']) ? $info['jpw_jpc'] : 0;
                    $info['is_close'] = !empty($info['is_close']) ? $info['is_close'] : 0;
                    $info['platform_status'] = !empty($info['platform_status']) ? $info['platform_status'] : 1;
                    $info['status'] = !empty($info['status']) ? $info['status'] : 1;
                    $info['finished_at'] = !empty($info['finished_at']) ? $info['finished_at'] : null;
                    $info['remark'] = !empty($info['remark']) ? $info['remark'] : '';
                    $info['trace_logs'] = !empty($info['trace_logs']) ? json_encode($info['trace_logs']) : array();

                    $gameBetDetailList[] = $info;
                }

                if (!empty($gameBetDetailList)) {
                    // 该批次数据 最大的id值.
                    $lastMaxId = collect($gameBetDetailList)->max('id');

                    try {

                        DB::transaction(function () use ($gameBetDetailList, $where, $page, $lastMaxId) {
                            if (!empty($gameBetDetailList)) {
                                $migrateStatus = batch_insert('game_bet_history_details', $gameBetDetailList, false);

                                // 迁移成功 执行删除操作.
                                if ($migrateStatus) {
                                    GameBetDetail::whereIn('id',array_column($gameBetDetailList,'id'))->delete();

                                    Log::stack(['command_migrate_game_data'])->info("第" . $page . "次迁移 status:success! count:" . count($gameBetDetailList)." maxId:".$lastMaxId);
                                }
                            }
                        });

                    } catch (\Exception $exception) {
                        Log::stack(['command_migrate_game_data'])->info("第" . $page . "次迁移 status:fail!"." error_code:" . $exception->getCode() . "|error:" . $exception->getMessage());
                    }
                }
            }
        }
    }
}
