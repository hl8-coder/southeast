<?php

namespace App\Http\Controllers\Api;

use App\Models\Advertisement;
use App\Models\Platform;
use App\Transformers\AdvertisementTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class AdvertisementsController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/advertisements",
     *      operationId="api.advertisements.index",
     *      tags={"Api-资讯"},
     *      summary="广告列表",
     *      @OA\Parameter(
     *         name="currency",
     *         in="header",
     *         description="币别",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Advertisement"),
     *          ),
     *      )
     *  )
     */
    public function index(Request $request)
    {
        $showType = $request->input('show_type', Advertisement::SHOW_TYPE_HEADER);
        $showTypes = [
            Advertisement::SHOW_TYPE_ALL
        ];
        if ($showType != Advertisement::SHOW_TYPE_ALL) {
            $showTypes[] = $showType;
        }

        $advertisements = Advertisement::query()->where('currency', $request->header('currency'))
            ->whereIn('show_type', $showTypes)
            ->enable()
            ->sortByDesc()
            ->get();

        return $this->response->collection($advertisements, new AdvertisementTransformer());
    }
}
