<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\UserRequest;
use App\Jobs\SendEmailJob;
use App\Models\MailboxTemplate;
use App\Models\Token;
use App\Models\User;
use App\Models\UserInfo;
use App\Repositories\UserRepository;
use App\Services\CrmService;
use App\Services\UserService;
use App\Transformers\UserAccountTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UsersController extends ApiController
{
    /**
     * @OA\Post(
     *      path="/users?include=info,account,vip,reward",
     *      operationId="api.users.store",
     *      tags={"Api-会员"},
     *      summary="会员注册",
     *      @OA\Parameter(name="currency", in="header", description="币别", required=true, @OA\Schema(type="string")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="name", type="string", description="账号"),
     *                  @OA\Property(property="password", type="string", format="password", description="密码"),
     *                  @OA\Property(property="password_confirmation", type="string", format="password", description="密码"),
     *                  @OA\Property(property="currency", type="string", description="币别"),
     *                  @OA\Property(property="country_code", type="string", description="电话国际代码"),
     *                  @OA\Property(property="phone", type="string", description="电话号码"),
     *                  @OA\Property(property="full_name", type="string", description="姓名"),
     *                  @OA\Property(property="email", type="string", description="邮箱"),
     *                  @OA\Property(property="birth_at", type="string", description="生日", format="date"),
     *                  @OA\Property(property="address", type="string", description="地址"),
     *                  required={"name", "password", "currency", "password_confirmation", "country_code", "phone", "full_name", "email", "birth_at"}
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
     *       @OA\Response(response=400, description="Bad request"),
     *       @OA\Response(response=422, description="验证错误"),
     *       @OA\Response(response=500, description="网络错误"),
     *     )
     */
    public function store(UserRequest $request, UserService $service)
    {
        $data = $request->only([
            'name',
            'password',
            'phone',
            'full_name',
            'email',
            'birth_at',
            'address',
            'is_agent',
            'referrer_code',
            'country_code',
            'affiliate_code',
            'other_contact',
            'currency',
        ]);

        try {
            $user = DB::transaction(function () use ($service, $data, $request) {
                $registerUrl = app()->isLocal() ? $request->root() : $request->header('DomainFE');
                $registerIP  = $request->input('ip', $request->getClientIp());
                return $service->store($data, $registerIP, $registerUrl);
            });
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 422);
        }

        if ($user) {
            dispatch(new SendEmailJob(MailboxTemplate::WELCOME, $user->info->email, $user->currency, $user->is_agent, $user->language, '', $user->name))->onQueue('send_email');
        }

        # 新用户加入电销系统
        try {
            app(CrmService::class)->createWelcomeOrder($user);
        }catch (\Exception $exception){
            Log::error($exception);
        }


        $token = Auth::guard('api')->fromUser($user);

        # 更新最后登录信息
        $user->info->updateLastLogin($request->getClientIp(), $token, $request->header('device'));

        # 将token存入token表中
        $code = $this->storeToken($token);

        # 检测crm设置，是否需要电销

        return $this->response->item($user->refresh(), new UserTransformer('front_show'))
            ->setMeta([
                'access_token' => $token,
                'code'         => $code,
                'token_type'   => 'Bearer',
                'expires_in'   => Auth::guard('api')->factory()->getTTL() * 60
            ])
            ->setStatusCode(201);
    }

    public function storeToken($token)
    {
        $code = Token::findAvailableCode();

        Token::query()->create(
            [
                'code'  => $code,
                'token' => $token,
            ]
        );
        return $code;
    }

    /**
     * @OA\Patch(
     *      path="/user?include=info,account,vip,reward",
     *      operationId="api.users.update",
     *      tags={"Api-会员"},
     *      summary="会员更新资料",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="password", type="string", description="密码"),
     *                  @OA\Property(property="gender", type="string", description="性别"),
     *                  @OA\Property(property="odds", type="integer", description="赔率类型"),
     *                  @OA\Property(property="address", type="string", description="地址更新"),
     *                  @OA\Property(property="security_question", type="integer", description="密保问题"),
     *                  @OA\Property(property="security_question_answer", type="string", description="密保问题回答"),
     *                  required={"password"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *       ),
     *       @OA\Response(response=400, description="Bad request"),
     *       @OA\Response(response=422, description="验证错误"),
     *       @OA\Response(response=500, description="网络错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     * ),
     */
    public function update(UserRequest $request)
    {
        $user = $this->user();
        # 验证密码不正确
        if (!Hash::check($request->password, $user->password)) {
            return $this->response->error(__('user.ERROR_PASSWORD'), 422);
        }

        $originOdds = $user->odds;
        try {
            $user = DB::transaction(function () use ($user, $request) {
                $data = $request->only(['security_question', 'security_question_answer', 'odds', 'language']);
                $data = remove_null($data);
                $user->update($data);

                $info = $request->only(['gender', 'address']);
                $info = remove_null($info);
                $user->info->update($info);

                return $user;
            });
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 422);
        }

        # 更新第三方服务商odds
        UserRepository::updatePlatformUserOdds($user, $originOdds, $request->odds);

        UserRepository::checkProfileVerified($user);

        return $this->response->item($user, new UserTransformer('front_show'));
    }

    /**
     * @OA\Get(
     *      path="/user?include=info,account,vip,reward",
     *      operationId="api.users.me",
     *      tags={"Api-会员"},
     *      summary="获取会员信息",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *       ),
     *       @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function me()
    {
        return $this->response->item($this->user(), new UserTransformer('front_show'));
    }

    /**
     * @OA\Get(
     *      path="/user/balance",
     *      operationId="api.users.user_account",
     *      tags={"Api-会员"},
     *      summary="获取会员账户信息",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/UserAccount")
     *       ),
     *       @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function getBalance()
    {
        return $this->response->item($this->user->account, new UserAccountTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/user/password?include=info",
     *      operationId="api.users.password",
     *      tags={"Api-会员"},
     *      summary="修改密码",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="old_password", type="string", format="password", description="旧密码"),
     *                  @OA\Property(property="new_password", type="string", format="password", description="新密码"),
     *                  @OA\Property(property="new_password_confirmation", type="string", format="password", description="新密码确认"),
     *                  required={"old_password", "new_password", "new_password_confirmation"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *       ),
     *       @OA\Response(response=400, description="Bad request"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function changePassword(UserRequest $request)
    {
        $user = $this->user();
        if (!Hash::check($request->old_password, $user->password)) {
            return $this->response()->error(__('authorization.WRONG_PASSWORD'), 422);
        }

        if ($user->updatePassword($request->new_password)) {
            $user->cancelNeedChangePassword();
        }

        return $this->response->item($user, new UserTransformer('front_show'))
            ->setMeta([
                'access_token' => Auth::guard('api')->fromUser($user),
                'token_type'   => 'Bearer',
                'expires_in'   => Auth::guard('api')->factory()->getTTL() * 60
            ]);
    }

    /**
     * @OA\Patch(
     *      path="/user/forget_password",
     *      operationId="api.users.forget_password",
     *      tags={"Api-会员"},
     *      summary="找回密码",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="name", type="string",  description="会员名称"),
     *                  @OA\Property(property="email", type="string", description="邮箱"),
     *                  required={"name", "email"}
     *              ),
     *          ),
     *      ),
     *     @OA\Response(response=204, description="No Content",),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=422, description="验证失败"),
     *     )
     */
    public function forgetPassword(UserRequest $request)
    {
        $user = UserRepository::findByName($request->name);

        $email = $user->info->email;

        if ($email != $request->email) {
            return $this->response->error(__('authorization.NAME_DO_NOT_MATCH'), 422);
        }

        $oldPassword = $user->password;
        $userService = new UserService();
        try {
            DB::transaction(function () use ($userService, $user, $oldPassword, $email) {

                $user = $userService->resetPassword($user);

                if ($oldPassword == $user->password) {
                    throw new \Exception(__('authorization.PASSWORD_FAILED'));
                }
            });
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 422);
        }

        if ($user) {
            dispatch(new SendEmailJob(MailboxTemplate::FORGET_PASSWORD, $user->info->email, $user->currency, $user->is_agent, $user->language, $user->source_password, $user->name))->onQueue('send_email');
        }

        return $this->response->noContent();
    }

    /**
     * @OA\Post(
     *     path="/user/claim_verify_prize?include=info",
     *     operationId="api.users.claim_verify_prize",
     *     tags={"Api-会员"},
     *     summary="领取资料验证奖励",
     *     @OA\Parameter(name="platform_code", in="query", description="平台code", @OA\Schema(type="string")),
     *     @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *       ),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=422, description="验证失败"),
     *     security={
     *           {"bearer": {}}
     *     }
     * )
     */
    public function claimVerifyPrize(UserRequest $request, UserService $userService)
    {
        try {
            $user = $userService->claimVerifyPrize($this->user, $request->platform_code);
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 422);
        }

        return $this->response->item($user, new UserTransformer('front_show'));
    }


    /**
     * @OA\Patch(
     *     path="/users/check_field_unique",
     *     operationId="api.users.check_field_unique",
     *     tags={"Api-会员"},
     *     summary="检查会员资料是否已经存在",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="field", type="string", description="字段"),
     *                  @OA\Property(property="value", type="string", description="值"),
     *              ),
     *          ),
     *      ),
     *     @OA\Response(response=204, description="No Content"),
     *     @OA\Response(response=422, description="验证失败"),
     * )
     */
    public function checkFieldUnique(UserRequest $request)
    {
        if (empty($request->field)
            || empty($request->value)
            || ('name' == $request->field && User::query()->isUser()->where($request->field, $request->value)->exists())
            || ('name' != $request->field && UserInfo::query()->isUser()->where($request->field, $request->value)->exists())) {
            return $this->response->error('-1', 422);
        }

        return $this->response->noContent();
    }
}
