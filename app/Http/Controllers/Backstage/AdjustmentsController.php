<?php

namespace App\Http\Controllers\Backstage;

use App\Exports\AdjustmentExport;
use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\AdjustmentRequest;
use App\Models\Adjustment;
use App\Models\GamePlatform;
use App\Models\TurnoverRequirement;
use App\Repositories\AdjustmentRepository;
use App\Repositories\UserRepository;
use App\Services\GamePlatformService;
use App\Services\RiskGroupService;
use App\Transformers\AdjustmentTransformer;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class AdjustmentsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/adjustments",
     *      operationId="backstage.adjustments.index",
     *      tags={"Backstage-会员账户"},
     *      summary="调整列表",
     *      @OA\Parameter(name="filter[id]", in="query", description="调整id", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[user_name]", in="query", description="会员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[type]", in="query", description="类型", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[category]", in="query", description="分类", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="注册查询开始日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="注册查询结束日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[reason]", in="query", description="调整理由", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[is_agent]", in="query", description="是否为代理", @OA\Schema(type="boolean")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Adjustment"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *         {"bearer": {}}
     *      }
     *  )
     */
    public function index(AdjustmentRequest $request)
    {
        $conditionArray = [
            'id'        => Filter::exact('id'),
            'user_name' => 'user_name',
            'type'      => Filter::exact('type'),
            'category'  => Filter::exact('category'),
            'start_at'  => Filter::scope('start_at'),
            'end_at'    => Filter::scope('end_at'),
            'is_agent'  => Filter::exact('is_agent')->ignore([true, false, null]),
            'status'    => Filter::exact('status'),
            'reason'    => 'reason',
            'currency'  => Filter::scope('currency'),
        ];

        $userName = $request->input('filter.user_name');
        $isAgent  = $request->input('filter.is_agent', false);

        $pagination = QueryBuilder::for(Adjustment::class)
            ->allowedFilters(array_values($conditionArray))
            ->whereHas('user', function ($query) use ($userName, $isAgent) {
                if ($userName) {
                    return $query->where('name', $userName)
                        ->where('is_agent', $isAgent);
                }
            })
            ->latest()
            ->paginate($request->per_page);

        $status = array_keys(Adjustment::$statuses);
        array_push($status, null);
        $type = array_values(Adjustment::$types);
        array_push($type, null);

        $conditionArray['status'] = Filter::exact('status')->ignore($status);
        $conditionArray['type']   = Filter::exact('type')->ignore($type);

        $total['transactions'] = QueryBuilder::for(Adjustment::class)
            ->allowedFilters(array_values($conditionArray))
            ->where('status', Adjustment::STATUS_SUCCESSFUL)
            ->whereHas('user', function ($query) use ($userName, $isAgent) {
                return $query->where('name', $userName)
                    ->where('is_agent', $isAgent);
            })
            ->count();

        $total['amount_add'] = thousands_number(QueryBuilder::for(Adjustment::class)
            ->allowedFilters(array_values($conditionArray))
            ->where('type', Adjustment::TYPE_DEPOSIT)
            ->where('status', Adjustment::STATUS_SUCCESSFUL)
            ->whereHas('user', function ($query) use ($userName, $isAgent) {
                return $query->where('name', $userName)
                    ->where('is_agent', $isAgent);
            })
            ->sum('amount'));

        $total['amount_dec'] = thousands_number(QueryBuilder::for(Adjustment::class)
            ->allowedFilters(array_values($conditionArray))
            ->where('type', Adjustment::TYPE_WITHDRAW)
            ->where('status', Adjustment::STATUS_SUCCESSFUL)
            ->whereHas('user', function ($query) use ($userName, $isAgent) {
                return $query->where('name', $userName)
                    ->where('is_agent', $isAgent);
            })
            ->sum('amount'));

        return $this->response->paginator($pagination, new AdjustmentTransformer())->addMeta('total', $total);
    }

    /**
     * @OA\Post(
     *      path="/backstage/users/{user_name}/adjustments",
     *      operationId="backstage.users.adjustments.store",
     *      tags={"Backstage-会员账户"},
     *      summary="调整会员账户",
     *      @OA\Parameter(
     *         name="user_name",
     *         in="path",
     *         description="会员名称",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="type", type="integer", description="调整类型"),
     *                  @OA\Property(property="amount", type="number", description="调整金额"),
     *                  @OA\Property(property="category", type="integer", description="分类"),
     *                  @OA\Property(property="platform_code", type="string", description="第三方平台code"),
     *                  @OA\Property(property="product_code", type="string", description="产品code"),
     *                  @OA\Property(property="turnover_closed_value", type="number", description="流水要求值"),
     *                  @OA\Property(property="reason", type="string", description="理由"),
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *                  required={"type", "amount", "category", "reason"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/Adjustment"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function store(AdjustmentRequest $request)
    {
        $is_agent = $request->is_agent ?? false;
        if ($is_agent) {
            $user = UserRepository::findAffiliateByName($request->route('user_name'));
        } else {
            $user = UserRepository::findByName($request->route('user_name'));
            # risk group limit adjustment check only work to user not the aff
            $riskGroupService = new RiskGroupService();
            $riskGroupService->checkUserCanDoAdjustment($user, $request->category);
        }

        if (!$user) {
            return $this->response->error('no user.', 422);
        }
        $data                       = remove_null($request->all());
        $data['user_id']            = $user->id;
        $data['user_name']          = $user->name;
        $data['created_admin_name'] = $this->user->name;

        $adjustment = Adjustment::query()->create($data);

        return $this->response->item($adjustment, new AdjustmentTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Delete(
     *      path="/backstage/adjustments/{adjustment}",
     *      operationId="api.adjustments.delete",
     *      tags={"Backstage-会员账户"},
     *      summary="拒绝调整",
     *      @OA\Parameter(
     *         name="adjustment",
     *         in="path",
     *         description="调整id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *                  required={"remark"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="拒绝成功",
     *          @OA\JsonContent(ref="#/components/schemas/Adjustment"),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function reject(Adjustment $adjustment, AdjustmentRequest $request)
    {
        $adjustment->reject($request->remark, $this->user->name);

        return $this->response->item($adjustment, new AdjustmentTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/adjustments/{adjustment}/approve",
     *      operationId="api.adjustments.approve",
     *      tags={"Backstage-会员账户"},
     *      summary="同意调整",
     *      @OA\Parameter(
     *         name="adjustment",
     *         in="path",
     *         description="调整id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="No Content",
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function approve(Adjustment $adjustment)
    {
        # 无第三方平台，正常异动主钱包
        # 有第三方平台
        # 正常发起第三方转账

        if (Adjustment::STATUS_PENDING == $adjustment->status) {
            $adjustment->adjusting();
        } else {
            return $this->response->error('Sorry! Transfer transaction status is not correct.', 422);
        }

        $adminName = $this->user->name;
        # 存在第三方游戏平台
        if ($platform = GamePlatform::findByCodeFromCache($adjustment->platform_code)) {

            $gamePlatformService = new GamePlatformService();
            $detail              = null;

            if ($adjustment->isDeposit()) {
                TurnoverRequirement::add($adjustment, $adjustment->is_turnover_closed);

                $detail = $gamePlatformService->redirectTransfer($platform, $adjustment->user, $adjustment->amount, 'Adjustment', true);

                if ($detail->isSuccess()) {
                    # 统计数据
                    AdjustmentRepository::recordReport($adjustment);
                }
            } else {
                $detail = $gamePlatformService->redirectTransfer($platform, $adjustment->user, $adjustment->amount, 'Adjustment', false);

                if ($detail->isSuccess()) {
                    # 判断关联流水被要求
                    AdjustmentRepository::closeTurnoverRequirement($adjustment);
                }
            }

            if ($detail) {
                $adjustment->update(['platform_transfer_detail_id' => $detail->id]);

                # 转账失败
                if ($detail->isFail()) {
                    $remark = 'Transfer to ' . $platform->code . ' failed.';
                    $adjustment->fail($adminName, $remark);
                    return $this->response->error('Sorry! Transfer transaction has been failed.', 422);
                }

                # 转账等待确认
                if ($detail->isNeedManualCheck()) {
                    $remark = 'Transfer to ' . $platform->code . ' need checking.';
                    $adjustment->waitingCheck($adminName, $remark);
                    return $this->response->error('Sorry! Transfer transaction has been need check.', 422);
                }

                # 转账成功
                if ($detail->isSuccess()) {
                    AdjustmentRepository::setSuccess($adjustment, $adminName);
                }
            }
        } else { # 不存在游戏平台
            AdjustmentRepository::adjustmentMainWallet($adjustment, $adminName);

            # 判断关联流水被要求
            if ($adjustment->isWithdrawal()) {
                AdjustmentRepository::closeTurnoverRequirement($adjustment);
            }
        }

        return $this->response->noContent();
    }

    /**
     * @OA\Delete(
     *      path="/backstage/adjustments/{adjustment}/close",
     *      operationId="backstage.adjustments.close",
     *      tags={"Backstage-会员账户"},
     *      summary="关闭会员调整流水",
     *      @OA\Parameter(name="user_bonus_prize", in="path", description="会员红利奖励", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=204, description="No Content",),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function close(Adjustment $adjustment, AdjustmentRequest $request)
    {
        # 关闭流水同时需要关闭流水要求值
        $adjustment->adminClose($this->user->name, $request->remark);

        return $this->response->noContent();
    }



    /**
     * @OA\Get(
     *      path="/backstage/adjustments/export",
     *      operationId="backstage.adjustments.export",
     *      tags={"Backstage-会员账户"},
     *      summary="调整列表",
     *      @OA\Parameter(name="filter[id]", in="query", description="调整id", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[user_name]", in="query", description="会员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[type]", in="query", description="类型", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[category]", in="query", description="分类", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="注册查询开始日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="注册查询结束日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[reason]", in="query", description="调整理由", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[is_agent]", in="query", description="是否为代理", @OA\Schema(type="boolean")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Adjustment"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *         {"bearer": {}}
     *      }
     *  )
     */
    public function adjustmentExport(AdjustmentRequest $request)
    {
        return Excel::download(new AdjustmentExport($request), 'adjustment.xlsx');
    }
}
