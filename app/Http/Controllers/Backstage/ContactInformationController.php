<?php

namespace App\Http\Controllers\Backstage;

use App\Models\Image;
use Illuminate\Http\Request;
use App\Models\ContactInformation;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Controllers\BackstageController;
use App\Transformers\ContactInformationTransformer;
use App\Http\Requests\Backstage\ContactInformationRequest;
use Spatie\QueryBuilder\Filter;

class ContactInformationController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/contact_information",
     *      operationId="backstage.contact_information.index",
     *      tags={"Backstage-联系我们"},
     *      summary="联系方式",
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/ContactInformation"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function index(Request $request)
    {
        $information = QueryBuilder::for(ContactInformation::class)
            ->allowedFilters([
                Filter::scope('currency'),
            ])
            ->paginate($request->per_page);

        return $this->response->paginator($information, new ContactInformationTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/contact_information",
     *      operationId="backstage.contact_information.store",
     *      tags={"Backstage-联系我们"},
     *      summary="添加联系方式",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="icon_id", type="integer", description="icon图片id"),
     *                  @OA\Property(property="api_url", type="string", description="备注"),
     *                  @OA\Property(property="is_enable", type="boolean", description="是否启用"),
     *                  @OA\Property(property="is_affiliate", type="boolean", description="是否是代理平台"),
     *                  @OA\Property(property="currencies", type="array", description="币别", @OA\Items(
     *                  )),
     *                  @OA\Property(property="languages", type="array", description="语言", @OA\Items(
     *                     @OA\Property(property="language", type="string", description="语言"),
     *                     @OA\Property(property="title", type="string", description="标题"),
     *                     @OA\Property(property="content", type="string", description="内容"),
     *                  )),
     *                  required={"icon_id", "currencies", "languages"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/ContactInformation"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function store(ContactInformationRequest $request)
    {
        $data = remove_null($request->all());

        $data = $this->getImagePath($data);

        $information = ContactInformation::query()->create($data);

        return $this->response->item($information->refresh(), new ContactInformationTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Patch(
     *      path="/backstage/contact_information/{information}",
     *      operationId="backstage.contact_information.update",
     *      tags={"Backstage-联系我们"},
     *      summary="修改联系方式",
     *      @OA\Parameter(
     *         name="information",
     *         in="path",
     *         description="联系方式id",
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
     *                  @OA\Property(property="icon_id", type="integer", description="icon图片id"),
     *                  @OA\Property(property="api_url", type="string", description="备注"),
     *                  @OA\Property(property="is_enable", type="boolean", description="是否启用"),
     *                  @OA\Property(property="is_affiliate", type="boolean", description="是否是代理平台"),
     *                  @OA\Property(property="currencies", type="array", description="币别", @OA\Items(
     *                  )),
     *                  @OA\Property(property="languages", type="array", description="语言", @OA\Items(
     *                     @OA\Property(property="language", type="string", description="语言"),
     *                     @OA\Property(property="title", type="string", description="标题"),
     *                     @OA\Property(property="content", type="string", description="内容"),
     *                  ))
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="更新成功",
     *          @OA\JsonContent(ref="#/components/schemas/ContactInformation"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function update(ContactInformationRequest $request, ContactInformation $information)
    {
        $data = remove_null($request->all());

        $data = $this->getImagePath($data);

        $information->update($data);

        return $this->response->item($information, new ContactInformationTransformer());
    }

    public function getImagePath($data)
    {
        if (!empty($data['icon_id'])) {
            $data['icon'] = Image::find($data['icon_id'])->path;
        }

        return $data;
    }
}
