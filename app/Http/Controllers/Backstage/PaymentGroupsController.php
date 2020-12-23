<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\PaymentGroupRequest;
use App\Models\Admin;
use App\Models\PaymentGroup;
use App\Models\RiskGroup;
use App\Transformers\AuditTransformer;
use App\Transformers\PaymentGroupTransformer;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class PaymentGroupsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/payment_groups",
     *      operationId="backstage.payment_groups.index",
     *      tags={"Backstage-支付组别"},
     *      summary="支付组别列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/PaymentGroup"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function index(PaymentGroupRequest $request)
    {
        $paymentGroups = QueryBuilder::for(PaymentGroup::class)
            ->allowedFilters(
                Filter::exact('currency'),
                'account_code'
            )
            ->with('presetRiskGroup')
            ->orderBy('id', 'desc')
            ->paginate($request->per_page);
        return $this->response->paginator($paymentGroups, new PaymentGroupTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/payment_groups",
     *      operationId="backstage.payment_groups.store",
     *      tags={"Backstage-支付组别"},
     *      summary="添加支付组别",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="name", type="string", description="分组名称"),
     *                  @OA\Property(property="currency", type="string", description="币别"),
     *                  @OA\Property(property="account_code", type="array", description="账号代号",@OA\Items()),
     *                  @OA\Property(property="preset_risk_group_id", type="integer", description="预设风控组别id"),
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *                  required={"name", "remark"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/PaymentGroup"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function store(PaymentGroupRequest $request)
    {
        $data                    = remove_null($request->all());
        $data['last_save_admin'] = $this->user->name;
        $data['last_save_at']    = now();

        $group = PaymentGroup::query()->create($data);

        return $this->response->item(PaymentGroup::findByCache($group->id), new PaymentGroupTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Patch(
     *      path="/backstage/payment_groups/{payment_group}",
     *      operationId="backstage.payment_groups.update",
     *      tags={"Backstage-支付组别"},
     *      summary="更新支付组别",
     *      @OA\Parameter(
     *         name="payment_group",
     *         in="path",
     *         description="支付组别id",
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
     *                  @OA\Property(property="currency", type="string", description="币别"),
     *                  @OA\Property(property="account_code", type="array", description="账号代号",@OA\Items()),
     *                  @OA\Property(property="preset_risk_group_id", type="integer", description="预设风控组别id"),
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/PaymentGroup"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function update(PaymentGroup $paymentGroup, PaymentGroupRequest $request)
    {
        $data = remove_null($request->all());
        # viet-192 不允许修改 name
        $data = collect($data)->except(['name'])->toArray();

        $data['last_save_admin'] = $this->user->name;
        $data['last_save_at']    = now();

        $paymentGroup->update($data);

        return $this->response->item($paymentGroup, new PaymentGroupTransformer());
    }


    /**
     * @OA\Get(
     *      path="/backstage/payment_groups/audit/{payment_gorup}",
     *      operationId="backstage.payment_groups.audit",
     *      tags={"Backstage-支付组别"},
     *      summary="payment group 修改历史",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Audit"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function audit(PaymentGroup $paymentGroup, PaymentGroupRequest $request)
    {
        $histories = $paymentGroup->audits()->orderBy('id', 'desc')->paginate($request->per_page);
        foreach ($histories as $history) {
            $oldValues = $history->old_values;
            $newValues = $history->new_values;
            $newOutput = '';
            $olOutput  = '';

            foreach ($oldValues as $key => $oldValue) {
                switch ($key){
                    case 'name':
                        $olOutput .= 'name:' . $oldValue . ';';
                        break;
                    case 'currency':
                        $olOutput .= 'currency:' . $oldValue . ';';
                        break;
                    case 'account_code':
                        if (!empty(json_decode($oldValue))){
                            $olOutput .= 'account_code:' . implode(',', json_decode($oldValue)) . ';';
                        }
                        break;
                    case 'remark':
                        $olOutput .= 'remark:' . $oldValue . ';';
                        break;
                    case 'preset_risk_group_id':
                        $riskGroup = RiskGroup::query()->find($oldValue);
                        if ($riskGroup){
                            $olOutput .= 'risk group:' . $riskGroup->name . ';';
                        }else{
                            $value = $oldValue ?? null;
                            $olOutput .= 'risk group id:' . $value . ';';
                        }
                        break;
                    case 'status':
                        $olOutput .= 'status:' . transfer_show_value($oldValue, PaymentGroup::$statusList) . ';';
                        break;
                    default:
                        $olOutput .= $key . ':' . $oldValue . ';';
                        break;
                }
            }

            foreach ($newValues as $key => $newValue) {
                switch ($key){
                    case 'name':
                        $newOutput .= 'name:' . $newValue . ';';
                        break;
                    case 'currency':
                        $newOutput .= 'currency:' . $newValue . ';';
                        break;
                    case 'account_code':
                        if (!empty(json_decode($newValue))){
                            $newOutput .= 'account_code:' . implode(',', json_decode($newValue)) . ';';
                        }
                        break;
                    case 'remark':
                        $newOutput .= 'remark:' . $newValue . ';';
                        break;
                    case 'preset_risk_group_id':
                        $riskGroup = RiskGroup::query()->find($newValue);
                        if ($riskGroup){
                            $newOutput .= 'risk group:' . $riskGroup->name . ';';
                        }else{
                            $value = $newValue ?? null;
                            $newOutput .= 'risk group id:' . $value . ';';
                        }
                        break;
                    case 'status':
                        $newOutput .= 'status:' . transfer_show_value($newValue, PaymentGroup::$statusList) . ';';
                        break;
                    default:
                        $newOutput .= $key . ':' . $newValue . ';';
                        break;
                }
            }
            $history->old_value = $olOutput;
            $history->new_value = $newOutput;
        }

        return $this->response->paginator($histories, new AuditTransformer());
    }
}
