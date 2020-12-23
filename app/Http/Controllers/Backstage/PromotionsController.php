<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\PromotionRequest;
use App\Models\Image;
use App\Models\Promotion;
use App\Transformers\PromotionTransformer;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class PromotionsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/promotions",
     *      operationId="backstage.promotions.index",
     *      tags={"Backstage-优惠"},
     *      summary="优惠列表",
     *      @OA\Parameter(name="is_agent", in="query", description="是否是代理", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[promotion_type_code]", in="query", description="优惠类型code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[code]", in="query", description="优惠code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[backstage_title]", in="query", description="后台title", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[related_type]", in="query", description="关联类型", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[is_can_claim]", in="query", description="是否可以报名", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[display_start_at]", in="query", description="展示开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[display_end_at]", in="query", description="展示结束时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Promotion"),
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
        $isAgent = $request->is_agent ?? false;

        $promotions = QueryBuilder::for(Promotion::class)
            ->allowedFilters([
                Filter::exact('promotion_type_code'),
                Filter::exact('code'),
                'backstage_title',
                Filter::exact('related_type'),
                Filter::exact('is_can_claim'),
                Filter::exact('status'),
                Filter::scope('display_start_at'),
                Filter::scope('display_end_at'),
                Filter::scope('currency'),
            ])
            ->where('is_agent', $isAgent)
            ->orderByDesc('sort')
            ->paginate($request->per_page);

        return $this->response->paginator($promotions, new PromotionTransformer('backstage_index'));
    }

    /**
     * @OA\Post(
     *      path="/backstage/promotions",
     *      operationId="backstage.promotions.store",
     *      tags={"Backstage-优惠"},
     *      summary="创建优惠",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="show_types", type="array", description="显示类型code", @OA\Items(
     *                  )),
     *                  @OA\Property(property="promotion_type_code", type="string", description="优惠类型code"),
     *                  @OA\Property(property="code", type="string", description="优惠辨识码"),
     *                  @OA\Property(property="codes", type="string", description="关联code"),
     *                  @OA\Property(property="backstage_title", type="string", description="后台显示标题"),
     *                  @OA\Property(property="display_start_at", type="string", description="上架时间", format="date-time"),
     *                  @OA\Property(property="display_end_at", type="string", description="下架时间", format="date-time"),
     *                  @OA\Property(property="is_verified", type="integer", description="是否验证"),
     *                  @OA\Property(property="related_type", type="integer", description="关联类型"),
     *                  @OA\Property(property="web_img_id", type="integer", description="PC端图片"),
     *                  @OA\Property(property="web_content_img_id", type="integer", description="PC端内容图片"),
     *                  @OA\Property(property="mobile_img_id", type="integer", description="Mobile端图片"),
     *                  @OA\Property(property="mobile_content_img_id", type="integer", description="Mobile端内容图片"),
     *                  @OA\Property(property="status", type="integer", description="状态"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  @OA\Property(property="is_agent", type="boolean", description="是否是代理"),
     *                  @OA\Property(property="is_can_claim", type="boolean", description="是否需要领取"),
     *                  @OA\Property(property="currencies", type="array", description="币别", @OA\Items(
     *                  )),
     *                  @OA\Property(property="languages", type="array", description="语言", @OA\Items(
     *                     @OA\Property(property="language", type="string", description="语言"),
     *                     @OA\Property(property="title", type="string", description="前端显示标题"),
     *                     @OA\Property(property="description", type="string", description="描述"),
     *                     @OA\Property(property="content", type="string", description="内容"),
     *                  )),
     *                  required={"promotion_type_code", "currencies", "backstage_title", "display_start_at", "display_end_at", "web_img_id", "mobile_img_id"},
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/Promotion"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function store(PromotionRequest $request)
    {
        $data = remove_null($request->all());

        if (isset($data['codes'])) {
            $data['codes'] = $this->formatCodes($data['codes']);
        }
        $data = $this->getImagePath($data);

        $data['admin_name'] = $this->user->name;

        $promotion = Promotion::query()->create($data);

        return $this->response->item($promotion, new PromotionTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Patch(
     *      path="/backstage/promotions/{promotion}",
     *      operationId="backstage.promotions.update",
     *      tags={"Backstage-优惠"},
     *      summary="更新优惠",
     *      @OA\Parameter(
     *          name="promotion",
     *          in="path",
     *          description="优惠id",
     *          @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                   @OA\Property(property="show_types", type="array", description="显示类型code", @OA\Items(
     *                  )),
     *                  @OA\Property(property="code", type="string", description="优惠code"),
     *                  @OA\Property(property="backstage_title", type="string", description="标题"),
     *                  @OA\Property(property="related_type", type="integer", description="关联类型"),
     *                  @OA\Property(property="codes", type="array", description="关联code", @OA\Items()),
     *                  @OA\Property(property="display_start_at", type="string", description="上架时间", format="date-time"),
     *                  @OA\Property(property="display_end_at", type="string", description="下架时间", format="date-time"),
     *                  @OA\Property(property="is_verified", type="integer", description="是否验证"),
     *                  @OA\Property(property="web_img_id", type="integer", description="PC端图片"),
     *                  @OA\Property(property="web_content_img_id", type="integer", description="PC端内容图片"),
     *                  @OA\Property(property="mobile_img_id", type="integer", description="Mobile端图片"),
     *                  @OA\Property(property="mobile_content_img_id", type="integer", description="Mobile端内容图片"),
     *                  @OA\Property(property="status", type="integer", description="状态"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  @OA\Property(property="is_agent", type="boolean", description="是否是代理"),
     *                  @OA\Property(property="is_can_claim", type="boolean", description="是否需要领取"),
     *                  @OA\Property(property="currencies", type="array", description="币别", @OA\Items(
     *                  )),
     *                  @OA\Property(property="languages", type="array", description="币别", @OA\Items(
     *                     @OA\Property(property="language", type="string", description="语言"),
     *                     @OA\Property(property="title", type="string", description="前端显示标题"),
     *                     @OA\Property(property="description", type="string", description="描述"),
     *                     @OA\Property(property="content", type="string", description="内容"),
     *                     @OA\Property(property="mobile_image_id", type="integer", description="图片ID"),
     *                  )),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="更新成功",
     *          @OA\JsonContent(ref="#/components/schemas/Promotion"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function update(Promotion $promotion, PromotionRequest $request)
    {
        $data = remove_null($request->all());
        $data = collect($data)->map(function ($value) {
            return !is_null($value) ? $value : '';
        })->toArray();

        if (isset($data['codes'])) {
            $data['codes'] = $this->formatCodes($data['codes']);
        }

        $data['related_type'] = $request->input('related_type', null);

        $data = $this->getImagePath($data);

        $data['admin_name'] = $this->user->name;

        $promotion->update($data);

        return $this->response->item($promotion, new PromotionTransformer());
    }

    public function getImagePath($data)
    {
        if (!empty($data['web_img_id'])) {
            $data['web_img_path'] = Image::find($data['web_img_id'])->path;
        }

        if (!empty($data['web_content_img_id'])) {
            $data['web_content_img_path'] = Image::find($data['web_content_img_id'])->path;
        }

        if (!empty($data['mobile_img_id'])) {
            $data['mobile_img_path'] = Image::find($data['mobile_img_id'])->path;
        }
        if (!empty($data['mobile_content_img_id'])) {
            $data['mobile_content_img_path'] = Image::find($data['mobile_content_img_id'])->path;
        }

        if (!empty($data['languages'])){
            foreach ($data['languages'] as &$languageContent){
                if (isset($languageContent['mobile_image_id'])){
                    $languageContent['mobile_image'] = $this->getImagePathByImageId($languageContent['mobile_image_id']);
                }
                unset($languageContent['mobile_image_id'], $languageContent['mobile_language_image']);
            }
        }

        return $data;
    }

    public function formatCodes($codes)
    {
        $codes = explode(',', str_replace('，', ',', $codes));
        $codes = array_map(function ($value) {
            return trim($value);
        }, $codes);
        return $codes;
    }

    /**
     * @OA\Delete(
     *      path="/backstage/promotions/{promotion}",
     *      operationId="backstage.promotions.delete",
     *      tags={"Backstage-优惠"},
     *      summary="删除优惠",
     *      @OA\Parameter(
     *         name="promotion",
     *         in="path",
     *         description="promotion id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(response=204,description="No Content"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function destroy(Promotion $promotion)
    {
        $promotion->code = $promotion->code . '_delete_' . now();

        $promotion->save();

        $promotion->delete();

        return $this->response->noContent();
    }


    /**
     * @OA\Post(
     *      path="/backstage/promotions/{promotion}",
     *      operationId="backstage.promotions.copy",
     *      tags={"Backstage-优惠"},
     *      summary="克隆优惠",
     *      @OA\Parameter(
     *          name="promotion",
     *          in="path",
     *          description="优惠id",
     *          @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/Promotion"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function copy(Promotion $promotion)
    {
        $new               = $promotion->replicate(['admin_name', 'status'])->toArray();
        $new['admin_name'] = $this->user->name;
        $new['status']     = false;
        $new['code']       = $new['code'] . '_COPY';
        try {
            $newPromotion      = app(Promotion::class)->create($new);
        }catch (\Exception $e){
            $this->response()->error('Copy Promotion Failed!', 422);
        }

        return $this->response()->item($newPromotion, new PromotionTransformer());
    }
}
