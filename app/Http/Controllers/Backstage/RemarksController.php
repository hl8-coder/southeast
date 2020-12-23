<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\RemarkRequest;
use App\Models\Remark;
use App\Repositories\UserRepository;
use App\Transformers\RemarkTransformer;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class RemarksController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/remarks?include=user",
     *      operationId="backstage.remarks.index",
     *      tags={"Backstage-Remark"},
     *      summary="remark列表",
     *      @OA\Parameter(name="filter[user_id]", in="query", description="会员id", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[user_name]", in="query", description="会员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[category]", in="query", description="分类", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[type]", in="query", description="类型", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="创建开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="创建结束时间", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Remark"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function index(RemarkRequest $request)
    {
        $remarks = QueryBuilder::for(Remark::class)
                    ->withTrashed()
                    ->allowedFilters(
                        Filter::exact('user_id'),
                        Filter::scope('start_at'),
                        Filter::scope('end_at'),
                        Filter::scope('user_name'),
                        Filter::exact('category'),
                        Filter::scope('type')
                    )
                    ->latest()
                    ->paginate($request->per_page);

        return $this->response->paginator($remarks, new RemarkTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/remarks",
     *      operationId="backstage.remarks.store",
     *      tags={"Backstage-Remark"},
     *      summary="添加remark",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="user_id", type="integer", description="会员id"),
     *                  @OA\Property(property="type", type="integer", description="类型"),
     *                  @OA\Property(property="category", type="integer", description="分类"),
     *                  @OA\Property(property="sub_category", type="integer", description="子分类"),     
     *                  @OA\Property(property="reason", type="string", description="理由"),
     *                  required={"user_id", "type", "category", "reason"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/Remark"),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function store(RemarkRequest $request)
    {
        $data = remove_null($request->all());
        $data['admin_name'] = $this->user->name;
        $remark = Remark::query()->create($data);

        return $this->response->item($remark, new RemarkTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Post(
     *      path="/backstage/remarks/by/username",
     *      operationId="backstage.remarks.by_user_name.store",
     *      tags={"Backstage-Remark"},
     *      summary="添加remark",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="name", type="integer", description="会员名"),
     *                  @OA\Property(property="type", type="integer", description="类型"),
     *                  @OA\Property(property="category", type="integer", description="分类"),
     *                  @OA\Property(property="sub_category", type="integer", description="子分类"),
     *                  @OA\Property(property="reason", type="string", description="理由"),
     *                  required={"user_id", "type", "category", "reason"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/Remark"),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function storeRemarkByUsername(RemarkRequest $request)
    {
        $data = remove_null($request->all());
        $member = UserRepository::findByName($request['name']);
        $data['admin_name'] = $this->user->name;
        $data['user_id'] = $member->id;
        $remark = Remark::query()->create($data);

        return $this->response->item($remark, new RemarkTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Delete(
     *      path="/backstage/remarks/{remark}",
     *      operationId="backstage.remarks.delete",
     *      tags={"Backstage-Remark"},
     *      summary="删除Remark",
     *      @OA\Parameter(
     *         name="remark",
     *         in="path",
     *         description="Remark id",
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
     *                  @OA\Property(property="remove_reason", type="string", description="移除理由"),
     *                  required={"remove_reason"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=204,description="No Content"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function destroy(Remark $remark, RemarkRequest $request)
    {
        $remark->remove($request->remove_reason, $this->user->name);

        return $this->response->noContent();
    }
}
