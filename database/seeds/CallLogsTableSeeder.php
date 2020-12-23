<?php

use App\Models\Admin;
use App\Models\CallLog;
use App\Models\CrmOrder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CallLogsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //php artisan db:seed --class=CallLogsTableSeeder
        $crm_order_list = DB::table('crm_orders')->whereNotNull('tag_admin_name')->get();

        foreach ($crm_order_list as $item){
            $times = random_int(1,5);
            for($i=1;$i<=$times;$i++){

                $call_data =   [
                    'user_id' => $item->user_id,
                    'crm_id' => $item->id,
                    'admin_id' =>$item->tag_admin_id,
                    'admin_name' => $item->tag_admin_name,
                    'category' => $item->type,
                ];

                if($i > 1){
                    $tag_admin = App\Models\Admin::inRandomOrder()->first(); #随机捞取一个管理员做分发
                    $call_data['admin_id'] = $tag_admin->id;
                    $call_data['admin_name'] = $tag_admin->name;
                }
                $call_log = factory(App\Models\CallLog::class)->create(
                    $call_data
                );

                if ($i == $times) {
                    $crm_order = CrmOrder::find($item->id);
                    $crm_order->call_status = $call_log->status;
                    $crm_order->admin_id = $call_log->tag_admin_id;
                    $crm_order->admin_name = $call_log->tag_admin_name;
                    $crm_order->last_save_case_at = now();
                    $crm_order->save();
                }
            }
        }
    }
}
