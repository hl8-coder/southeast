<?php

namespace App\Http\Controllers\Api;

use App\Models\Game;
use App\Models\GamePlatform;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\GamePlatformProduct;
use App\Repositories\UserRepository;
use App\Services\GamePlatformService;
use App\Transformers\GameTransformer;
use App\Transformers\GameSubMenuTransformer;
use App\Http\Controllers\ApiController;

class GamesController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/games",
     *      operationId="api.games.index",
     *      tags={"Api-游戏"},
     *      summary="获取游戏列表",
     *      @OA\Parameter(name="currency", in="header", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="device", in="header", description="装置", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="language", in="header", description="语言", @OA\Schema(type="string")),
     *      @OA\Parameter(name="product_code", in="query", description="游戏产品code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="code", in="query", description="唯一码", @OA\Schema(type="string")),
     *      @OA\Parameter(name="type", in="query", description="游戏类型 1:捕鱼 2:老虎机 3:真人 4:体育", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="name", in="query", description="游戏名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="is_hot", in="query", description="热门游戏", @OA\Schema(type="boolean")),
     *      @OA\Parameter(name="is_new", in="query", description="最新游戏", @OA\Schema(type="boolean")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Game"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *  )
     */
    public function index(Request $request)
    {
        $device   = $request->header('device');
        $currency = $request->header('currency');

        $games     = $this->getFilterGames($currency, $device, $request->all(), $this->user);
        $paginator = $this->paginate($request, $games);

        $product = GamePlatformProduct::findByCodeFromCache($request->product_code);

        return $this->response->paginator($paginator, new GameTransformer('front_index'))->setMeta([
            'product_web_img_path' => $product ? get_image_url($product->two_web_img_path) : '',
        ]);
    }

    /**
     * @OA\Get(
     *      path="/games/no_slot",
     *      operationId="api.games.no_slot",
     *      tags={"Api-游戏"},
     *      summary="获取游戏列表",
     *      @OA\Parameter(name="currency", in="header", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="device", in="header", description="装置", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="language", in="header", description="语言", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Game"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *  )
     */
    public function noSlotIndex(Request $request)
    {
        $device   = $request->header('device');
        $currency = $request->header('currency');
        $user = $this->user;

        $availableGamePlatformCodes = $user ? UserRepository::getAvailableGamePlatformCode($user) : [];

        $fields = [
            'id',
            'platform_code',
            'product_code',
            'code',
            'devices',
            'currencies',
            'languages',
            'type',
            'web_img_path',
            'droplist_img_path',
            'is_mobile_iframe',
            'is_iframe',
            'is_using_cookie',
            'is_soon',
        ];

        # 这里不再用缓存，用缓存无法做到预加载
        $games = Game::query()->select($fields)
            ->where('status', true)
            ->where('type', '!=', GamePlatformProduct::TYPE_SLOT)
            ->with(['platform', 'product'])
            ->sortByDesc()
            ->get();

        $games = $games->filter(function ($value) use ($currency, $device, $user, $availableGamePlatformCodes) {

            if ($user && !in_array($value->platform_code, $availableGamePlatformCodes)) {
                return false;
            }

            if (!in_array($device, $value->devices)) {
                return false;
            }

            if (($user && !$value->checkCurrencySet($user->currency)) || !$value->checkCurrencySet($currency)) {
                return false;
            }

            return true;
        });

        return $this->response->collection($games, new GameTransformer('no_slot_index'));
    }

    /**
     * @OA\Get(
     *      path="/games/invalid_bet",
     *      operationId="api.games.invalid_bet",
     *      tags={"Api-游戏"},
     *      summary="获取无效投注游戏列表",
     *      @OA\Parameter(name="currency", in="header", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="device", in="header", description="装置", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="language", in="header", description="语言", @OA\Schema(type="string")),
     *      @OA\Parameter(name="product_code", in="query", description="游戏产品code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="code", in="query", description="唯一码", @OA\Schema(type="string")),
     *      @OA\Parameter(name="type", in="query", description="游戏类型 1:捕鱼 2:老虎机 3:真人 4:体育", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="name", in="query", description="游戏名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="is_hot", in="query", description="热门游戏", @OA\Schema(type="boolean")),
     *      @OA\Parameter(name="is_new", in="query", description="最新游戏", @OA\Schema(type="boolean")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Game"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *  )
     */
    public function invalidBetIndex(Request $request)
    {
        $device   = $request->header('device');
        $currency = $request->header('currency');

        $games     = $this->getFilterGames($currency, $device, $request->all(), $this->user);
        $games     = $games->where('is_effective_bet', false);
        $paginator = $this->paginate($request, $games);

        return $this->response->paginator($paginator, new GameTransformer('front_index'));
    }

    /**
     * @OA\Get(
     *      path="/games/hot",
     *      operationId="api.games.hot_index",
     *      tags={"Api-游戏"},
     *      summary="获取热门游戏列表",
     *      @OA\Parameter(name="currency", in="header", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="product_code", in="query", description="游戏产品code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="type", in="query", description="游戏类型", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Game"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *  )
     */
    public function hotIndex(Request $request)
    {
        $device   = $request->header('device');
        $currency = $request->header('currency');

        $games = $this->getFilterGames($currency, $device, $request->all(), $this->user);
        $games = $games->where('is_hot', true)->take(3);

        return $this->response->collection($games, new GameTransformer('front_index'));
    }

    /**
     * @OA\Get(
     *      path="/games/sub_menu",
     *      operationId="api.games.sub_menu",
     *      tags={"Api-游戏"},
     *      summary="从菜单中获取游戏列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/GameSubMenu"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *  )
     */
    public function subMenu(Request $request)
    {
        $device         = $request->header('device');
        $currency       = $request->header('currency');
        $limit          = $request->get('limit');
        $transformer    = new GameSubMenuTransformer();
        $productTypes   = [1, 3, 4, 5, 6];
        $limit          = !empty($limit)? $limit : 10;
        $subMenus       = [];
        foreach($productTypes as $type) {
            $games      = $this->getFilterGames($currency, $device, ['type' => $type], $this->user);
            $hotGames   = $games->where('is_hot', true);
            if(count($hotGames) < 1) {
                $hotGames = $games;
            }
            $subMenus[$type] = $transformer->transform($hotGames->take($limit));
        }

        return $this->response->array($subMenus);
    }

    /**
     * @OA\Post(
     *      path="/games/{game}/login",
     *      operationId="api.games.login",
     *      tags={"Api-游戏"},
     *      summary="登录游戏",
     *      @OA\Parameter(name="device", in="header", description="装置", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="game", in="path", description="游戏id", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="登录成功",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="url", description="游戏地址", type="string"),
     *              @OA\Property(property="is_iframe", description="是否iframe打开", type="boolean"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function login(Game $game, Request $request)
    {
        $device = $request->header('device');

        $data = [
            'ip'     => $request->getClientIp(),
            'code'   => $game->code,
            'device' => $device,
            'is_try' => false,
        ];

        $url = $this->getLoginUrl($this->user, $game->platform, $data);

        return $this->response->array([
            'url'       => $url,
            'is_iframe'         => $game->is_iframe,
            'is_mobile_iframe'  => $game->is_mobile_iframe,
        ]);
    }

    /**
     * @OA\Post(
     *      path="/games/{game}/try_login",
     *      operationId="api.games.try_login",
     *      tags={"Api-游戏"},
     *      summary="登录试玩游戏",
     *      @OA\Parameter(name="device", in="header", description="装置", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="game", in="path", description="游戏id", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="登录成功",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="url", description="游戏地址", type="string"),
     *              @OA\Property(property="is_iframe", description="是否iframe打开", type="boolean"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *  )
     */
    public function tryLogin(Game $game, Request $request)
    {
        $device = $request->header('device');
        $currency = $request->header('currency', 'VND');

        if ('USD' == $currency) {
            $currency = 'VND';
        }

        if ($user = $this->user()) {
            $currency = $user->currency;
        }
        $data = [
            'ip'        => $request->getClientIp(),
            'code'      => $game->code,
            'device'    => $device,
            'language'  => app()->getLocale(),
            'is_try'    => true,
        ];

        # 获取试玩会员[系统至少保持一个测试会员]
        $tryUser = User::query()->where('is_test', true)->where('currency', $currency)->first();
        if (!$tryUser) {
            return $this->response->error('Not Try', 422);
        }

        $url = $this->getLoginUrl($tryUser, $game->platform, $data);

        return $this->response->array([
            'url'               => $url,
            'is_iframe'         => $game->is_iframe,
            'is_mobile_iframe'  => $game->is_mobile_iframe,
        ]);
    }

    public function getFilterGames($currency, $device, $filters, User $user = null)
    {
        $availableGamePlatformCode = $user ? UserRepository::getAvailableGamePlatformCode($user) : [];
        $name  = !empty($filters['name']) ? $filters['name'] : '';
        # 这里不再用缓存，用缓存无法做到预加载
        $games = Game::query()->where('status', true)
                ->with(['platform', 'product'])
                ->get();
        $games = $games->filter(function ($value) use ($currency, $device, $user, $availableGamePlatformCode, $name) {

            if ($user && !in_array($value->platform_code, $availableGamePlatformCode)) {
                return false;
            }

            if (!in_array($device, $value->devices)) {
                return false;
            }

            if (($user && !$value->checkCurrencySet($user->currency)) || !$value->checkCurrencySet($currency)) {
                return false;
            }

            if (!empty($name)) {
                $languageSet = $value->getLanguageSet(app()->getLocale());
                return false !== strpos(strtolower($languageSet['name']), strtolower($name));
            }

            return true;
        });

        if (isset($filters['product_code'])) {
            $games = $games->where('product_code', $filters['product_code']);
        }

        if (isset($filters['type'])) {
            $games = $games->where('type', $filters['type']);
        }

        if (isset($filters['code'])) {
            $games = $games->where('code', $filters['code']);
        }

        if (isset($filters['is_hot'])) {
            $games = $games->where('is_hot', $filters['is_hot']);
        }

        if (isset($filters['is_new'])) {
            $games = $games->where('is_new', $filters['is_new']);
        }

        $games = $games->sortByDesc('sort');

        return $games;
    }

    /**
     * 获取登录地址
     *
     * @param User $user
     * @param GamePlatform $platform
     * @param $data
     * @return mixed
     */
    public function getLoginUrl(User $user, GamePlatform $platform, $data)
    {
        if ($platform->isMaintain()) {
            error_response(422, __('gamePlatform.game_platform_maintain'));
        }

        return app(GamePlatformService::class)->login($user, $platform, $data);
    }
}
