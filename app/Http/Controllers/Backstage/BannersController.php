<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\BannerRequest;
use App\Models\Banner;
use App\Models\Image;
use App\Transformers\BannerTransformer;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class BannersController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/banners",
     *      operationId="backstage.banners.index",
     *      tags={"Backstage-资讯"},
     *      summary="轮播图列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Banner"),
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

        $banners = QueryBuilder::for(Banner::class)
            ->allowedFilters([
                Filter::exact('currency'),
                Filter::exact('code'),
                Filter::exact('position'),
                Filter::exact('show_type'),
                Filter::exact('target_type'),
                Filter::exact('status'),
                Filter::scope('display_start_at'),
                Filter::scope('display_end_at'),
            ])
            ->orderByDesc('sort')
            ->paginate($request->per_page);

        return $this->response->paginator($banners, new BannerTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/banners",
     *      operationId="backstage.banners.store",
     *      tags={"Backstage-资讯"},
     *      summary="创建轮播图",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="code", type="string", description="代号"),
     *                  @OA\Property(property="currency", type="string", description="币别"),
     *                  @OA\Property(property="button_text", type="string", description="按钮显示内容"),
     *                  @OA\Property(property="languages", type="array", description="多语言", @OA\Items(
     *                     @OA\Property(property="language", type="string", description="语言"),
     *                     @OA\Property(property="title", type="string", description="标题"),
     *                     @OA\Property(property="content", type="string", description="内容"),
     *                     @OA\Property(property="description", type="string", description="描述文字"),
     *                  )),
     *                  @OA\Property(property="show_type", type="integer", description="显示类型"),
     *                  @OA\Property(property="position", type="integer", description="位置"),
     *                  @OA\Property(property="target_type", type="integer", description="跳转目标类型"),
     *                  @OA\Property(property="display_start_at", type="string", description="上架时间", format="date-time"),
     *                  @OA\Property(property="display_end_at", type="string", description="下架时间", format="date-time"),
     *                  @OA\Property(property="web_img_id", type="integer", description="PC端图片id"),
     *                  @OA\Property(property="mobile_img_id", type="integer", description="Mobile端图片id"),
     *                  @OA\Property(property="web_link_url", type="string", description="PC跳转地址"),
     *                  @OA\Property(property="mobile_link_url", type="string", description="移动端跳转地址"),
     *                  @OA\Property(property="status", type="boolean", description="状态"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  required={"currencies", "titles", "code", "display_start_at", "display_end_at", "show_type", "target_type", "web_img_id", "mobile_img_id"},
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/Banner"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function store(BannerRequest $request)
    {
        $data = remove_null($request->all());

        $data = $this->getImagePath($data);

        $data['admin_name'] = $this->user->name;

        $banner = Banner::query()->create($data);

        return $this->response->item($banner, new BannerTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Patch(
     *      path="/backstage/banners/{banner}",
     *      operationId="backstage.banners.update",
     *      tags={"Backstage-资讯"},
     *      summary="更新轮播图",
     *      @OA\Parameter(
     *          name="banner",
     *          in="path",
     *          description="Banner id",
     *          @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="button_text", type="string", description="按钮显示内容"),
     *                  @OA\Property(property="languages", type="array", description="多语言",@OA\Items(
     *                      @OA\Property(property="title", type="string", description="标题文字"),
     *                      @OA\Property(property="content", type="string", description="内容文字"),
     *                      @OA\Property(property="description", type="string", description="描述文字"),
     *                      @OA\Property(property="language", type="string", description="语言"),
     *                  ),description="多语言内容"),
     *                  @OA\Property(property="currency", type="string", description="币别"),
     *                  @OA\Property(property="show_type", type="integer", description="显示类型"),
     *                  @OA\Property(property="position", type="integer", description="位置"),
     *                  @OA\Property(property="target_type", type="integer", description="跳转目标类型"),
     *                  @OA\Property(property="display_start_at", type="string", description="上架时间", format="date-time"),
     *                  @OA\Property(property="display_end_at", type="string", description="下架时间", format="date-time"),
     *                  @OA\Property(property="web_img_id", type="integer", description="PC端图片id"),
     *                  @OA\Property(property="mobile_img_id", type="integer", description="Mobile端图片id"),
     *                  @OA\Property(property="web_link_url", type="string", description="PC跳转地址"),
     *                  @OA\Property(property="mobile_link_url", type="string", description="移动端跳转地址"),
     *                  @OA\Property(property="status", type="boolean", description="状态"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="更新成功",
     *          @OA\JsonContent(ref="#/components/schemas/Banner"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function update(Banner $banner, BannerRequest $request)
    {
        $data = remove_null($request->all());

        $data = $this->getImagePath($data);

        $data['admin_name'] = $this->user->name;

        $banner->update($data);

        return $this->response->item($banner, new BannerTransformer());
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

    /**
     * @OA\Delete(
     *      path="/backstage/banners/{banner}",
     *      operationId="backstage.banners.delete",
     *      tags={"Backstage-资讯"},
     *      summary="删除banner",
     *      @OA\Parameter(
     *         name="banner",
     *         in="path",
     *         description="banner id",
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
    public function destroy(Banner $banner)
    {
        $banner->delete();

        return $this->response->noContent();
    }
}
