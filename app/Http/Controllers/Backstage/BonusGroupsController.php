<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\BonusGroupRequest;
use App\Models\BonusGroup;
use App\Transformers\BonusGroupTransformer;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;

class BonusGroupsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/bonus_groups",
     *      operationId="backstage.bonus_groups.index",
     *      tags={"Backstage-红利"},
     *      @OA\Parameter(name="filter[name]", in="query", description="分组名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="查询创建开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="查询创建结束时间", @OA\Schema(type="string")),
     *      summary="红利分组",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/BonusGroup"),
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
        $groups = QueryBuilder::for(BonusGroup::class)
                ->allowedFilters(
                    Filter::exact('name'),
                    Filter::scope('start_at'),
                    Filter::scope('end_at')
                )
                ->latest()
                ->paginate($request->per_page);
        return $this->response->paginator($groups, new BonusGroupTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/bonus_groups",
     *      operationId="backstage.bonus_groups.store",
     *      tags={"Backstage-红利"},
     *      summary="添加红利分组",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="name", type="string", description="名称"),
     *                  required={"name"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/BonusGroup"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function store(BonusGroupRequest $request)
    {
        $group = BonusGroup::query()->create([
            'name'         => $request->name,
            'admin_name'   => $this->user->name,
        ]);

        return $this->response->item(BonusGroup::find($group->id), new BonusGroupTransformer())->setStatusCode(201);
    }
}
