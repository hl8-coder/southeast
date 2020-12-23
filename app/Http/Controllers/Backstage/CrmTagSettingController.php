<?php

namespace App\Http\Controllers\Backstage;

use App\Models\Config;
use Illuminate\Http\Request;
use App\Models\ChangingConfig;
use App\Http\Controllers\BackstageController;
use App\Transformers\ChangingConfigTransformer;

class CrmTagSettingController extends BackstageController
{
    private $setting = ['crm_welcome', 'crm_non_deposit', 'crm_retention', 'crm_daily_retention', 'crm_resource'];
    private $configSetting = ['assign_crm_order_stop_ahead', 'assign_crm_order_start_delay', 'assign_crm_order_per_admin_hourly'];

    /**
     * @OA\Get(
     *      path="/backstage/crm_tag_setting",
     *      operationId="backstage.crm_tag_setting.index",
     *      tags={"Backstage-CRM"},
     *      summary="CRM Tag Setting 配置设定列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(type="array",@OA\Items(ref="#/components/schemas/ChangingConfig"),),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function index()
    {
        $setting = ChangingConfig::query()->whereIn('code', $this->setting)->get();
        $show    = Config::getAll()->whereIn('code', $this->configSetting)->toArray();

        return $this->response()->collection($setting, new ChangingConfigTransformer())->setMeta($show);
    }


    /**
     * @OA\Patch(
     *      path="/backstage/crm_tag_setting",
     *      operationId="backstage.crm_tag_setting.update",
     *      tags={"Backstage-CRM"},
     *      summary="CRM Tag Setting 配置设定列表",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="crm_welcome", type="boolean", description="自动分配 welcome 订单"),
     *                  @OA\Property(property="crm_non_deposit", type="boolean", description="自动分配 non deposit 订单"),
     *                  @OA\Property(property="crm_retention", type="boolean", description="自动分配 retention 订单"),
     *                  @OA\Property(property="crm_daily_retention", type="boolean", description="自动分配 daily retention 订单"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/ChangingConfig"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function update(Request $request)
    {
        $input = $request->only($this->setting);

        $update = array_map(function ($key, $value) {
            return ['code' => $key, 'value' => (bool)$value];
        }, array_keys($input), $input);

        $count = ChangingConfig::updateBatch($update);

        if ($count !== false) {
            $setting = ChangingConfig::query()->whereIn('code', $this->setting)->get();
            return $this->response()->collection($setting, new ChangingConfigTransformer());
        }
        $this->response()->error('Update CRM Setting Fail!', 422);

    }
}
