<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\AuthorizationRequest;
use App\Http\Requests\Api\TokenRequest;
use App\Models\Model;
use App\Models\Report;
use App\Models\Token;
use App\Models\User;
use App\Models\UserAccount;
use App\Services\ReportService;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\Transformers\UserTransformer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Torann\GeoIP\Facades\GeoIP;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class AuthorizationsController extends ApiController
{
    /**
     * @OA\Post(
     *      path="/authorizations",
     *      operationId="authorizations.store",
     *      tags={"Api-授权"},
     *      summary="会员登录",
     *      @OA\Parameter(name="device", in="header", description="装置 1:pc 2:mobile", required=true, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="currency", in="header", description="币别", required=true, @OA\Schema(type="string")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="name", type="string", description="账号"),
     *                  @OA\Property(property="password", type="string", format="password", description="密码"),
     *                  required={"name", "password"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  allOf={
     *                      @OA\Schema(ref="#/components/schemas/User"),
     *                      @OA\Schema(
     *                          @OA\Property(property="meta", ref="#/components/schemas/tokenInfo")
     *                      ),
     *                  }
     *              ),
     *          ),
     *       ),
     *       @OA\Response(response=403, description="禁止登录"),
     *       @OA\Response(response=422, description="用户名或密码错误"),
     *     )
     */
    public function store(AuthorizationRequest $request, UserService $service)
    {
        $isAgent = $request->is_agent ?? false;

        if ($isAgent) {
            $user = UserRepository::findAffiliateByName($request->name);
        } else {
            $user = UserRepository::findByName($request->name);
        }
        $credentials = [
            'name'     => $request->name,
            'password' => $request->password,
            'is_agent' => $isAgent,
        ];

        # 验证泰国迁移密码
        $this->specialThUserLogin($user, $request->password);

        $loginIp = $request->getClientIp();

        $status = 1;

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            UserRepository::addLoginFailTimes($user);
            $status = 0;
            $service->recordLoginLog($user, $loginIp, $request->header('device'), $request->userAgent(), $status);

            return $this->response->error(__('authorization.wrong_name_or_password'), 422);
        }

        # 设置旧token过期
        $service->setTokenInvalidate($user);

        # 记录登录日志
        $service->recordLoginLog($user, $loginIp, $request->header('device'), $request->userAgent(), $status);

        # 更新最后登录信息
        $user->info->updateLastLogin($loginIp, $token, $request->header('device'));

        # 会员登录后创建统计日志
        (new ReportService())->platformReport(
            $user,
            UserAccount::MAIN_WALLET,
            Report::TYPE_DEPOSIT,
            0,
            now()
        );

        return $this->response->item($user->refresh(), new UserTransformer('front_show'))
            ->setMeta([
                'access_token' => $token,
                'token_type'   => 'Bearer',
                'expires_in'   => Auth::guard('api')->factory()->getTTL() * 60,
            ])
            ->setStatusCode(201);
    }

    /**
     * @OA\Put(
     *      path="/authorizations/current",
     *      operationId="authorizations.current.update",
     *      tags={"Api-授权"},
     *      summary="刷新token",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/tokenInfo")
     *       ),
     *       @OA\Response(response=401, description="无效token"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function update()
    {
        $token = Auth::guard('api')->refresh();

        return $this->respondWithToken($token);
    }

    /**
     * @OA\Delete(
     *      path="/authorizations/current",
     *      operationId="authorizations.current.destroy",
     *      tags={"Api-授权"},
     *      summary="登出",
     *      @OA\Response(response=204,description="no content"),
     *       @OA\Response(response=401, description="无效token"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function destroy()
    {
        try {
            Auth::guard('api')->logout();
        } catch (\Exception $e) {

        }

        return $this->response->noContent();
    }

    /**
     * @OA\Schema(
     *   schema="tokenInfo",
     *   required={"access_token", "token_type", "expires_in"},
     *   type="object",
     *   @OA\Property(property="access_token", type="integer", description="token"),
     *   @OA\Property(property="token_type", type="string", description="token类型", default="Bearer"),
     *   @OA\Property(property="expires_in", type="integer", description="过期时间"),
     * )
     */
    protected function respondWithToken($token)
    {
        return $this->response->array([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'expires_in'   => Auth::guard('api')->factory()->getTTL() * 60,
        ]);
    }

    # 迁移泰国密码验证
    private function specialThUserLogin(User $user, $pwd)
    {
        if ('THB' == $user->currency &&
             empty($user->password) &&
            !empty($user->fund_password &&
             $user->fund_password == sha1($pwd))) {
            $user->updatePassword($pwd);
            $user->refresh();
        }
        return $user;
    }




    /**
     * @OA\Get(
     *      path="/auth/language",
     *      operationId="authorizations.auth.language",
     *      tags={"Api-会员"},
     *      summary="匹配当前的币别",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/currentLanguage")
     *       ),
     *       @OA\Response(response=401, description="无效token"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    /**
     * @OA\Schema(
     *   schema="currentLanguage",
     *   required={"access_token", "token_type", "expires_in"},
     *   type="object",
     *   @OA\Property(property="currency", type="string", description="当前地区的币别"),
     * )
     */
    public function authLanguage(Request $request)
    {
        $currentLanguage = 'en-US';
        $loginIp          = $request->getClientIp();
        $location         = GeoIP::getLocation($loginIp);
        $currency         = empty($location) || empty($location->currency) ? 'VND' : $location->currency;
        $currencyLanguage = array_flip(Model::$languageToCurrency);
        try {
            $currentLanguage  = isset($currencyLanguage[$currency]) ? $currencyLanguage[$currency] : 'en-US';
        } catch (\Exception $e) {

        }

        return ['language' => $currentLanguage];
    }

    /**
     * @OA\Get(
     *      path="/exchange/code",
     *      operationId="api.exchange.code",
     *      tags={"Api-授权"},
     *      summary="交换code",
     *      @OA\Parameter(name="t_code", in="query", description="唯一验证码", required=true, @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=201,
     *          description="successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  allOf={
     *                      @OA\Schema(
     *                          @OA\Property(property="meta", ref="#/components/schemas/tokenInfo")
     *                      ),
     *                  }
     *              ),
     *          ),
     *       ),
     *       @OA\Response(response=422, description="code错误"),
     *     )
     */
    public function exchangeCode(TokenRequest $request)
    {
        $code = $request->t_code;

        $token = Token::findTokenBy($code);

        return $this->respondWithToken($token);
    }
}
