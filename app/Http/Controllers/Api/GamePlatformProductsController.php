<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\GamePlatform;
use App\Models\GamePlatformProduct;
use App\Repositories\UserRepository;
use App\Transformers\GamePlatformProductTransformer;
use Illuminate\Http\Request;

class GamePlatformProductsController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/game_platform_products",
     *      operationId="api.game_platform_products.index",
     *      tags={"Api-游戏"},
     *      summary="获取游戏产品列表",
     *      @OA\Parameter(name="currency", in="header", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="device", in="header", description="装置", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="type", in="query", description="游戏类型 1:捕鱼 2:老虎机 3:真人 4:体育 5:Lottery 6:P2P", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/GamePlatformProduct"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *  )
     */
    public function index(Request $request)
    {
        $currency = $request->header('currency');
        $device   = $request->header('device');

        $platformCodes = GamePlatform::getEnablePlatformCode();

        $products      = GamePlatformProduct::getAll()->where('status', true)->whereIn('platform_code', $platformCodes);
        if (isset($request->type)) {
            $products = $products->where('type', $request->type);
        }
        $user = $this->user;

        $products = $products->filter(function ($product) use ($currency, $device, $user) {

            if ($user && !in_array($product->platform_code, UserRepository::getAvailableGamePlatformCode($user))) {
                return false;
            }

            if (($user && !$product->checkCurrencySet($user->currency)) || !$product->checkCurrencySet($currency)) {
                return false;
            }

            if (empty($product->devices) || !in_array($device, $product->devices)) {
                return false;
            }

            return true;
        })->sortByDesc('sort');
        
        return $this->response->collection($products, new GamePlatformProductTransformer('front_index'));
    }
}
