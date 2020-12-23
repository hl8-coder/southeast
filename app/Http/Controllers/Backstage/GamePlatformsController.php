<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\GamePlatformRequest;
use App\Models\GamePlatform;
use App\Models\UserAccount;
use App\Repositories\GamePlatformUserRepository;
use App\Repositories\UserRepository;
use App\Services\GamePlatformService;
use App\Transformers\GamePlatformTransformer;
use App\Transformers\GamePlatformUserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class GamePlatformsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/game_platforms",
     *      operationId="backstage.game_platforms.index",
     *      tags={"Backstage-游戏平台"},
     *      summary="获取游戏平台列表",
     *      @OA\Parameter(name="filter[code]", in="query", description="唯一码", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/GamePlatform"),
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
        $platforms = QueryBuilder::for(GamePlatform::class)
                    ->allowedFilters(
                        Filter::exact('status'),
                        'code'
                    )
                    ->sortByDesc()
                    ->paginate($request->per_page);

        return $this->response->paginator($platforms, new GamePlatformTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/game_platforms/{game_platform}",
     *      operationId="backstage.game_platforms.update",
     *      tags={"Backstage-游戏平台"},
     *      summary="更新游戏平台",
     *      @OA\Parameter(
     *         name="game_platform",
     *         in="path",
     *         description="平台id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="name", type="string", description="名称"),
     *                  @OA\Property(property="icon", type="string", description="图标"),
     *                  @OA\Property(property="request_url", type="string", description="api请求地址"),
     *                  @OA\Property(property="report_request_url", type="string", description="报表请求地址"),
     *                  @OA\Property(property="launcher_request_url", type="string", description="游戏启动地址"),
     *                  @OA\Property(property="rsa_our_private_key", type="string", description="RSA我方私钥"),
     *                  @OA\Property(property="rsa_our_public_key", type="string", description="RSA我方公钥"),
     *                  @OA\Property(property="rsa_public_key", type="string", description="RSA平台公钥"),
     *                  @OA\Property(property="account", type="object", description="账户相关"),
     *                  @OA\Property(property="interval", type="integer", description="间隔时间(分钟)"),
     *                  @OA\Property(property="is_maintain", type="integer", description="平台游戏是否维护"),
     *                  @OA\Property(property="is_wallet_maintain", type="integer", description="平台钱包是否维护"),
     *                  @OA\Property(property="delay", type="integer", description="延迟时间(分钟)"),
     *                  @OA\Property(property="offset", type="integer", description="偏移时间(分钟)"),
     *                  @OA\Property(property="limit", type="integer", description="每分钟现在拉取几次"),
     *                  @OA\Property(property="is_update_odds", type="integer", description="是否更新odds"),
     *                  @OA\Property(property="remark", type="string", description="抽成信息"),
     *                  @OA\Property(property="status", type="boolean", description="状态"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="更新成功",
     *          @OA\JsonContent(ref="#/components/schemas/GamePlatform"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function update(GamePlatform $gamePlatform, GamePlatformRequest $request)
    {
        $data = remove_null($request->except(['code']));

        $gamePlatform->update($data);

        return $this->response->item($gamePlatform, new GamePlatformTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/game_platforms/transfer",
     *      operationId="backstage.game_platforms.transfer",
     *      tags={"Backstage-游戏平台"},
     *      summary="转账",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="user_name", type="string", description="会员名称"),
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
    public function transfer(GamePlatformRequest $request)
    {
        $user = UserRepository::findByName($request->input('user_name'));

        $service = new GamePlatformService();

        $details      = [
            'form' => null,
            'to'   => null,
        ];
        $fromPlatform = GamePlatform::findByCode($request->from_platform_code);
        $toPlatform   = GamePlatform::findByCode($request->to_platform_code);

        $service->checkTransferAmountLimit($user->currency, $request->amount);

        $service->checkPlatformIsWalletMaintain($fromPlatform, $toPlatform);

        # 主转入子
        if (UserAccount::isMainWallet($request->from_platform_code)) {
            $details['to'] = $service->transferIn($toPlatform, $user, $request->amount, $request->getClientIp(), $request->bonus_code, $this->user->name);
        } elseif (UserAccount::isMainWallet($request->to_platform_code)) {
            # 子转出主
            $details['form'] = $service->transferOut($fromPlatform, $user, $request->amount, $request->getClientIp(), $this->user->name);
        } else {
            # 子转子
            $details = $service->internalTransfer($fromPlatform, $toPlatform, $user, $request->amount, $request->getClientIp(), $this->user->name);
        }

        # 转账成功后清除会员第三方钱包缓存
        if ($fromPlatform) {
            Cache::forget('game_platform_balance_' . $user->id . '_' . $fromPlatform->code);
        }

        if ($toPlatform) {
            Cache::forget('game_platform_balance_' . $user->id . '_' . $toPlatform->code);
        }

        return $this->response->array($details);
    }

    /**
     * @OA\Get(
     *      path="/backstage/game_platforms/{code}/balance",
     *      operationId="backstage.game_platforms.balance",
     *      tags={"Backstage-游戏平台"},
     *      summary="获取会员第三方钱包余额",
     *      @OA\Parameter(
     *         name="code",
     *         in="path",
     *         description="游戏平台code",
     *         @OA\Schema(
     *             type="string"
     *         )
     *      ),
     *      @OA\Parameter(
     *         name="user_name",
     *         in="query",
     *         description="会员名称",
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
    public function balance(Request $request, GamePlatformService $service)
    {
        $platformCode = $request->route('code');
        $userName = $request->user_name;
        if (!$user = UserRepository::findByName($userName)) {
            return $this->response->errorNotFound();
        }

        try {
            $gamePlatformUser = $service->getGamePlatformUserByUser($user, $platformCode);
        } catch (\Exception $e) {
            $gamePlatformUser = GamePlatformUserRepository::findByUserAndPlatform($user->id, $platformCode);
        }

        return $this->response->item($gamePlatformUser, new GamePlatformUserTransformer('backstage'));
    }
}
