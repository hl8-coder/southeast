<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AccessLogsRequest;
use App\Http\Controllers\ApiController;
use App\Models\TrackingStatisticLog;

class AccessLogsController extends ApiController
{
    /**
     * @OA\Post(
     *      path="/access_logs",
     *      operationId="api.access_logs.store",
     *      tags={"Api-访问记录"},
     *      summary="访问记录",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="affiliate_code", type="string", description="代理号"),
     *                  @OA\Property(property="url", type="string", description="来源地址"),
     *                  @OA\Property(property="tracking_id", type="integer", description="追踪链接ID"),
     *                  @OA\Property(property="ip", type="string", description="IP地址"),
     *                  required={"ip"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=204,description="no content"),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function log(AccessLogsRequest $request)
    {
        $data = remove_null($request->all());

        $data['ip'] = $request->getClientIp();

        TrackingStatisticLog::query()->create($data);

        return $this->response->noContent();
    }
}
