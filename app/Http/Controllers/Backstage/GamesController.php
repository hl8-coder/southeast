<?php

namespace App\Http\Controllers\Backstage;

use App\Models\Game;
use App\Models\Image;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\Filter;
use App\Models\GamePlatformProduct;
use App\Models\RiskGroup;
use Spatie\QueryBuilder\QueryBuilder;
use App\Transformers\GameTransformer;
use App\Transformers\AuditTransformer;
use App\Http\Requests\Backstage\GameRequest;
use App\Http\Controllers\BackstageController;

class GamesController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/Games",
     *      operationId="backstage.Games.index",
     *      tags={"Backstage-游戏"},
     *      summary="获取游戏列表",
     *      @OA\Parameter(name="filter[platform_code]", in="query", description="游戏平台code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[product_code]", in="query", description="游戏产品code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[code]", in="query", description="唯一码", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[type]", in="query", description="类型", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Game"),
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
        $games = QueryBuilder::for(Game::class)
            ->with('product')
            ->allowedFilters(
                Filter::exact('platform_code'),
                Filter::exact('product_code'),
                Filter::exact('status'),
                Filter::exact('type'),
                'code'
            )
            ->sortByDesc()
            ->paginate($request->per_page);

        return $this->response->paginator($games, new GameTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/Games",
     *      operationId="backstage.Games.store",
     *      tags={"Backstage-游戏"},
     *      summary="添加游戏",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="platform_code", type="string", description="平台code"),
     *                  @OA\Property(property="product_code", type="string", description="产品code"),
     *                  @OA\Property(property="code", type="string", description="辨识码"),
     *                  @OA\Property(property="devices", type="array", description="装置", @OA\Items()),
     *                  @OA\Property(property="is_hot", type="boolean", description="是否热门"),
     *                  @OA\Property(property="is_new", type="boolean", description="是否最新"),
     *                  @OA\Property(property="is_iframe", type="boolean", description="是否是 iframe 打开游戏"),
     *                  @OA\Property(property="is_mobile_iframe", type="boolean", description="移动端是否是 iframe 打开游戏"),
     *                  @OA\Property(property="is_using_cookie", type="boolean", description="if the game uses cookie"),
     *                  @OA\Property(property="is_effective_bet", type="boolean", description="是否计算有效投注"),
     *                  @OA\Property(property="is_close_bonus", type="boolean", description="是否可用于关闭红利"),
     *                  @OA\Property(property="is_close_cash_back", type="boolean", description="是否可用于关闭赎返"),
     *                  @OA\Property(property="is_close_adjustment", type="boolean", description="是否可用于关闭调整"),
     *                  @OA\Property(property="is_calculate_reward", type="boolean", description="是否可用于计算积分"),
     *                  @OA\Property(property="is_calculate_cash_back", type="boolean", description="是否可用于计算赎返"),
     *                  @OA\Property(property="is_calculate_rebate", type="boolean", description="是否可用于计算返点"),
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  @OA\Property(property="status", type="boolean", description="状态"),
     *                  @OA\Property(property="is_soon", type="boolean", description="是否即将发布"),
     *                  @OA\Property(property="currencies", type="array", description="币别", @OA\Items(
     *                  )),
     *                  @OA\Property(property="languages", type="array", description="语言", @OA\Items(
     *                     @OA\Property(property="language", type="string", description="语言"),
     *                     @OA\Property(property="name", type="string", description="名称"),
     *                     @OA\Property(property="description", type="string", description="描述"),
     *                     @OA\Property(property="content", type="string", description="内容"),
     *                     @OA\Property(property="web_img_id", type="integer", description="Pc端图片id"),
     *                     @OA\Property(property="web_menu_img_id", type="integer", description="Pc Menu 端图片id"),
     *                     @OA\Property(property="small_img_path_id", type="integer", description="游戏小图标id"),
     *                     @OA\Property(property="droplist_img_path_id", type="integer", description="下拉游戏图片id"),
     *                     @OA\Property(property="mobile_img_id", type="integer", description="Mobile端图片id"),
     *                     @OA\Property(property="mobile_img_2_id", type="integer", description="Mobile端图片2id"),
     *                  )),
     *                  required={"platform_code", "product_code", "type", "code", "devices", "currencies"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/Game"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function store(GameRequest $request)
    {
        $data = remove_null($request->all());

        # 游戏类型
        $product      = GamePlatformProduct::findByCodeFromCache($data['product_code']);
        $data['type'] = $product->type;

        $data['languages'] = $this->getImgPath($data['languages']);

        $game = Game::query()->create($data);

        return $this->response->item($game->refresh(), new GameTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Patch(
     *      path="/backstage/Games/{game}",
     *      operationId="backstage.Games.update",
     *      tags={"Backstage-游戏"},
     *      summary="更新游戏",
     *      @OA\Parameter(
     *         name="game",
     *         in="path",
     *         description="游戏id",
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
     *                  @OA\Property(property="is_hot", type="boolean", description="是否热门"),
     *                  @OA\Property(property="is_new", type="boolean", description="是否最新"),
     *                  @OA\Property(property="is_iframe", type="boolean", description="是否是 iframe 打开游戏"),
     *                  @OA\Property(property="is_mobile_iframe", type="boolean", description="移动端是否是 iframe 打开游戏"),
     *                  @OA\Property(property="is_using_cookie", type="boolean", description="if the game uses cookie"),
     *                  @OA\Property(property="is_effective_bet", type="boolean", description="是否计算有效投注"),
     *                  @OA\Property(property="is_close_bonus", type="boolean", description="是否可用于关闭红利"),
     *                  @OA\Property(property="is_close_cash_back", type="boolean", description="是否可用于关闭赎返"),
     *                  @OA\Property(property="is_close_adjustment", type="boolean", description="是否可用于关闭调整"),
     *                  @OA\Property(property="is_calculate_reward", type="boolean", description="是否可用于计算积分"),
     *                  @OA\Property(property="is_calculate_cash_back", type="boolean", description="是否可用于计算赎返"),
     *                  @OA\Property(property="is_calculate_rebate", type="boolean", description="是否可用于计算返点"),
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  @OA\Property(property="is_soon", type="boolean", description="是否即将发布"),
     *                  @OA\Property(property="status", type="boolean", description="状态"),
     *                  @OA\Property(property="currencies", type="array", description="币别", @OA\Items(
     *                  )),
     *                  @OA\Property(property="languages", type="array", description="语言", @OA\Items(
     *                     @OA\Property(property="language", type="string", description="语言"),
     *                     @OA\Property(property="name", type="string", description="名称"),
     *                     @OA\Property(property="description", type="string", description="描述"),
     *                     @OA\Property(property="content", type="string", description="内容"),
     *                     @OA\Property(property="web_img_id", type="integer", description="Pc端图片id"),
     *                     @OA\Property(property="web_menu_img_id", type="integer", description="Pc Menu 端图片id"),
     *                     @OA\Property(property="small_img_path_id", type="integer", description="游戏小图标id"),
     *                     @OA\Property(property="droplist_img_path_id", type="integer", description="下拉游戏图片id"),
     *                     @OA\Property(property="mobile_img_id", type="integer", description="Mobile端图片id"),
     *                     @OA\Property(property="mobile_img_2_id", type="integer", description="Mobile端图片2id"),
     *                  )),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/Game"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function update(Game $game, GameRequest $request)
    {
        $data = remove_null($request->except(['platform_code', 'product_code', 'code', 'type']));

        $data['languages'] = $this->getUpdateImgPath($data['languages'], $game->languages);
        $data['last_save_admin'] = $this->user->name;
        $data['last_save_at'] = now();

        $game->update($data);

        return $this->response->item($game, new GameTransformer());
    }

    /**
     * @OA\Delete(
     *      path="/backstage/games/{game}",
     *      operationId="backstage.games.delete",
     *      tags={"Backstage-游戏"},
     *      summary="删除游戏",
     *      @OA\Parameter(
     *         name="game",
     *         in="path",
     *         description="游戏id",
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
    public function destroy(Game $game)
    {
        $game->delete();

        return $this->response->noContent();
    }

    protected function getImgPath($languages)
    {
        $imgKeys = array_keys(Game::$imgFields);
        foreach ($languages as &$language) {
            foreach($language as $k => $img) {
                if (in_array($k, Game::$languageRequestFields)) {
                    if (in_array($k, $imgKeys)) {
                        $image = Image::find($img);
                        $language[Game::$imgFields[$k]] = $image ? $image->path : '';
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
        $imgKeys = array_keys(Game::$imgFields);
        $imgValues = array_values(Game::$imgFields);
        foreach ($languages as $key => &$language) {
            foreach($language as $k => $img) {

                if (in_array($k, $imgValues)) {
                        $language[$k] = $oldLanguages[$key][$k];
                }

                if (in_array($k, $imgKeys)) {
                    if (!empty($img)) {
                        $image = Image::find($img);
                        $language[Game::$imgFields[$k]] = $image ? $image->path : '';
                    }
                    unset($language[$k]);
                }
            }
        }

        return $languages;
    }

    public function audit(Game $game, GameRequest $request)
    {
        $histories = $game->audits()->orderBy('id', 'desc')->paginate($request->per_page);
        foreach ($histories as $history) {
            $oldValues = $history->old_values;
            $newValues = $history->new_values;
            $newOutput = '';
            $oldOutput  = '';

            foreach ($oldValues as $key => $oldValue) {
                switch ($key){
                    case 'currencies':
                        $oldOutput .= 'currencies' . $oldValue . ';';
                        break;
                    case 'is_hot':
                        $oldOutput .= 'is_hot' . $oldValue . ';';
                        break;
                    case 'is_new':
                        $oldOutput .= 'is_new' . $oldValue . ';';
                        break;
                    case 'is_iframe':
                        $oldOutput .= 'is_iframe' . $oldValue . ';';
                        break;
                    case 'is_effective_bet':
                        $oldOutput .= 'is_effective_bet' . $oldValue . ';';
                        break;
                    case 'is_close_bonus':
                        $oldOutput .= 'is_close_bonus' . $oldValue . ';';
                        break;
                    case 'is_close_cash_back':
                        $oldOutput .= 'is_close_cash_back' . $oldValue . ';';
                        break;
                    case 'is_calculate_reward':
                        $oldOutput .= 'is_calculate_reward' . $oldValue . ';';
                        break;
                    case 'is_calculate_cash_back':
                        $oldOutput .= 'is_calculate_cash_back' . $oldValue . ';';
                        break;
                    case 'is_calculate_rebate':
                        $oldOutput .= 'is_calculate_rebate' . $oldValue . ';';
                        break;
                    case 'remark':
                        $oldOutput .= 'remark' . $oldValue . ';';
                        break;
                    case 'sort':
                        $oldOutput .= 'sort' . $oldValue . ';';
                        break;
                    case 'status':
                        $oldOutput .= 'status' . $oldValue . ';';
                        break;
                    default:
                        $oldOutput .= $key . ':' . $oldValue . ';';
                        break;
                }
            }

            foreach ($newValues as $key => $newValue) {
                switch ($key){
                    case 'currencies':
                        $newOutput .= 'currencies' . $newValue . ';';
                        break;
                    case 'is_hot':
                        $newOutput .= 'is_hot' . $newValue . ';';
                        break;
                    case 'is_new':
                        $newOutput .= 'is_new' . $newValue . ';';
                        break;
                    case 'is_iframe':
                        $newOutput .= 'is_iframe' . $newValue . ';';
                        break;
                    case 'is_effective_bet':
                        $newOutput .= 'is_effective_bet' . $newValue . ';';
                        break;
                    case 'is_close_bonus':
                        $newOutput .= 'is_close_bonus' . $newValue . ';';
                        break;
                    case 'is_close_cash_back':
                        $newOutput .= 'is_close_cash_back' . $newValue . ';';
                        break;
                    case 'is_calculate_reward':
                        $newOutput .= 'is_calculate_reward' . $newValue . ';';
                        break;
                    case 'is_calculate_cash_back':
                        $newOutput .= 'is_calculate_cash_back' . $newValue . ';';
                        break;
                    case 'is_calculate_rebate':
                        $newOutput .= 'is_calculate_rebate' . $newValue . ';';
                        break;
                    case 'remark':
                        $newOutput .= 'remark' . $newValue . ';';
                        break;
                    case 'sort':
                        $newOutput .= 'sort' . $newValue . ';';
                        break;
                    case 'status':
                        $newOutput .= 'status' . $newValue . ';';
                        break;
                    default:
                        $newOutput .= $key;
                        break;
                }
            }
            $history->old_value = $oldOutput;
            $history->new_value = $newOutput;
        }

        return $this->response->paginator($histories, new AuditTransformer());
    }
}
