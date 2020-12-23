<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\ProfileRemarkRequest;
use App\Models\ProfileRemark;
use App\Models\User;
use App\Transformers\ProfileRemarkTransformer;
use Illuminate\Http\Request;

class ProfileRemarksController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/users/{user}/profile_remarks",
     *      operationId="backstage.profile_remarks.index",
     *      tags={"Backstage-会员"},
     *      summary="profile remark列表",
     *      @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="会员ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/ProfileRemark"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function index(User $user, Request $request)
    {
        $remarks = $user->profileRemarks()->latest()->paginate($request->per_page);

        return $this->response->paginator($remarks, new ProfileRemarkTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/users/{user}/profile_remarks",
     *      operationId="backstage.profile_remarks.store",
     *      tags={"Backstage-会员"},
     *      summary="添加profile remark",
     *      @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="会员ID",
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
     *                  @OA\Property(property="category", type="integer", description="分类"),
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *                  required={"user_id", "category", "remark"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/ProfileRemark"),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function store(User $user, ProfileRemarkRequest $request)
    {
        $data = remove_null($request->all());
        $data['user_id']    = $user->id;
        $data['admin_name'] = $this->user->name;
        $remark = ProfileRemark::query()->create($data);

        return $this->response->item(ProfileRemark::find($remark->id), new ProfileRemarkTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *      path="/backstage/profile_remarks/drop_list",
     *      operationId="backstage.profile_remarks.drop_list",
     *      tags={"Backstage-会员"},
     *      summary="ProfileRemark下拉列表【功能重复，请使用 /backstage/drop_list/{profile_remark} 】",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(),
     *          ),
     *      ),
     *     @OA\Response(response=401, description="授权不通过"),
     *     @OA\Response(response=422, description="验证错误"),
     *     security={
     *         {"bearer": {}}
     *     }
     *  )
     */
    # 该方法与 backstage/drop_list/{profile_remark} 重复
    # 但目前不确定是否在使用
    public function dropList()
    {
        $data = [];

        $data['category_id'] = transform_list(ProfileRemark::$categories);

        return $this->response->array($data);
    }
}
