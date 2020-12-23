<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Language;
use App\Transformers\LanguageTransformer;
use Illuminate\Http\Request;

class LanguagesController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/languages",
     *      operationId="api.languages.index",
     *      tags={"Api-平台"},
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
        return $this->response->collection(Language::getAll()->where('status', true), new LanguageTransformer());
    }
}
