<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\UserRequest;
use App\Jobs\SendEmailJob;
use App\Models\GamePlatformUser;
use App\Models\MailboxTemplate;
use App\Models\ProfileRemark;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\GamePlatformService;
use App\Services\UserService;
use App\Transformers\AuditTransformer;
use App\Transformers\GamePlatformUserTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class UsersController extends BackstageController
{
    protected $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     *      path="/backstage/users?include=info,account,gamePlatformUsers",
     *      operationId="backstage.users.index",
     *      tags={"Backstage-会员"},
     *      summary="会员列表",
     *      @OA\Parameter(name="filter[name]", in="query", description="名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[risk_group_id]", in="query", description="风控组别id", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[payment_group_id]", in="query", description="支付组别id", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[email]", in="query", description="邮箱", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[phone]", in="query", description="电话号码", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="注册查询开始日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="注册查询结束日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[last_login_start_at]", in="query", description="最后登陆查询开始日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[last_login_end_at]", in="query", description="最后登陆查询结束日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[register_url]", in="query", description="注册地址", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[deposit]", in="query", description="是否充值", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[affiliated_code]", in="query", description="上级代理code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="sort", in="query", description="排序(字段_asc/字段_desc)", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/User"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *         {"bearer": {}}
     *      }
     *  )
     */
    public function index(Request $request)
    {
        $fields = [
            'id',
            'currency',
            'language',
            'name',
            'vip_id',
            'reward_id',
            'risk_group_id',
            'payment_group_id',
            'status',
            'affiliated_code',
            'created_at',
        ];

        $ORM = User::query();

        # 設定排序
        if ($request->order) {
            $order    = explode('_', $request->order);
            $sortType = array_pop($order);
            $ORM->orderBy(implode('_', $order), $sortType);
        }

        $users = QueryBuilder::for($ORM)
            ->select($fields)
            ->isUser()
            ->allowedFilters([
                'name',
                Filter::exact('currency'),
                Filter::exact('status'),
                Filter::exact('risk_group_id'),
                Filter::exact('payment_group_id'),
                Filter::exact('affiliated_code'),
                Filter::scope('email'),
                Filter::scope('phone'),
                Filter::scope('full_name'),
                Filter::scope('start_at'),
                Filter::scope('end_at'),
                Filter::scope('last_login_start_at'),
                Filter::scope('last_login_end_at'),
                Filter::scope('register_url'),
                Filter::scope('deposit'),
            ])
            ->paginate($request->per_page);

        return $this->response->paginator($users, new UserTransformer('backstage_index'));
    }

    /**
     * @OA\Get(
     *      path="/backstage/users/{user}?include=info,account,gamePlatformUsers,vip,reward",
     *      operationId="backstage.users.show",
     *      tags={"Backstage-会员"},
     *      summary="会员详情",
     *      @OA\Parameter(name="user", in="path", description="会员ID", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/User"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *         {"bearer": {}}
     *      }
     *  )
     */
    public function show(User $user)
    {
        return $this->response->item($user, new UserTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/get_user_by_name?include=info,account,gamePlatformUsers,vip,reward",
     *      operationId="backstage.users.get_user_by_name",
     *      tags={"Backstage-会员"},
     *      summary="会员详情",
     *      @OA\Parameter(name="user_name", in="path", description="会员名", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/User"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *         {"bearer": {}}
     *      }
     *  )
     */
    public function showUserByName(UserRequest $request)
    {
        $user = UserRepository::findByName($request->user_name);
        return $this->response->item($user, new UserTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/users/{user}?include=info",
     *      operationId="backstage.users.update",
     *      tags={"Backstage-会员"},
     *      summary="更新会员信息",
     *      @OA\Parameter(name="user", in="path", description="会员ID", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="full_name", type="string", description="全名"),
     *                  @OA\Property(property="birth_at", type="string", description="生日"),
     *                  @OA\Property(property="email", type="string", description="邮箱"),
     *                  @OA\Property(property="country_code", type="string", description="电话国家代码"),
     *                  @OA\Property(property="phone", type="string", description="电话号码"),
     *                  @OA\Property(property="gender", type="integer", description="性别(male,female)"),
     *                  @OA\Property(property="risk_group_id", type="integer", description="风控组别id"),
     *                  @OA\Property(property="payment_group_id", type="integer", description="支付组别id"),
     *                  @OA\Property(property="vip_id", type="integer", description="vip组别id"),
     *                  @OA\Property(property="reward_id", type="integer", description="积分等级id"),
     *                  @OA\Property(property="language", type="string", description="语言"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/User"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function update(User $user, UserRequest $request)
    {
        $originOdds = $user->odds;
        try {
            $user = DB::transaction(function () use ($user, $request) {

                $userData = $request->only(['risk_group_id', 'payment_group_id', 'vip_id', 'reward_id', 'language', 'odds']);
                $userData = remove_null($userData);
                $user->update($userData);

                $infoData = $request->only(['full_name', 'birth_at', 'email', 'country_code', 'phone', 'gender']);
                $infoData = remove_null($infoData);
                $user->info->update($infoData);

                $userBankAccounts = $user->bankAccounts;
                if ($user->is_agent == false && isset($infoData['full_name']) && $userBankAccounts->isNotEmpty()) {
                    $userBankAccounts->each(function ($userBankAccount) use ($infoData) {
                        $userBankAccount->update(['account_name' => $infoData['full_name']]);
                    });
                }

                return $user;
            });
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 422);
        }

        # 更新第三方服务商odds
        UserRepository::updatePlatformUserOdds($user, $originOdds, $request->odds);

        return $this->response->item($user, new UserTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/users/{user}/reset_password",
     *      operationId="backstage.users.reset_password",
     *      tags={"Backstage-会员"},
     *      summary="重置密码",
     *      @OA\Parameter(name="user", in="path", description="会员ID", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="type", type="string", description="重置类型(manual/auto(手动/自动))"),
     *                  @OA\Property(property="new_password", type="string", description="新密码"),
     *                  required={"type"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="successful operation"
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function resetPassword(User $user, UserRequest $request)
    {
        # 获取初始密码
        if ('auto' == $request->type) {
            $password = str_random(8);
        } else {
            $password = $request->new_password;
        }

        if ($user->updatePassword($password)) {
            $user->setNeedChangePassword();
            ProfileRemark::add($user->id, ProfileRemark::CATEGORY_ACCOUNT, 'reset password', $this->user->name);
        }

        if ('auto' == $request->type) {
            dispatch(new SendEmailJob(MailboxTemplate::FORGET_PASSWORD, $user->info->email, $user->currency, $user->is_agent, $user->language, $password))->onQueue('send_email');
        }

        return $this->response->noContent();
    }

    /**
     * @OA\Patch(
     *      path="/backstage/users/{user}/status",
     *      operationId="backstage.users.status.update",
     *      tags={"Backstage-会员"},
     *      summary="更改状态",
     *      @OA\Parameter(name="user", in="path", description="会员ID", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="status", type="integer", description="状态"),
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *                  required={"status", "remark"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="successful operation"
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function updateStatus(User $user, UserRequest $request, UserService $service)
    {
        if ($user->updateStatus($request->status)) {
            ProfileRemark::add($user->id, ProfileRemark::CATEGORY_CHANGE, $request->remark, $this->user->name);
        }

        # 如果是inactive
        if (in_array($request->status, [User::STATUS_INACTIVE, User::STATUS_BLOCKED])) {
            $service->setTokenInvalidate($user);
        }

        return $this->response->item($user, new UserTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/users/{user}/risk_group",
     *      operationId="backstage.users.risk_group.update",
     *      tags={"Backstage-会员"},
     *      summary="更改风控组别",
     *      @OA\Parameter(name="user", in="path", description="会员ID", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="risk_group_id", type="integer", description="风控组别id"),
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *                  required={"status", "remark"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="successful operation"
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function updateRiskGroup(User $user, UserRequest $request)
    {
        if ($user->update(['risk_group_id' => $request->risk_group_id])) {
            $userService = new UserService;
            $admin       = $this->user;
            $userService->modifyStatusByRiskGroup($user, $request->risk_group_id, $admin);
            # 添加remark
            ProfileRemark::add($user->id, ProfileRemark::CATEGORY_RISK, $request->remark, $admin->name);
        }

        return $this->response->item($user, new UserTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/users/{user}/payment_group",
     *      operationId="backstage.users.payment_group.update",
     *      tags={"Backstage-会员"},
     *      summary="更改支付组别",
     *      @OA\Parameter(name="user", in="path", description="会员ID", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="payment_group_id", type="integer", description="支付组别id"),
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *                  required={"status", "remark"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="successful operation"
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function updatePaymentGroup(User $user, UserRequest $request)
    {
        if ($user->update(['payment_group_id' => $request->payment_group_id])) {
            # 添加remark
            ProfileRemark::add($user->id, ProfileRemark::CATEGORY_PAYMENT, $request->remark, $this->user->name);
        }

        return $this->response->item($user->refresh(), new UserTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/users/{user}/reward",
     *      operationId="backstage.users.reward.update",
     *      tags={"Backstage-会员"},
     *      summary="更改积分等级",
     *      @OA\Parameter(name="user", in="path", description="会员ID", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="reward_id", type="integer", description="积分等级"),
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *                  required={"status", "remark"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="successful operation"
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function updateReward(User $user, UserRequest $request)
    {
        if ($user->update(['reward_id' => $request->reward_id])) {
            # 添加remark
            ProfileRemark::add($user->id, ProfileRemark::CATEGORY_CHANGE, $request->remark, $this->user->name);
        }

        return $this->response->item($user->refresh(), new UserTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/users/{user}/verify_phone",
     *      operationId="backstage.users.verify_phone",
     *      tags={"Backstage-会员"},
     *      summary="后台验证手机号",
     *      @OA\Parameter(name="user", in="path", description="会员ID", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=204,
     *          description="No Content"
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       @OA\Response(response=422, description="验证不通过"),
     *       security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function verifyPhone(User $user)
    {
        if ($user->info->phone_verified_at) {
            return $this->response->error('Phone has been verified', 422);
        }

        $user->info->update(['phone_verified_at' => now()]);

        return $this->response->noContent();
    }

    /**
     * @OA\Patch(
     *      path="/backstage/users/{user}/verify_email",
     *      operationId="backstage.users.verify_email",
     *      tags={"Backstage-会员"},
     *      summary="后台验证邮箱",
     *      @OA\Parameter(name="user", in="path", description="会员ID", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=204,
     *          description="No Content"
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       @OA\Response(response=422, description="验证不通过"),
     *       security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function verifyEmail(User $user)
    {
        if ($user->info->email_verified_at) {
            return $this->response->error('Email has been verified', 422);
        }

        $user->info->update(['email_verified_at' => now()]);

        return $this->response->noContent();
    }

    /**
     * @OA\Patch(
     *      path="/backstage/users/{user}/verify_bank_account",
     *      operationId="backstage.users.verify_bank_account",
     *      tags={"Backstage-会员"},
     *      summary="后台验证银行卡",
     *      @OA\Parameter(name="user", in="path", description="会员ID", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=204,
     *          description="No Content"
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       @OA\Response(response=422, description="验证不通过"),
     *       security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function verifyBankAccount(User $user)
    {
        if ($user->info->bank_account_verified_at) {
            return $this->response->error('Bank Account has been verified', 422);
        }

        $user->info->update(['bank_account_verified_at' => now()]);

        return $this->response->noContent();
    }

    /**
     * @OA\Patch(
     *      path="/backstage/game_platform_users/{game_platform_user}/balance_status",
     *      operationId="backstage.game_platform_user.balance_status",
     *      tags={"Backstage-会员"},
     *      summary="更改会员第三方钱包状态",
     *       @OA\Parameter(
     *         name="game_platform_user",
     *         in="path",
     *         description="第三方会员ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="status", type="integer", description="状态"),
     *                  required={"status"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="successful operation"
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function updateGameWalletStatus(GamePlatformUser $gamePlatformUser, UserRequest $request, GamePlatformService $service)
    {
        $status = $request->status;

        try {
            DB::transaction(function () use ($gamePlatformUser, $service, $status) {
                $gamePlatformUser->updateBalanceStatus($status);

                # 如果是锁钱包，直接踢出会员
                if (!$status) {
                    $service->kickOut($gamePlatformUser->user, $gamePlatformUser->platform);
                }
            });
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 422);
        }

        return $this->response->item($gamePlatformUser, new GamePlatformUserTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/users/{user}/audit",
     *      operationId="backstage.users.audit",
     *      tags={"Backstage-会员"},
     *      summary="会员信息修改记录",
     *      @OA\Parameter(name="user", in="path", description="会员ID", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="field", in="query", description="查询字段", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Audit"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function audit(User $user, Request $request)
    {
        $field = $request->field;
        if (in_array($field, $user->getAuditFields())) {
            $audits = $user->audits()->whereRaw("FIND_IN_SET(?, tags)", $field)->get();
        } else {
            $audits = $user->info->audits()->whereRaw("FIND_IN_SET(?, tags)", $field)->get();
        }

        foreach ($audits as $audit) {
            $audit->new_value = UserRepository::transformAudit($field, $audit->new_values[$field]);
            $audit->old_value = UserRepository::transformAudit($field, $audit->old_values[$field]);
            $audit->new_value = $field != 'security_question' ? $audit->new_value : 'Q:' . $audit->new_value . ' A:' . $audit->new_values['security_question_answer'];
            $audit->old_value = $field != 'security_question' ? $audit->old_value : 'Q:' . $audit->old_value . ' A:' . $audit->old_values['security_question_answer'];
        }

        return $this->response->collection($audits, new AuditTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/users/{user}/reset_security_question",
     *      operationId="backstage.users.reset_security_question",
     *      tags={"Backstage-会员"},
     *      summary="重置会员密保问题",
     *      @OA\Parameter(name="user", in="path", description="会员ID", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *                  required={"remark"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="No Content",
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function resetSecurityQuestion(User $user, UserRequest $request)
    {
        $user->update([
            'security_question'        => '',
            'security_question_answer' => '',
        ]);

        # 添加remark
        ProfileRemark::add($user->id, ProfileRemark::CATEGORY_ACCOUNT, $request->remark, $this->user->name);

        return $this->response->noContent();
    }

    /**
     * @OA\Post(
     *     path="/backstage/users/{user}/claim_verify_prize",
     *     operationId="backstage.users.claim_verify_prize",
     *     tags={"Backstage-会员"},
     *     summary="领取资料验证奖励",
     *     @OA\Parameter(name="user", in="path", description="会员ID", @OA\Schema(type="integer")),
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
    public function claimVerifyPrize(User $user, UserRequest $request, UserService $userService)
    {
        try {
            $user = $userService->claimVerifyPrize($user, $request->platform_code, $this->user);
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 422);
        }

        return $this->response->item($user, new UserTransformer());
    }
}
