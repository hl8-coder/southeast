<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Vip;
use App\Transformers\VipTransformer;
use Illuminate\Http\Request;

class VipsController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/vips",
     *      operationId="api.vips.index",
     *      tags={"Api-平台"},
     *      summary="vip列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Vip"),
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
        $vips = Vip::getAll();

        return $this->response->collection($vips, new VipTransformer());
    }
}
