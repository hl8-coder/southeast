<?php

namespace App\Http\Controllers\Backstage;

use App\Models\Image;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\Filter;
use App\Models\GamePlatformProduct;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Controllers\BackstageController;
use App\Transformers\GamePlatformProductTransformer;
use App\Http\Requests\Backstage\GamePlatformProductRequest;

class GamePlatformProductsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/game_platform_products",
     *      operationId="backstage.game_platform_products.index",
     *      tags={"Backstage-游戏"},
     *      summary="获取游戏产品列表",
     *      @OA\Parameter(name="filter[platform_code]", in="query", description="游戏平台code", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[code]", in="query", description="唯一码", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[type]", in="query", description="游戏类型", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/GamePlatformProduct"),
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
        $products = QueryBuilder::for(GamePlatformProduct::class)
                    ->allowedFilters(
                        Filter::exact('platform_code'),
                        Filter::exact('status'),
                        Filter::exact('type'),
                        'code'
                    )
                    ->sortByDesc()
                    ->paginate($request->per_page);

        return $this->response->paginator($products, new GamePlatformProductTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/game_platform_products/{game_platform_product}?include=gamePlatform",
     *      operationId="backstage.game_platform_products.store",
     *      tags={"Backstage-游戏"},
     *      summary="更新游戏产品",
     *      @OA\Parameter(
     *         name="game_platform_product",
     *         in="path",
     *         description="游戏产品id",
     *         required=true,
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
     *                  @OA\Property(property="devices", type="array", description="装置", @OA\Items()),
     *                  @OA\Property(property="is_close_bonus", type="boolean", description="是否可用于关闭红利"),
     *                  @OA\Property(property="is_close_cash_back", type="boolean", description="是否可用于关闭赎返"),
     *                  @OA\Property(property="is_close_adjustment", type="boolean", description="是否可用于关闭调整"),
     *                  @OA\Property(property="is_calculate_reward", type="boolean", description="是否可用于计算积分"),
     *                  @OA\Property(property="is_calculate_cash_back", type="boolean", description="是否可用于计算赎返"),
     *                  @OA\Property(property="is_calculate_rebate", type="boolean", description="是否可用于计算返点"),
     *                  @OA\Property(property="is_can_try", type="boolean", description="是否可以试玩"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  @OA\Property(property="status", type="boolean", description="状态"),
     *                  @OA\Property(property="currencies", type="array", description="币别", @OA\Items(
     *                  )),
     *                  @OA\Property(property="languags", type="array", description="语言", @OA\Items(
     *                     @OA\Property(property="language", type="string", description="语言"),
     *                     @OA\Property(property="name", type="string", description="名称"),
     *                     @OA\Property(property="description", type="string", description="描述"),
     *                     @OA\Property(property="content", type="string", description="内容"),
     *                     @OA\Property(property="one_web_img_id", type="string", description="PC端图片1"),
     *                     @OA\Property(property="two_web_img_id", type="string", description="PC端图片2"),
     *                     @OA\Property(property="mobile_img_id", type="string", description="手机端图片链接"),
     *                  )),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/GamePlatformProduct"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function update(GamePlatformProduct $gamePlatformProduct, GamePlatformProductRequest $request)
    {
        $data = remove_null($request->except(['platform_code', 'code', 'type']));

        $data['languages'] = $this->getUpdateImgPath($data['languages'], $gamePlatformProduct->languages);

        $gamePlatformProduct->update($data);

        return $this->response->item($gamePlatformProduct, new GamePlatformProductTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/game_platform_products/relation",
     *      operationId="backstage.game_platform_products.relation",
     *      tags={"Backstage-游戏"},
     *      summary="获取游戏产品关联列表",
     *      @OA\Parameter(name="platform_code", in="query", description="游戏平台code", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function relation(Request $request)
    {
        $products = GamePlatformProduct::getAll();

        if (!empty($request->platform_code)) {
            $products = $products->where('platform_code', $request->platform_code);
        }

        $products = $products->pluck('code', 'code')->toArray();

        $data = [];
        $data['product'] = transform_list($products);

        return $this->response->array($data);
    }

    protected function getImgPath($languages)
    {
        $imgKeys = array_keys(GamePlatformProduct::$imgFields);
        foreach ($languages as &$language) {
            foreach($language as $k => $img) {
                if (in_array($k, GamePlatformProduct::$languageRequestFields)) {
                    if (in_array($k, $imgKeys)) {
                        $language[GamePlatformProduct::$imgFields[$k]] = Image::find($img)->path;
                        unset($language[$k]);
                    }
                } else {
                    unset($language[$k]);
                }
            }
        }

        return $languages;
    }

    protected function getUpdateImgPath($languages, $oldLanguages)
    {
        $imgKeys = array_keys(GamePlatformProduct::$imgFields);
        $imgValues = array_values(GamePlatformProduct::$imgFields);
        foreach ($languages as $key=>&$language) {
            foreach($language as $k => $img) {

                if (in_array($k, $imgValues)) {
                    $language[$k] = $oldLanguages[$key][$k];
                }

                if (in_array($k, $imgKeys)) {
                    if (!empty($img)) {
                        $language[GamePlatformProduct::$imgFields[$k]] = Image::find($img)->path;
                    }
                    unset($language[$k]);
                }
            }
        }

        return $languages;
    }
}
