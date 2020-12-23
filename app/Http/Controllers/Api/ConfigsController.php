<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Config;
use App\Transformers\ConfigTransformer;
use Illuminate\Http\Request;

class ConfigsController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/configs",
     *      operationId="api.configs.index",
     *      tags={"Api-平台"},
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
        $configs = Config::query()->where('is_front_show', true)->get();

        return $this->response->collection($configs, new ConfigTransformer());
    }
}
