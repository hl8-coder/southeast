<?php

namespace App\Http\Controllers\Api\Affiliate;

use App\Http\Controllers\Api\AuthorizationsController as ApiController;
use App\Http\Requests\Affiliate\AuthorizationRequest;
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\Transformers\UserTransformer;
use Illuminate\Support\Facades\Auth;

class AuthorizationsController extends ApiController
{
    /**
     * @OA\Post(
     *      path="/affiliate/authorizations",
     *      operationId="affiliate.authorizations.store",
     *      tags={"Affiliate-代理"},
     *      summary="代理登录",
     *      @OA\Parameter(name="device", in="header", description="装置 1:pc 2:mobile", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="name", type="string", description="账号"),
     *                  @OA\Property(property="password", type="string", format="password", description="密码"),
     *                  @OA\Property(property="device", type="string", format="device", description="装置"),
     *                  required={"name", "password"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/tokenInfo")
     *       ),
     *       @OA\Response(response=401, description="用户名或密码错误"),
     *       @OA\Response(response=403, description="禁止登录"),
     *     )
     */
    public function affiliateStore(AuthorizationRequest $request, UserService $service)
    {
        $isAgent = true;

        $user = UserRepository::findAffiliateByName($request->name);

        $credentials = [
            'name'     => $request->name,
            'password' => $request->password,
            'is_agent' => $isAgent,
        ];

        $logLoginStatus = 1;

        $loginIp = $request->getClientIp();

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            $logLoginStatus = 0;
            $service->recordLoginLog($user, $loginIp, $request->header('device'), $request->userAgent(), $logLoginStatus);
            UserRepository::addLoginFailTimes($user);
            return $this->response->error(__('authorization.wrong_name_or_password'), 422);
        }

        # 设置旧token过期`
        $service->setTokenInvalidate($user);

        # 记录登录日志
        $service->recordLoginLog($user, $loginIp, $request->header('device'), $request->userAgent(), $logLoginStatus);

        # 更新最后登录信息
        $user->info->updateLastLogin($loginIp, $token, $request->header('device'));

        return $this->response->item($user->refresh(), new UserTransformer('affiliate_show'))
            ->setMeta([
                'access_token' => $token,
                'token_type'   => 'Bearer',
                'expires_in'   => Auth::guard('api')->factory()->getTTL() * 60
            ])
            ->setStatusCode(201);
    }
}
