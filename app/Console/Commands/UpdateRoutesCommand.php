<?php

namespace App\Console\Commands;

use App\Models\Action;
use App\Models\Route;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateRoutesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:update-routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新系统路由到系统路由表';

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

        // $actionList = Action::all();
        // foreach ($actionList as $action) {
        //     $actionRole[] = [
        //         'action_id'     => $action->id,
        //         'admin_role_id' => 1,
        //         'created_at' => now(),
        //         'updated_at' =>now(),
        //     ];
        // }
        // DB::table('action_admin_role')->insert($actionRole);
        // dd(9);

        // // 从 action 表抓出数据
        // $actionList = Action::all();
        // $routeList = Route::all();
        // // 跟 route 表的数据比对，然后提取到方法
        // foreach ($actionList as $action){
        //     foreach ($routeList as $route){
        //         if ($action->url == $route->url && $route->method == $action->method){
        //             $action->remark = $route->remark;
        //             $action->action = $route->action;
        //             $action->save();
        //         }
        //     }
        // }
        // // 将方法回填到 action 表中
        // dd(1);

        $api           = app('Dingo\Api\Routing\Router');
        $versionRoutes = $api->getRoutes(); // array v1; actions names [api backstage]
        $actionList    = [];
        foreach ($versionRoutes as $version => $routes) {
            foreach ($routes as $key => $value) {
                $action                 = $value->action;
                $action['methods']      = $value->methods;
                $actionList[$version][] = $action;
            }
        }

        // 后台功能地址
        $actionInsert = [];
        foreach ($actionList['v1'] as $key => $value) {
            foreach ($value['methods'] as $method) {
                if ($method == 'HEAD' || strstr($value['uses'], '\Backstage\\') == false) {
                    continue;
                }
                $actionOne['action'] = $value['as'];
                $actionOne['name']   = str_replace('.', ' ', $value['as']);
                $actionOne['method'] = $method;
                $actionOne['url']    = $value['uri'];
                $actionOne['remark'] = str_replace('App\Http\Controllers\\', '', $value['uses']);
                $actionInsert[]      = $actionOne;
            }
        }

        try {
            batch_insert(app(Route::class)->getTable(), $actionInsert, true);
        } catch (\Exception $e) {
            Log::error($e);
            $this->error('报错拉，要看日志');
            return null;
        }

        $actionList = Action::all();
        $routeList = Route::all();
        // 跟 route 表的数据比对，然后提取到方法
        foreach ($actionList as $action){
            foreach ($routeList as $route){
                if ($action->url == $route->url && $route->method == $action->method){
                    $action->remark = $route->remark;
                    $action->action = $route->action;
                    $action->save();
                }
            }
        }

    }
}
