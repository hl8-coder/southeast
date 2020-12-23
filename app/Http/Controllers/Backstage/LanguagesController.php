<?php

namespace App\Http\Controllers\Backstage;

use App\Models\Language;
use App\Transformers\LanguageTransformer;
use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\LanguageRequest;

class LanguagesController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/languages",
     *      operationId="backstage.languages.index",
     *      tags={"Backstage-平台"},
     *      summary="语言列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Language"),
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
        return $this->response->collection(Language::getAll(), new LanguageTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/languages",
     *      operationId="backstage.languages.store",
     *      tags={"Backstage-平台"},
     *      summary="添加语言",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="name", type="string", description="名称"),
     *                  @OA\Property(property="code", type="string", description="代码"),
     *                  @OA\Property(property="status", type="integer", description="状态"),
     *                  required={"name", "code"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/Language"),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function store(LanguageRequest $request)
    {
        $data = remove_null($request->all());
        $language = Language::query()->create($data);

        return $this->response->item($language->refresh(), new LanguageTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Patch(
     *      path="/backstage/languages/{language}",
     *      operationId="backstage.languages.update",
     *      tags={"Backstage-平台"},
     *      summary="更新语言",
     *      @OA\Parameter(
     *         name="language",
     *         in="path",
     *         description="语言id",
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
     *                  @OA\Property(property="name", type="string", description="名称"),
     *                  @OA\Property(property="code", type="string", description="代码"),
     *                  @OA\Property(property="status", type="integer", description="状态"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/Language"),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function update(Language $language, LanguageRequest $request)
    {
        $data = remove_null($request->all());
        $language->update($data);
        return $this->response->item($language, new LanguageTransformer());
    }

    /**
     * @OA\Delete(
     *      path="/backstage/languages/{language}",
     *      operationId="backstage.languages.delete",
     *      tags={"Backstage-平台"},
     *      summary="删除语言",
     *      @OA\Parameter(
     *         name="language",
     *         in="path",
     *         description="语言id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(response=204,description="No Content"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function destroy(Language $language)
    {
        $language->delete();

        return $this->response->noContent();
    }
}
