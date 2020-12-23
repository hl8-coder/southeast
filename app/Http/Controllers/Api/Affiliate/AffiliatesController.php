<?php

namespace App\Http\Controllers\Api\Affiliate;

use App\Http\Requests\Api\UserBankAccountRequest;
use App\Models\Affiliate;
use App\Models\AffiliateCommission;
use App\Models\MailboxTemplate;
use App\Models\TurnoverRequirement;
use App\Models\User;
use App\Jobs\SendEmailJob;
use App\Models\Transaction;
use App\Services\UserService;
use App\Models\TransferDetail;
use App\Models\UserBankAccount;
use App\Transformers\AffiliateTransformer;
use Illuminate\Support\Facades\DB;
use App\Jobs\TransactionProcessJob;
use App\Repositories\UserRepository;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;
use App\Transformers\UserTransformer;
use App\Http\Requests\Api\UserRequest;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\AffiliateRequest;
use App\Transformers\UserBankAccountTransformer;
use Spatie\QueryBuilder\QueryBuilder;

class AffiliatesController extends ApiController
{
    /**
     * @OA\Post(
     *      path="/affiliates?include=info,account,affiliate",
     *      operationId="api.affiliates.store",
     *      tags={"Affiliate-代理"},
     *      summary="代理注册",
     *      @OA\Parameter(name="currency", in="header", description="币别", required=true, @OA\Schema(type="string")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="currency", type="string", description="币别"),
     *                  @OA\Property(property="name", type="string", description="账号"),
     *                  @OA\Property(property="gender", type="string", description="性别"),
     *                  @OA\Property(property="password", type="string", format="password", description="密码"),
     *                  @OA\Property(property="password_confirmation", type="string", format="password", description="确认密码"),
     *                  @OA\Property(property="phone", type="string", description="电话号码"),
     *                  @OA\Property(property="full_name", type="string", description="姓名"),
     *                  @OA\Property(property="email", type="string", description="邮箱"),
     *                  @OA\Property(property="birth_at", type="string", description="生日", format="date"),
     *                  @OA\Property(property="address", type="string", description="地址"),
     *                  @OA\Property(property="country_code", type="string", description="电话国际代码"),
     *                  @OA\Property(property="other_contact", type="string", description="其他联系方式"),
     *                  @OA\Property(property="affiliate_id", type="string", description="上級代理推薦碼"),
     *                  required={"name", "email", "phone", "full_name", "birth_at", "country_code", "password", "password_confirmation"}
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
     * )
     */
    public function store(AffiliateRequest $request, UserService $service)
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
            'gender',
            'currency',
        ]);
        $data['is_agent'] = true;
        try {
            $user = DB::transaction(function () use ($service, $data, $request) {
                $registerUrl = app()->isLocal() ? $request->root() : $request->header('DomainFE');
                return $service->store($data, $request->getClientIp(), $registerUrl);
            });
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 422);
        }

        $token = Auth::guard('api')->fromUser($user);

        # 更新最后登录信息
        $user->info->updateLastLogin($request->getClientIp(), $token, $request->header('device'));

        return $this->response->item($user->refresh(), new UserTransformer('affiliate_show'))
            ->setStatusCode(201);
    }

    /**
     * @OA\Patch(
     *      path="/affiliates/forget_password",
     *      operationId="api.affiliates.forget_password",
     *      tags={"Affiliate-代理"},
     *      summary="找回密码",
     *      @OA\Parameter(name="currency", in="header", description="币别", required=true, @OA\Schema(type="string")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="name", type="string", description="会员名称"),
     *                  @OA\Property(property="email", type="string", description="邮箱"),
     *                  required={"name", "email"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=204, description="No Content",),
     *      @OA\Response(response=404, description="Not Found"),
     *      @OA\Response(response=422, description="验证失败"),
     * )
     */
    public function forgetPassword(AffiliateRequest $request)
    {
        $user = UserRepository::findAffiliateByName($request->name);

        $email = $user->info->email;

        if($email != $request->email) {
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
                dispatch(new SendEmailJob(MailboxTemplate::FORGET_PASSWORD, $email, $user->currency, $user->is_agent, $user->language, $user->source_password, $user->name))->onQueue('send_email');
            });
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 422);
        }

        return $this->response->noContent();
    }

    /**
     * @OA\Get(
     *      path="/affiliate?include=info,account,affiliate",
     *      operationId="api.affiliates.me",
     *      tags={"Affiliate-代理"},
     *      summary="获取代理信息",
     *      @OA\Parameter(name="currency", in="header", description="币别", required=true, @OA\Schema(type="string")),
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
     * @OA\Post(
     *      path="/affiliate/transfer/{user}",
     *      operationId="api.affiliates.transfer",
     *      tags={"Affiliate-代理"},
     *      summary="代理转账",
     *      @OA\Parameter(name="currency", in="header", description="币别", required=true, @OA\Schema(type="string")),
     *      @OA\Parameter(name="user", in="path", description="会员id", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="amount", type="integer", description="转账金额"),
     *                  required={"amount"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=204, description="No Content"),
     *      @OA\Response(response=404, description="Not Found"),
     *      @OA\Response(response=422, description="验证失败"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function transfer(User $user, UserRequest $request, TransactionService $transactionService)
    {
        $affiliate = $this->user();

        # 判断被转账是否是代理的直属下级
        if (!$affiliate->isDirectChild($user)) {
            return $this->response->error('not parent', 422);
        }

        $amount = $request->amount;
        # 判断账户余额
        if ($affiliate->account->getAvailableBalance() < $amount) {
            error_response(422, __('userAccount.BALANCE_NOT_ENOUGH'));
        }

        # 添加转账记录
        $detail = TransferDetail::add($affiliate, $user, $amount);

        # 发起转账
        try {
            $transactions = DB::transaction(function () use ($affiliate, $user, $detail, $transactionService) {
                if ($detail->success()) {
                    # 扣钱
                    $fromTransaction = $transactionService->addTransaction(
                        $affiliate,
                        $detail->amount,
                        Transaction::TYPE_AFFILIATE_TRANSFER_OUT,
                        $detail->id,
                        $detail->order_no
                    );

                    # 更新帐变前后余额
                    $detail->update([
                        'from_before_balance' => $fromTransaction->before_balance,
                        'from_after_balance'  => $fromTransaction->after_balance,
                    ]);

                    # 加钱
                    $toTransaction = $transactionService->addTransaction(
                        $user,
                        $detail->amount,
                        Transaction::TYPE_AFFILIATE_TRANSFER_IN,
                        $detail->id,
                        $detail->order_no
                    );

                    # 添加流水要求
                    if ($user->isUser()) {
                        TurnoverRequirement::add($detail, $detail->is_turnover_closed, $detail->to_user_id);
                    }

                    return [
                        $fromTransaction,
                        $toTransaction,
                    ];
                }

                return [];
            });
        } catch (\Exception $e) {
            $detail->fail(
                str_limit($e->getMessage(), 1024, '...')
            );
            return $this->response->error('transfer fail', 422);
        }

        foreach ($transactions as $transaction) {

            dispatch(new TransactionProcessJob($transaction))->onQueue('balance');
        }

        return $this->response->noContent();
    }

    /**
     * @OA\Patch(
     *      path="/affiliate?include=info,account,vip,reward",
     *      operationId="api.affiliates.users.update",
     *      tags={"Affiliate-代理"},
     *      summary="代理更新资料",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="language", type="string", description="语言"),
     *                  @OA\Property(property="city", type="string", description="城市"),
     *                  @OA\Property(property="address", type="string", description="地址更新"),
     *                  @OA\Property(property="web_url", type="string", description="网站地址"),
     *                  @OA\Property(property="security_question", type="integer", description="密保问题"),
     *                  @OA\Property(property="security_question_answer", type="string", description="密保问题回答"),
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
    public function updateProfile(UserRequest $request)
    {
        $user = $this->user();
        $data = $request->only(['security_question', 'security_question_answer', 'language']);
        $data = remove_null($data);
        $user->update($data);

        $info = $request->only(['city', 'address', 'web_url']);
        $info = remove_null($info);
        $user->info->update($info);

        UserRepository::checkProfileVerified($user);

        return $this->response->item($user, new UserTransformer('front_show'));
    }

    /**
     * @OA\Get(
     *      path="/affiliate/user_bank_accounts?include=bank",
     *      operationId="api.affiliates.user_bank_accounts.store",
     *      tags={"Affiliate-代理"},
     *      summary="代理银行卡",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/UserBankAccount"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function affiliateBank()
    {
        $userBankAccounts = $this->user()->bankAccounts()->active()->latest('last_used_at')->get();

        if (!$userBankAccounts) {
            return $this->response->noContent();
        }

        return $this->response->collection($userBankAccounts, new UserBankAccountTransformer('affiliate_bank_account'));
    }

    /**
     * @OA\Post(
     *      path="/affiliate/user_bank_accounts/store?include=bank",
     *      operationId="api.affiliates.user_bank_accounts.store",
     *      tags={"Affiliate-代理"},
     *      summary="代理添加银行卡",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="bank_id", type="integer", description="银行id"),
     *                  @OA\Property(property="province", type="string", description="省"),
     *                  @OA\Property(property="city", type="string", description="市"),
     *                  @OA\Property(property="branch", type="string", description="支行"),
     *                  @OA\Property(property="account_name", type="string", description="户名"),
     *                  @OA\Property(property="account_no", type="string", description="卡号"),
     *                  @OA\Property(property="is_preferred", type="string", description="是否首選"),
     *                  required={"bank_id", "province", "city", "branch", "account_name", "account_no"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/UserBankAccount"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function storeBank(UserBankAccountRequest $request)
    {
        $user = $this->user();
        $this->checkCanChangeBank($user);
        $userBankAccounts = $this->user()->bankAccounts()->active()->latest('last_used_at')->first();
        if (!empty($userBankAccounts)) {
            return $this->response->error(__('affiliate.YOU_HAVE_ALREADY_ADDED_A_BANK_CARD'), 422);
        }
        $data = $request->all();

        // 如果设定首选，将取消其它银行卡首选设定
        if($data["is_preferred"]) {
            $this->user->bankAccounts()->update(["is_preferred"=>0]);
        }

        $userBankAccount = $this->user->bankAccounts()->create($data);

        return $this->response->item($userBankAccount, new UserBankAccountTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Post(
     *      path="/affiliate/user_bank_accounts/{user_bank_account}/update?include=bank",
     *      operationId="api.affiliates.user_bank_accounts.update",
     *      tags={"Affiliate-代理"},
     *      summary="代理更新银行卡",
     *      @OA\Parameter(
     *         name="user_bank_account",
     *         in="path",
     *         description="会员银行卡id",
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
     *                  @OA\Property(property="bank_id", type="integer", description="银行id"),
     *                  @OA\Property(property="province", type="string", description="省"),
     *                  @OA\Property(property="city", type="string", description="市"),
     *                  @OA\Property(property="branch", type="string", description="支行"),
     *                  @OA\Property(property="account_name", type="string", description="户名"),
     *                  @OA\Property(property="account_no", type="string", description="卡号"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/UserBankAccount"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function updateBank(UserBankAccount $userBankAccount, UserBankAccountRequest $request)
    {
        $user = $this->user();
        $this->checkCanChangeBank($user);
        $this->authorize('own', $userBankAccount);

        if (!$userBankAccount->isActive()) {
            return $this->response->error(__('userAccount.BANK_ACCOUNT_NOT_EXISTS'), 422);
        }

        $data = $request->only([
            'bank_id', 'province', 'city', 'branch', 'account_name', 'account_no',
        ]);
        $data = remove_null($data);

        if ($userBankAccount->accountExists($data['account_no'], $user->id)) {
            return $this->response->error(__('request/api/userbankaccount.account_is_exists'), 422);
        }

        $userBankAccount->update($data);

        return $this->response->item($userBankAccount, new UserBankAccountTransformer());
    }

    /**
     * @OA\Delete(
     *      path="/affiliate/user_bank_accounts/{user_bank_account}/delete",
     *      operationId="api.affiliates.user_bank_accounts.destroy",
     *      tags={"Affiliate-代理"},
     *      summary="代理删除银行卡",
     *      @OA\Parameter(name="currency", in="header", description="币别", required=true, @OA\Schema(type="string")),
     *      @OA\Parameter(name="user_bank_account", in="path", description="银行卡id", @OA\Schema(type="integer")),
     *      @OA\Response(response=204, description="No Content"),
     *      @OA\Response(response=404, description="Not Found"),
     *      @OA\Response(response=422, description="验证失败"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function destroyUserBankAccount(UserBankAccount $userBankAccount)
    {
        $user = $this->user();
        $this->checkCanChangeBank($user);
        $this->authorize('own', $userBankAccount);

        $userBankAccount->delete();

        return $this->response->noContent();
    }

    public function checkCanChangeBank($user)
    {
        $day = date('d');
        if ($day > 28) {
            return $this->response->error(__('userAccount.CAN_NOT_CHANGE'), 431);
        }
        # 获取上个月的流水
        $comm = AffiliateCommission::query()->where('user_id', $user->id)->latest('end_at')->first();
        if ((!$comm || $comm->status != AffiliateCommission::STATUS_RELEASE) && $day < 7) {
            return $this->response->error(__('userAccount.CAN_NOT_CHANGE'), 431);
        }
    }



    /**
     * @OA\Get(
     *      path="/affiliate/random_show?include=userInfo",
     *      operationId="api.affiliates.random_show",
     *      tags={"Affiliate-代理"},
     *      summary="随机展示代理信息",
     *      @OA\Response(response=204, description="No Content"),
     *      @OA\Response(response=404, description="Not Found"),
     *      @OA\Response(response=422, description="验证失败"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function randomShow()
    {
        $affiliates = QueryBuilder::for(Affiliate::class)
            ->where("cs_status", Affiliate::CS_STATUS_APPROVED)
            ->status(User::STATUS_ACTIVE)
            ->describeSwitchLanguage(app()->getLocale())
            ->inRandomOrder()->limit(6)->get();
        return $this->response->collection($affiliates, new AffiliateTransformer('front_index'));
    }
}
