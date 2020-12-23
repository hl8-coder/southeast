<?php


namespace App\Http\Controllers\Backstage;

use App\Exports\ExcelTemplateExport;
use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\UserRiskGroupRequest;
use App\Imports\UserRiskGroupImport;
use App\Models\ProfileRemark;
use App\Models\RiskGroup;
use App\Models\User;
use App\Services\RiskGroupService;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Facades\Excel;

class UserRiskGroupController extends BackstageController
{

    /**
     * @OA\Get(
     *      path="/backstage/user_risk_group/template",
     *      operationId="backstage.user_risk_group.template",
     *      tags={"Backstage-风控分组"},
     *      summary="Risk Group会员信息模版",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\MediaType(
     *              mediaType="application/vnd.ms-excel",
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function template()
    {
        return Excel::download(new ExcelTemplateExport([], ['member code']), 'user_risk_group.xlsx');
    }


    /**
     * @OA\Post(
     *      path="/backstage/user_risk_group",
     *      operationId="backstage.user_risk_group.store",
     *      tags={"Backstage-风控分组"},
     *      summary="通过表格批量修改用户风控组",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="excel", type="file", description="资源文件"),
     *                  @OA\Property(property="risk_group_id", type="string", description="风控组ID"),
     *                  required={"excel"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201,description="no content"),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function store(UserRiskGroupRequest $request)
    {
        try {
            $data        = Excel::toArray(new UserRiskGroupImport(), $request->file('excel'));
        }catch (\Exception $e){
            return $this->response->error('File error!', 422);
        }
        $riskGroupId = $request->risk_group_id;
        $names       = Arr::flatten($data);
        $names       = array_unique($names);

        $users = User::query()->whereIn('name', $names)
            ->where('is_agent', User::AGENT_0)
            ->get(['id', 'name', 'risk_group_id']);

        if (count($names) !== $users->count()) {
            $nameNon = array_diff($names, $users->pluck('name')->toArray());
            $this->response()->error('Invalid Member Code : ' . implode(', ', $nameNon), 422);
        }

        $userIds = $users->pluck('id');
        $admin   = $this->user;

        if ($userIds) {
            User::query()->whereIn('id', $userIds)->update(['risk_group_id' => $riskGroupId]);
            # 批量处理，防止该分组用户数量过大导致单次 IO 数据量过大
            collect($userIds)->chunk(50, function ($ids) use ($admin) {
                foreach ($ids as $id) {
                    $remark[] = [
                        'user_id'    => $id,
                        'category'   => ProfileRemark::CATEGORY_CHANGE,
                        'remark'     => 'admin batch change user risk group',
                        'admin_name' => $admin->name,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                ProfileRemark::query()->insert($remark);
            });

            $riskGroupService = new RiskGroupService();
            $riskGroup        = RiskGroup::query()->find($request->risk_group_id);
            $riskGroupService->batchChangeUserStatusByRiskGroup($riskGroup, $this->user);
        }
        return $this->response->noContent();
    }
}
