<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\GamePlatformTransferDetailRequest;
use App\Models\GamePlatformTransferDetail;
use App\Repositories\AdjustmentRepository;
use App\Repositories\GamePlatformTransferDetailRepository;
use App\Services\GamePlatformService;
use App\Transformers\GamePlatformTransferDetailTransformer;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class GamePlatformTransferDetailsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/users/{user_name}/game_platform_transfer_details",
     *      operationId="backstage.users.game_platform_transfer_details.index",
     *      tags={"Backstage-游戏"},
     *      summary="第三方平台转账明细",
     *      @OA\Parameter(
     *         name="user_name",
     *         in="path",
     *         description="会员名称",
     *         @OA\Schema(
     *             type="string"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/GamePlatformTransferDetail"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function userIndex(Request $request)
    {
        $details = QueryBuilder::for(GamePlatformTransferDetail::class)
                    ->allowedFilters(
                        Filter::scope('start_at'),
                        Filter::scope('end_at')
                    )
                    ->where('user_name', $request->route('user_name'))
                    ->paginate($request->per_page);

        return $this->response->paginator($details, new GamePlatformTransferDetailTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/game_platform_transfer_details",
     *      operationId="backstage.game_platform_transfer_details.index",
     *      tags={"Backstage-游戏"},
     *      summary="第三方平台转账明细",
     *      @OA\Parameter(name="filter[user_name]", in="query", description="会员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="创建开始时间", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="创建结束时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/GamePlatformTransferDetail"),
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
        $fields = [
            'id',
            'user_id',
            'user_name',
            'order_no',
            'from',
            'to',
            'order_no',
            'amount',
            'conversion_amount',
            'from_before_balance',
            'from_after_balance',
            'to_before_balance',
            'to_after_balance',
            'status',
            'admin_name',
            'created_at',
        ];

        $details = QueryBuilder::for(GamePlatformTransferDetail::class)
            ->select($fields)
            ->allowedFilters(
                'user_name',
                Filter::scope('start_at'),
                Filter::scope('end_at'),
                Filter::scope('currency')
            )
            ->latest()
            ->with('user')
            ->paginate($request->per_page);

        return $this->response->paginator($details, new GamePlatformTransferDetailTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/game_platform_transfer_details/{game_platform_transfer_detail}/add_check_job",
     *      operationId="backstage.game_platform_transfer_details.add_check_job",
     *      tags={"Backstage-游戏"},
     *      summary="添加checking状态的转账单到检查队列中",
     *      @OA\Parameter(
     *         name="game_platform_transfer_detail",
     *         in="path",
     *         description="第三方转账明细",
     *         @OA\Schema(
     *             type="string"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/GamePlatformTransferDetail"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function addCheckJob(GamePlatformTransferDetail $gamePlatformTransferDetail)
    {
        if (now()->diffInMinutes($gamePlatformTransferDetail->created_at, true) <= 5) {
            return $this->response->error('Please use this function after 5 minutes.', 422);
        }

        # 修改为waiting状态
        $gamePlatformTransferDetail->waiting('Manual');

        GamePlatformTransferDetailRepository::addCheckJob($gamePlatformTransferDetail);

        return $this->response->noContent();
    }

    /**
     * @OA\Patch(
     *      path="/backstage/game_platform_transfer_details/{game_platform_transfer_detail}/manual_success",
     *      operationId="backstage.users.game_platform_transfer_details.manual_success",
     *      tags={"Backstage-游戏"},
     *      summary="人工审核第三方转账明细成功",
     *      @OA\Parameter(
     *         name="game_platform_transfer_detail",
     *         in="path",
     *         description="第三方转账明细",
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
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *                  required={"remark"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="No Content.",
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function manualSuccess(
        GamePlatformTransferDetail $gamePlatformTransferDetail,
        GamePlatformTransferDetailRequest $request,
        GamePlatformService $service
    ) {
        if (!$gamePlatformTransferDetail->isWaitingConfirm()) {
            return $this->response->error('Error status.', 422);
        }

        # 判断是否关联到adjustment, 如果关联到不走帐变流程
        if (AdjustmentRepository::checkSuccessPlatformTransferDetail($gamePlatformTransferDetail)) {
            return $this->response->noContent();
        }

        # 更新状态成功
        $gamePlatformTransferDetail = GamePlatformTransferDetailRepository::setSuccess($gamePlatformTransferDetail, $this->user->name, $request->remark);

        # 后续处理
        if ($gamePlatformTransferDetail->isIncome()) {
            $service->transferInAfterDo($gamePlatformTransferDetail, $gamePlatformTransferDetail->user, $gamePlatformTransferDetail->userBonusPrize);
        } else {
            $service->transferOutAfterDo($gamePlatformTransferDetail, $gamePlatformTransferDetail->user, false);
        }

        return $this->response->noContent();
    }

    /**
     * @OA\Patch(
     *      path="/backstage/game_platform_transfer_details/{game_platform_transfer_detail}/manual_fail",
     *      operationId="backstage.users.game_platform_transfer_details.manual_fail",
     *      tags={"Backstage-游戏"},
     *      summary="人工审核第三方转账明细失败",
     *      @OA\Parameter(
     *         name="game_platform_transfer_detail",
     *         in="path",
     *         description="第三方转账明细",
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
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *                  required={"remark"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="No Content.",
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function manualFail(
        GamePlatformTransferDetail $gamePlatformTransferDetail,
        GamePlatformTransferDetailRequest $request,
        GamePlatformService $service
    ) {
        if (!$gamePlatformTransferDetail->isWaitingConfirm()) {
            return $this->response->error('Error status.', 422);
        }

        # 判断是否关联到adjustment, 如果关联到不走帐变流程
        if (AdjustmentRepository::checkSuccessPlatformTransferDetail($gamePlatformTransferDetail)) {
            return $this->response->noContent();
        }

        # 更新状态成功
        $gamePlatformTransferDetail = GamePlatformTransferDetailRepository::setFail($gamePlatformTransferDetail, '', $request->remark, $this->user->name);

        # 后续处理
        if ($gamePlatformTransferDetail->isIncome()) {
            $service->transferInAfterDo($gamePlatformTransferDetail, $gamePlatformTransferDetail->user, $gamePlatformTransferDetail->userBonusPrize);
        } else {
            $service->transferOutAfterDo($gamePlatformTransferDetail, $gamePlatformTransferDetail->user, false);
        }

        return $this->response->noContent();
    }
}
