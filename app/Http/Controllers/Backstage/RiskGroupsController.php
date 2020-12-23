<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\RiskGroupRequest;
use App\Models\RiskGroup;
use App\Models\User;
use App\Services\RiskGroupService;
use App\Services\UserService;
use App\Transformers\AuditTransformer;
use App\Transformers\RiskGroupTransformer;

class RiskGroupsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/risk_groups",
     *      operationId="backstage.risk_groups.index",
     *      tags={"Backstage-风控分组"},
     *      summary="风控分组列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/RiskGroup"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function index()
    {
        $groups = RiskGroup::getAll()->sortBy('sort');

        return $this->response->collection($groups, new RiskGroupTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/risk_groups",
     *      operationId="backstage.risk_groups.store",
     *      tags={"Backstage-风控分组"},
     *      summary="添加风控分组",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="name", type="string", description="分组名称"),
     *                  @OA\Property(property="rules", type="array", description="分组名称", @OA\Items()),
     *                  @OA\Property(property="description", type="string", description="描述"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  required={"name"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/RiskGroup"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function store(RiskGroupRequest $request)
    {
        $data = remove_null($request->all());
        if ($request->has('rules')) {
            $data['rules'] = remove_null($request->input('rules'));
        }

        $group = RiskGroup::query()->create($data);

        return $this->response->item(RiskGroup::findByCache($group->id), new RiskGroupTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Patch(
     *      path="/backstage/risk_groups/{risk_group}",
     *      operationId="backstage.risk_groups.update",
     *      tags={"Backstage-风控分组"},
     *      summary="更新风控分组信息",
     *      @OA\Parameter(
     *         name="risk_group",
     *         in="path",
     *         description="风控分组id",
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
     *                  @OA\Property(property="name", type="string", description="分组名称"),
     *                  @OA\Property(property="rules", type="array", description="分组名称", @OA\Items()),
     *                  @OA\Property(property="description", type="string", description="描述"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/RiskGroup"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function update(RiskGroup $riskGroup, RiskGroupRequest $request)
    {
        $data = remove_null($request->all());
        if ($request->has('rules')) {
            $data['rules'] = remove_null($request->input('rules'));
        }

        $riskGroup->update($data);
        # 根据
        $response = (new RiskGroupService())->batchChangeUserStatusByRiskGroup($riskGroup, $this->user);
        if (is_array($response)) {
            (new UserService())->kickUsersOut($response);
        }

        return $this->response->item($riskGroup, new RiskGroupTransformer());
    }

    /**
     * @OA\Delete(
     *      path="/backstage/risk_groups/{risk_group}",
     *      operationId="backstage.risk_groups.delete",
     *      tags={"Backstage-风控分组"},
     *      summary="删除风控分组",
     *      @OA\Parameter(
     *         name="reward",
     *         in="path",
     *         description="风控分组id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(response=204,description="No Content"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function destroy(RiskGroup $riskGroup)
    {
        $exists = User::query()->where('risk_group_id', $riskGroup->id)->exists();
        if ($exists) {
            return $this->response->error('This risk group is using by some members, it is now allowed to delete!');
        }
        $riskGroup->delete();
        return $this->response->noContent();
    }


    /**
     * @OA\Get(
     *      path="/backstage/risk_groups/audits/{risk_group_id}",
     *      operationId="backstage.risk_groups.audits",
     *      tags={"Backstage-风控分组"},
     *      summary="风控组修改记录",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Audit"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function audit(RiskGroup $riskGroup)
    {
        $records = $riskGroup->audits;
        foreach ($records as $record) {
            $oldString = '';
            $newString = '';
            $old       = $record->old_values;
            $new       = $record->new_values;

            if (isset($old['rules']) || isset($new['rules'])) {
                $oleRules = isset($old['rules']) ? json_decode($old['rules']) : [];
                $newRules = isset($new['rules']) ? json_decode($new['rules']) : [];

                $oldString .= 'Rules: ' . implode(', ', transfer_array_show_value($oleRules, RiskGroup::$ruleLists)) . ';';
                $newString .= 'Rules: ' . implode(', ', transfer_array_show_value($newRules, RiskGroup::$ruleLists)) . ';';
            }
            if (isset($old['status']) || isset($new['status'])) {
                $oldStatus = isset($old['status']) ? transfer_show_value($old['status'], RiskGroup::$booleanStatusesDropList) : null;
                $newStatus = isset($new['status']) ? transfer_show_value($new['status'], RiskGroup::$booleanStatusesDropList) : null;
                $oldString .= 'Status: ' . $oldStatus . ';';
                $newString .= 'Status: ' . $newStatus . ';';
            }
            collect($old)->except(['rules', 'status'])->each(function ($value, $key) use (&$oldString) {
                $oldString .= ucfirst($key) . ': ' . $value . ';';
            });
            collect($new)->except(['rules', 'status'])->each(function ($value, $key) use (&$newString) {
                $newString .= ucfirst($key) . ': ' . $value . ';';
            });
            $record->old_value = $oldString;
            $record->new_value = $newString;
        }

        return $this->response->collection($records, new AuditTransformer());
    }
}
