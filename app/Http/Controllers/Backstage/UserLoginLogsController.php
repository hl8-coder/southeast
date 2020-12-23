<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Models\User;
use App\Models\UserLoginLog;
use App\Transformers\UserLoginLogTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Filter;

class UserLoginLogsController extends BackstageController
{

    /**
     * @OA\Get(
     *      path="/backstage/user_login_logs?include=user.info",
     *      operationId="backstage.user_login_logs.index",
     *      tags={"Backstage-会员"},
     *      summary="会员登录日志",
     *      @OA\Parameter(name="filter[user_name]", in="query", description="会员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[affiliated_code]", in="query", description="上级代理代码", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[ip]", in="query", description="ip", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="查询开始时间", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="查询结束时间", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/UserLoginLog"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function index(Request $request)
    {
        $logs = QueryBuilder::for(UserLoginLog::query())
                ->allowedFilters([
                    Filter::exact('user_name'),
                    Filter::scope('currency'),
                    Filter::scope('affiliated_code'),
                    'ip',
                    Filter::scope('start_at'),
                    Filter::scope('end_at'),
                ])
                ->whereHas('user', function($query) {
                    $query->where('is_agent', false);
                })
                ->latest()
                ->paginate($request->per_page);

        return $this->response->paginator($logs, new UserLoginLogTransformer('backstage_index'));
    }

    /**
     * @OA\Get(
     *      path="/backstage/user_login_logs/by_ip?include=user.info",
     *      operationId="backstage.user_login_logs.show",
     *      tags={"Backstage-会员"},
     *      summary="相同ip登录的会员",
     *      @OA\Parameter(
     *         name="ip",
     *         in="path",
     *         description="ip地址",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/UserLoginLog"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function getTheLogsByIp(Request $request)
    {
        if (empty($request->ip)) {
            return $this->response->error('Please input Ip', 422);
        }
        $logs = UserLoginLog::query()
            ->whereIn('id', UserLoginLog::query()->whereHas('user', function ($query) {
                $query->where('is_agent', false);
            })->where('ip', $request->ip)->select(DB::raw('max(id)'))->groupBy('user_name'))
            ->whereHas('user', function($query) {
                $query->where('is_agent', false);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page);
        return $this->response->paginator($logs, new UserLoginLogTransformer('backstage_index_by_ip'));
    }

    /**
     * @OA\Get(
     *      path="/backstage/affiliate_login_logs?include=user.info",
     *      operationId="backstage.affiliate_login_logs.index",
     *      tags={"Backstage-代理"},
     *      summary="代理登录日志",
     *      @OA\Parameter(name="filter[user_name]", in="query", description="代理名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[affiliate_code]", in="query", description="代理代码", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[ip]", in="query", description="ip", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="查询开始时间", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="查询结束时间", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/UserLoginLog"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function affiliateIndex(Request $request)
    {
        $logs = QueryBuilder::for(UserLoginLog::query())
            ->allowedFilters([
                Filter::exact('user_name'),
                Filter::scope('currency'),
                Filter::scope('affiliate_code'),
                'ip',
                Filter::scope('start_at'),
                Filter::scope('end_at'),
            ])
            ->whereHas('user', function($query) {
                $query->where('is_agent', true);
            })
            ->latest()
            ->paginate($request->per_page);

        return $this->response->paginator($logs, new UserLoginLogTransformer());
    }
}
