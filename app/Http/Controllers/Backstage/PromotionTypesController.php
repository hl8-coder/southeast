<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\PromotionTypeRequest;
use App\Models\Image;
use App\Models\PromotionType;
use App\Transformers\PromotionTypeTransformer;
use Illuminate\Http\Request;

class PromotionTypesController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/promotion_types",
     *      operationId="backstage.promotion_types.index",
     *      tags={"Backstage-优惠"},
     *      summary="优惠类型列表",
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/PromotionType"),
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
        $promotionTypes = PromotionType::getAll()->sortByDesc('sort');

        if (!empty($request->filter['currency'])) {
            $currency = $request->filter['currency'];
            $promotionTypes = $promotionTypes->filter(function($value) use ($currency) {
                return in_array($currency, $value->currencies);
            });
        }

        return $this->response->collection($promotionTypes, new PromotionTypeTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/promotion_types",
     *      operationId="backstage.promotion_types.store",
     *      tags={"Backstage-优惠"},
     *      summary="创建优惠类型",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="code", type="string", description="辨识码"),
     *                  @OA\Property(property="web_img_id", type="integer", description="PC端图片"),
     *                  @OA\Property(property="mobile_img_id", type="integer", description="Mobile端图片"),
     *                  @OA\Property(property="status", type="integer", description="状态"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  @OA\Property(property="currencies", type="array", description="币别", @OA\Items(
     *                  )),
     *                  @OA\Property(property="languages", type="array", description="语言", @OA\Items(
     *                     @OA\Property(property="language", type="string", description="语言"),
     *                     @OA\Property(property="title", type="string", description="前端显示标题"),
     *                     @OA\Property(property="description", type="string", description="描述"),
     *                  )),
     *                  required={"code", "currencies", "web_img_id", "mobile_img_id"},
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/PromotionType"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function store(PromotionTypeRequest $request)
    {
        $data = remove_null($request->all());
        $data = $this->getImagePath($data);
        $data['admin_name'] = $this->user->name;

        $promotionTypes = PromotionType::query()->create($data);

        return $this->response->item($promotionTypes, new PromotionTypeTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Patch(
     *      path="/backstage/promotion_types/{promotion_type}",
     *      operationId="backstage.promotion_types.update",
     *      tags={"Backstage-优惠"},
     *      summary="更新优惠类型",
     *      @OA\Parameter(
     *         name="promotion_type",
     *         in="path",
     *         description="优惠类型id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="web_img_id", type="integer", description="PC端图片id"),
     *                  @OA\Property(property="mobile_img_id", type="integer", description="Mobile端图片id"),
     *                  @OA\Property(property="status", type="integer", description="状态"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  @OA\Property(property="currencies", type="array", description="币别", @OA\Items(
     *                  )),
     *                  @OA\Property(property="languages", type="array", description="语言", @OA\Items(
     *                     @OA\Property(property="language", type="string", description="语言"),
     *                     @OA\Property(property="title", type="string", description="前端显示标题"),
     *                     @OA\Property(property="description", type="string", description="描述"),
     *                  )),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="更新成功",
     *          @OA\JsonContent(ref="#/components/schemas/PromotionType"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function update(PromotionType $promotionType, PromotionTypeRequest $request)
    {
        $data = remove_null($request->all());

        $data = $this->getImagePath($data);

        $data['admin_name'] = $this->user->name;

        $promotionType->update($data);

        return $this->response->item($promotionType, new PromotionTypeTransformer());
    }

    public function getImagePath($data)
    {
        if (!empty($data['web_img_id'])) {
            $data['web_img_path'] = Image::find($data['web_img_id'])->path;
        }

        if (!empty($data['mobile_img_id'])) {
            $data['mobile_img_path'] = Image::find($data['mobile_img_id'])->path;
        }

        return $data;
    }
}
