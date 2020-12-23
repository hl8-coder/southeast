<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\ConfigRequest;
use App\Models\Config;
use App\Transformers\ConfigTransformer;
use Illuminate\Http\Request;

class ConfigsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/configs",
     *      operationId="backstage.configs.index",
     *      tags={"Backstage-配置"},
     *      summary="获取配置列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Config"),
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
        $configs = Config::query()->get();

        return $this->response->collection($configs, new ConfigTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/configs/{config}",
     *      operationId="backstage.configs.update",
     *      tags={"Backstage-配置"},
     *      summary="更新配置",
     *      @OA\Parameter(
     *         name="config",
     *         in="path",
     *         description="配置id",
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
     *                  @OA\Property(property="value", type="string", description="值"),
     *                  required={"value"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="更新成功",
     *          @OA\JsonContent(ref="#/components/schemas/Config"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function update(Config $config, ConfigRequest $request)
    {
        $config->update([
            'value' => $request->value,
        ]);

        return $this->response->item($config, new ConfigTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/get/operation_id",
     *      operationId="backstage.configs.get.operation_id",
     *      tags={"Backstage-配置"},
     *      summary="获取平台",
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function getOperationId()
    {
        return Config::findValue('operation_id');
    }
}
