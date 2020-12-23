<?php

namespace App\Http\Controllers\Api\Affiliate;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Backstage\TrackingStatisticRequest;
use App\Models\CreativeResource;
use App\Models\TrackingStatistic;
use App\Transformers\CreativeResourceTransformer;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class CreativeResourcesController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/affiliate/creative_resources",
     *      operationId="api.affiliate.creative_resources.index",
     *      tags={"Affiliate-代理"},
     *      summary="代理资源链接",
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[tracking_id]", in="query", description="tracking_id", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[group]", in="query", description="组别", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[size]", in="query", description="尺寸", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[type]", in="query", description="类型", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/CreativeResource"),
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
        $resources = QueryBuilder::for(CreativeResource::class)->allowedFilters(
            Filter::exact('type'),
            Filter::scope('currency'),
            Filter::exact('size'),
            Filter::exact('group')
        )
            ->paginate($request->per_page);

        return $this->response->paginator($resources, new CreativeResourceTransformer());
    }

    /**
     * @OA\Post(
     *      path="/affiliate/tracking_statistics",
     *      operationId="api.affiliate.tracking_statistics.store",
     *      tags={"Affiliate-代理"},
     *      summary="添加tracking",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="tracking_name", type="string", description="名称"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=204,description="no content"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function store(TrackingStatisticRequest $request)
    {
        $user              = $this->user();
        $data              = remove_null($request->all());
        $data['user_id']   = $user->id;
        $data['user_name'] = $user->name;

        TrackingStatistic::query()->create($data);

        return $this->response->noContent();
    }

    /**
     * @OA\Get(
     *      path="/affiliate/get_tracking_statistics",
     *      operationId="api.affiliate.get_tracking_statistics",
     *      tags={"Affiliate-代理"},
     *      summary="tracking_statistics下拉菜单",
     *      @OA\Response(response=204,description="no content"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function getTrackingStatistic()
    {
        $user = $this->user();
        $list = TrackingStatistic::query()
            ->where('user_id', $user->id)
            ->get()->toArray();
        $data = [];
        foreach ($list as $value) {
            $name = $value['tracking_name'];
            if ($user->affiliate_code == $value['tracking_name']) {
                $name = 'default';
            }
            $data[] = [
                'key'   => $value['id'],
                'value' => $name,
            ];
        }

        return $this->response->array($data);
    }
}
