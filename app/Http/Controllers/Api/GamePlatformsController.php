<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\TransferRequest;
use App\Models\ChangingConfig;
use App\Models\GamePlatform;
use App\Models\UserAccount;
use App\Services\GamePlatformService;
use App\Transformers\GamePlatformUserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GamePlatformsController extends ApiController
{
    protected $service;

    public function __construct(GamePlatformService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     *      path="/game_platforms/wallets",
     *      operationId="api.game_platforms.wallets",
     *      tags={"Api-游戏"},
     *      summary="获取会员第三方钱包",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/GamePlatformUser"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function getWallets()
    {
        $platformCodes = GamePlatform::getAll()
            ->where('status', true)
            ->whereNotIn('code', ['IMESports'])
            ->pluck('code')
            ->toArray();

        $wallets = $this->user->gamePlatformUsers()
                    ->whereIn('platform_code', $platformCodes)
                    ->where('balance_status', true)
                    ->with('platform')
                    ->get();

        return $this->response->collection($wallets, new GamePlatformUserTransformer('wallet'));
    }

    /**
     * @OA\Get(
     *      path="/game_platforms/{code}/balance",
     *      operationId="api.game_platforms.balance",
     *      tags={"Api-游戏"},
     *      summary="获取会员第三方钱包余额",
     *      @OA\Parameter(
     *         name="code",
     *         in="path",
     *         description="游戏平台code",
     *         @OA\Schema(
     *             type="string"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/GamePlatformUser")
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function balance(Request $request)
    {
        $platformCode = $request->route('code');

        $gamePlatformUser = $this->service->getGamePlatformUserByUser($this->user(), $platformCode);

        return $this->response->item($gamePlatformUser, new GamePlatformUserTransformer());
    }

    /**
     * @OA\Post(
     *      path="/game_platforms/transfer",
     *      operationId="api.game_platforms.transfer",
     *      tags={"Api-游戏"},
     *      summary="转账",
     *      @OA\Parameter(name="currency", in="header", description="币别", @OA\Schema(type="string")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="from_platform_code", type="string", description="转出平台"),
     *                  @OA\Property(property="to_platform_code", type="string", description="转入平台"),
     *                  @OA\Property(property="amount", type="integer", description="转出金额", format="float"),
     *                  @OA\Property(property="bonus_code", type="string", description="红利代码"),
     *                  required={"from_platform_code", "to_platform_code", "amount"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="转账成功",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="from", description="转出钱包记录", ref="#/components/schemas/GamePlatformTransferDetail"),
     *              @OA\Property(property="to", description="转入钱包记录", ref="#/components/schemas/GamePlatformTransferDetail"),
     *          ),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      @OA\Response(response=422, description="验证不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function transfer(TransferRequest $request)
    {
        $details      = [
            'form' => null,
            'to'   => null,
        ];
        $fromPlatform = GamePlatform::findByCode($request->from_platform_code);
        $toPlatform   = GamePlatform::findByCode($request->to_platform_code);

        $this->service->checkTransferAmountLimit($this->user->currency, $request->amount);

        $this->service->checkPlatformIsWalletMaintain($fromPlatform, $toPlatform);

        # 主转入子
        if (UserAccount::isMainWallet($request->from_platform_code)) {
            $details['to'] = $this->service->transferIn($toPlatform, $this->user, $request->amount, $request->getClientIp(), $request->bonus_code);
        } elseif (UserAccount::isMainWallet($request->to_platform_code)) {
            # 子转出主
            $details['form'] = $this->service->transferOut($fromPlatform, $this->user, $request->amount, $request->getClientIp(), $request->bonus_code);
        } else {
            # 子转子
            $details = $this->service->internalTransfer($fromPlatform, $toPlatform, $this->user, $request->amount, $request->getClientIp());
        }

        # 转账成功后清楚会员第三方钱包缓存
        if ($fromPlatform) {
            Cache::forget('game_platform_balance_' . $this->user->id . '_' . $fromPlatform->code);
        }

        if ($toPlatform) {
            Cache::forget('game_platform_balance_' . $this->user->id . '_' . $toPlatform->code);
        }

        return $this->response->array($details);
    }

    /**
     * @OA\Get(
     *      path="/game_platforms/jackpot",
     *      operationId="api.game_platforms.jackpot",
     *      tags={"Api-游戏"},
     *      summary="获取奖池数据",
     *      @OA\Response(
     *          response=200,
     *          description="登录成功",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="jackpot", description="奖池数据", type="number"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found")
     *  )
     */
    public function getJackpot(Request $request)
    {
        $currency = $request->header('currency');
        if ($user = $this->user()) {
            $currency = $user->currency;
        }

        $baseAmount = 17294832.53;
        $cacheKey   = 'slot_jackpot_' . $currency;
        $code       = 'slot_jackpot_' . $currency;

        if (Cache::has($cacheKey)) {
            $amount = Cache::get($cacheKey);
        } else {
            $amount = ChangingConfig::findValue($code, $baseAmount);
        }

        return $this->response->array([
            'jackpot'  => $amount,
            'currency' => $currency,
        ]);
    }
}
