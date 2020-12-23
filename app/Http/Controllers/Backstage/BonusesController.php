<?php

namespace App\Http\Controllers\Backstage;

use App\Exports\ExcelTemplateExport;
use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\BonusRequest;
use App\Models\Bonus;
use App\Models\BonusGroup;
use App\Models\GamePlatformProduct;
use App\Models\User;
use App\Transformers\BonusTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class BonusesController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/bonuses",
     *      operationId="backstage.bonuses.index",
     *      tags={"Backstage-红利"},
     *      summary="红利列表",
     *      @OA\Parameter(name="filter[code]", in="query", description="红利code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[product_code]", in="query", description="第三方游戏产品code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[bonus_group_id]", in="query", description="红利分组id", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="创建开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="创建结束时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[title]", in="query", description="红利标题", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[effective_start_at]", in="query", description="有效开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[effective_end_at]", in="query", description="有效结束时间", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Bonus"),
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
        $bonuses = QueryBuilder::for(Bonus::class)
            ->allowedFilters(
                Filter::exact('code'),
                Filter::exact('product_code'),
                'title',
                Filter::exact('status'),
                Filter::exact('bonus_group_id'),
                Filter::scope('start_at'),
                Filter::scope('end_at'),
                Filter::scope('effective_start_at'),
                Filter::scope('effective_end_at')
            )
            ->latest()
            ->paginate($request->per_page);

        return $this->response->paginator($bonuses, new BonusTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/bonuses",
     *      operationId="backstage.bonuses.store",
     *      tags={"Backstage-红利"},
     *      summary="添加红利",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="is_claim", type="boolean", description="是否需要申请"),
     *                  @OA\Property(property="category", type="integer", description="新旧红利"),
     *                  @OA\Property(property="languages", type="array", description="标题内容数组", @OA\Items(
     *                     @OA\Property(property="language", type="string", description="语言"),
     *                     @OA\Property(property="title", type="string", description="标题"),
     *                  )),
     *                  @OA\Property(property="code", type="string", description="红利代码"),
     *                  @OA\Property(property="product_code", type="string", description="产品代码"),
     *                  @OA\Property(property="effective_start_at", type="string", description="红利有效开始时间", format="date-time"),
     *                  @OA\Property(property="effective_end_at", type="string", description="红利有效结束时间", format="date-time"),
     *                  @OA\Property(property="sign_start_at", type="string", description="申请开始时间", format="date-time"),
     *                  @OA\Property(property="sign_end_at", type="string", description="申请结束时间", format="date-time"),
     *                  @OA\Property(property="status", type="boolean", description="状态"),
     *                  @OA\Property(property="bonus_group_id", type="integer", description="红利组别id"),
     *                  @OA\Property(property="type", type="integer", description="计算类型"),
     *                  @OA\Property(property="rollover", type="integer", description="流水倍数(本金+红利)"),
     *                  @OA\Property(property="amount", type="number", description="计算数值"),
     *                  @OA\Property(property="is_auto_hold_withdrawal", type="boolean", description="是否自动添加hold withdrawal标签"),
     *                  @OA\Property(property="cycle", type="integer", description="周期"),
     *                  @OA\Property(property="user_type", type="integer", description="会员类型"),
     *                  @OA\Property(property="risk_group_ids", type="string", description="风控组别"),
     *                  @OA\Property(property="payment_group_ids", type="string", description="支付组别"),
     *                  @OA\Property(property="user_ids", type="array", description="会员名称数组", @OA\Items()),
     *                  @OA\Property(property="currencies", type="array", description="币别", @OA\Items(
     *                     @OA\Property(property="currency", type="string", description="币别"),
     *                     @OA\Property(property="min_transfer", type="integer", description="最小转账金额"),
     *                     @OA\Property(property="deposit_count", type="integer", description="充值次数"),
     *                     @OA\Property(property="max_prize", type="integer", description="奖金上限"),
     *                  )),
     *                  required={"category", "title", "bonus_group_id", "code", "product_code", "currencies", "type", "cycle", "amount", "user_type", "effective_start_at", "effective_end_at"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/Bonus"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function store(BonusRequest $request)
    {
        $data = $this->dealData($request->all());

        $product = GamePlatformProduct::findByCodeFromCache($data['product_code']);

        $data['platform_code'] = $product->platform_code;

        $bonus = Bonus::query()->create($data);

        return $this->response->item($bonus->refresh(), new BonusTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *      path="/backstage/bonuses/{bonus}?include=prizes",
     *      operationId="backstage.bonuses.show",
     *      tags={"Backstage-红利"},
     *      summary="获取红利详情",
     *      @OA\Parameter(
     *         name="bonus",
     *         in="path",
     *         description="红利id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Bonus")
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function show(Bonus $bonus)
    {
        return $this->response->item($bonus, new BonusTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/bonuses/{bonus}/users",
     *      operationId="backstage.bonuses.users",
     *      tags={"Backstage-红利"},
     *      summary="获取红利会员详情",
     *      @OA\Parameter(
     *         name="bonus",
     *         in="path",
     *         description="红利id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function getUsers(Bonus $bonus, Request $request)
    {
        $users = User::query()->whereIn('id', $bonus->user_ids)->paginate($request->per_page);

        return $this->response->paginator($users, new UserTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/bonuses/{bonus}",
     *      operationId="backstage.bonuses.update",
     *      tags={"Backstage-红利"},
     *      summary="更新红利",
     *      @OA\Parameter(
     *         name="bonus",
     *         in="path",
     *         description="红利id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="is_claim", type="boolean", description="是否需要申请"),
     *                  @OA\Property(property="category", type="integer", description="新旧红利"),
     *                  @OA\Property(property="languages", type="array", description="标题内容数组", @OA\Items(
     *                     @OA\Property(property="language", type="string", description="语言"),
     *                     @OA\Property(property="title", type="string", description="标题"),
     *                  )),
     *                  @OA\Property(property="product_code", type="string", description="产品代码"),
     *                  @OA\Property(property="effective_start_at", type="string", description="红利有效开始时间", format="date-time"),
     *                  @OA\Property(property="effective_end_at", type="string", description="红利有效结束时间", format="date-time"),
     *                  @OA\Property(property="sign_start_at", type="string", description="申请开始时间", format="date-time"),
     *                  @OA\Property(property="sign_end_at", type="string", description="申请结束时间", format="date-time"),
     *                  @OA\Property(property="status", type="boolean", description="状态"),
     *                  @OA\Property(property="type", type="integer", description="计算类型"),
     *                  @OA\Property(property="rollover", type="integer", description="流水倍数(本金+红利)"),
     *                  @OA\Property(property="amount", type="number", description="计算数值"),
     *                  @OA\Property(property="is_auto_hold_withdrawal", type="boolean", description="是否自动添加hold withdrawal标签"),
     *                  @OA\Property(property="cycle", type="integer", description="周期"),
     *                  @OA\Property(property="user_type", type="integer", description="会员类型"),
     *                  @OA\Property(property="risk_group_ids", type="string", description="风控组别"),
     *                  @OA\Property(property="payment_group_ids", type="string", description="支付组别"),
     *                  @OA\Property(property="user_ids", type="array", description="会员名称数组", @OA\Items()),
     *                  @OA\Property(property="currencies", type="array", description="币别", @OA\Items(
     *                     @OA\Property(property="currency", type="string", description="币别"),
     *                     @OA\Property(property="min_transfer", type="integer", description="最小转账金额"),
     *                     @OA\Property(property="deposit_count", type="integer", description="充值次数"),
     *                     @OA\Property(property="max_prize", type="integer", description="奖金上限"),
     *                  )),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Bonus"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function update(Bonus $bonus, BonusRequest $request)
    {
        $data = $this->dealData($request->only([
            'is_claim',
            'category',
            'product_code',
            'effective_start_at',
            'effective_end_at',
            'sign_start_at',
            'sign_end_at',
            'languages',
            'status',
            'type',
            'rollover',
            'amount',
            'is_auto_hold_withdrawal',
            'cycle',
            'user_type',
            'risk_group_ids',
            'payment_group_ids',
            'user_ids',
            'currencies',
        ]));

        if (!empty($data['product_code'])) {
            $product = GamePlatformProduct::findByCodeFromCache($data['product_code']);
            $data['platform_code'] = $product->platform_code;
        }

        $bonus->update($data);

        return $this->response->item($bonus, new BonusTransformer());
    }

    protected function dealData($data)
    {
        $data = remove_null($data);

        if (isset($data['bonus_group_id'])) {
            $bonusGroup               = BonusGroup::findByCache($data['bonus_group_id']);
            $data['bonus_group_name'] = $bonusGroup->name;
        }

        $data['admin_name'] = $this->user->name;

        if (Bonus::isUserTypeList($data['user_type'])) {
            try {
                $data['user_ids'] = User::query()->where('is_agent', false)->whereIn('name', $data['user_ids'])->get(['id'])->pluck('id')->toArray();
            } catch (\Exception $e) {
                error_response(422, 'Error format.');
            }
        }

        return $data;
    }

    /**
     * @OA\Get(
     *      path="/backstage/bonuses/excel/download",
     *      operationId="backstage.bonuses.downloadExcelTemplate",
     *      tags={"Backstage-红利"},
     *      summary="下载模板",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\MediaType(
     *              mediaType="application/vnd.ms-excel",
     *              @OA\Items(ref="#/components/schemas/ExcelTemplateExport"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function downloadBonusExcelTemplate()
    {
        $headings = [
            'Member_code',
        ];
        return Excel::download(new ExcelTemplateExport([], $headings), 'message.xlsx');
    }
}
