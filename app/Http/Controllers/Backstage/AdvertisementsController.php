<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\AdvertisementRequest;
use App\Models\Advertisement;
use App\Models\Image;
use App\Transformers\AdvertisementTransformer;
use Illuminate\Http\Request;

class AdvertisementsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/advertisements",
     *      operationId="backstage.advertisements.index",
     *      tags={"Backstage-资讯"},
     *      summary="广告列表",
     *      description="广告列表",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Advertisement"),
     *          ),
     *      ),
     *      security={
     *           {"bearer": {}}
     *       }
     *  )
     */
    public function index(Request $request)
    {
        $advertisements = Advertisement::query()->latest()->paginate($request->per_page);

        return $this->response->paginator($advertisements, new AdvertisementTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/advertisements",
     *      operationId="backstage.advertisements.store",
     *      tags={"Backstage-资讯"},
     *      summary="广告",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="currency", type="string", description="币别"),
     *                  @OA\Property(property="web_image_id", type="integer", description="web端图片id"),
     *                  @OA\Property(property="mobile_img_id", type="integer", description="移动端图片id"),
     *                  @OA\Property(property="login_img_id", type="integer", description="登录页图片id"),
     *                  @OA\Property(property="description", type="string", description="描述"),
     *                  @OA\Property(property="img_link_url", type="string", description="图片跳转地址"),
     *                  @OA\Property(property="alone_link_url", type="string", description="独立跳转地址"),
     *                  @OA\Property(property="target_type", type="integer", description="跳转方式"),
     *                  @OA\Property(property="show_type", type="integer", description="显示类型"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  @OA\Property(property="status", type="boolean", description="状态"),
     *                  required={"country_name", "web_image_id", "mobile_img_id", "login_img_id"},
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/Advertisement"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function store(AdvertisementRequest $request)
    {
        $data = remove_null($request->all());

        $data['web_img_path']       = Image::find($request->web_img_id)->path;
        $data['mobile_img_path']    = Image::find($request->mobile_img_id)->path;
        $data['login_img_path']     = Image::find($request->login_img_id)->path;

        $advertisement = Advertisement::query()->create($data);

        return $this->response->item(Advertisement::find($advertisement->id), new AdvertisementTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Put(
     *      path="/backstage/advertisements",
     *      operationId="backstage.advertisements.update",
     *      tags={"Backstage-资讯"},
     *      summary="更新广告",
     *      @OA\Parameter(
     *         name="advertisement",
     *         in="path",
     *         description="广告id",
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
     *                  @OA\Property(property="currency", type="string", description="币别"),
     *                  @OA\Property(property="web_image_id", type="integer", description="web端图片id"),
     *                  @OA\Property(property="mobile_img_id", type="integer", description="移动端图片id"),
     *                  @OA\Property(property="login_img_id", type="integer", description="登录页图片id"),
     *                  @OA\Property(property="description", type="string", description="描述"),
     *                  @OA\Property(property="img_link_url", type="string", description="图片跳转地址"),
     *                  @OA\Property(property="alone_link_url", type="string", description="独立跳转地址"),
     *                  @OA\Property(property="target_type", type="integer", description="跳转方式"),
     *                  @OA\Property(property="show_type", type="integer", description="显示类型"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  @OA\Property(property="status", type="boolean", description="状态"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/Advertisement"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function update(Advertisement $advertisement, AdvertisementRequest $request)
    {
        $data = remove_null($request->all());

        if ($request->has('web_image_id')) {
            $data['web_image_id'] = Image::find($request->web_image_id)->path;
        }

        if ($request->has('mobile_img_id')) {
            $data['mobile_img_path'] = Image::find($request->mobile_img_id)->path;
        }

        if ($request->has('login_img_id')) {
            $data['login_img_path'] = Image::find($request->login_img_id)->path;
        }

        $advertisement->update($data);

        return $this->response->item($advertisement, new AdvertisementTransformer());
    }

    /**
     * @OA\Delete(
     *      path="/backstage/advertisements/{advertisement}",
     *      operationId="backstage.advertisements.delete",
     *      tags={"Backstage-资讯"},
     *      summary="删除广告",
     *      @OA\Parameter(
     *         name="advertisement",
     *         in="path",
     *         description="广告id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(response=204,description="No Content"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function destroy(Advertisement $advertisement)
    {
        $advertisement->delete();

        return $this->response->noContent();
    }
}
