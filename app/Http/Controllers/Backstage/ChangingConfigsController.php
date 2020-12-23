<?php

namespace App\Http\Controllers\Backstage;

use Illuminate\Http\Request;
use App\Models\ChangingConfig;
use App\Http\Controllers\BackstageController;
use App\Transformers\ChangingConfigTransformer;

class ChangingConfigsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/changing_configs",
     *      operationId="backstage.changing_configs.index",
     *      tags={"Backstage-配置"},
     *      summary="获取高平配置列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/ChangingConfig"),
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
        $data = ChangingConfig::query()->paginate($request->per_page);
        return $this->response->paginator($data, new ChangingConfigTransformer());
    }
}
