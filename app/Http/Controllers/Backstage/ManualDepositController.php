<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Requests\Backstage\CheckUsernameRequest;
use App\Http\Requests\Backstage\ManualDepositRequest;
use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\PaymentPlatformRequest;
use App\Models\DepositLog;
use App\Models\PaymentPlatform;
use App\Models\User;
use App\Repositories\DepositRepository;
use App\Repositories\ImageRepository;
use App\Repositories\PaymentPlatformRepository;
use App\Repositories\UserRepository;
use App\Transformers\PaymentPlatformMenuTransformer;
use App\Transformers\PaymentPlatformSimpleTransformer;
use App\Transformers\UserBankAccountTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\DepositService;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class ManualDepositController extends BackstageController
{
    /**
     * @OA\Post(
     *      path="/backstage/manual/deposits",
     *      operationId="backstage.manual.deposits.store",
     *      tags={"Backstage-充值"},
     *      summary="后台手动充值",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="user_name", type="string", description="会员名"),
     *                  @OA\Property(property="payment_type", type="integer", description="支付类型"),
     *                  @OA\Property(property="payment_platform_id", type="integer", description="支付平台id"),
     *                  @OA\Property(property="amount", type="number", description="充值金额"),
     *                  @OA\Property(property="online_banking_channel", type="number", description="银行卡支付渠道"),
     *                  @OA\Property(property="company_bank_account_id", type="integer", description="公司银行卡id"),
     *                  @OA\Property(property="deposit_date", type="string", description="支付日期"),
     *                  @OA\Property(property="user_bank_account_id", type="string", description="会员银行卡"),
     *                  @OA\Property(property="user_bank_account_name", type="string", description="会员银行帐户"),
     *                  @OA\Property(property="user_bank_id", type="string", description="会员银行id"),
     *                  @OA\Property(property="receipts", type="string", description="凭证图片id(,逗号分割)"),
     *                  @OA\Property(property="reference_id", type="string", description="银行回应码"),
     *                  @OA\Property(property="user_mpay_number", type="string", description="会员Mpay号码"),
     *                  @OA\Property(property="network", type="string", description="点数卡电信类型"),
     *                  @OA\Property(property="pin_number", type="string", description="点数卡PIN NUMBER"),
     *                  @OA\Property(property="serial_number", type="string", description="点数卡SERIAL NUMBER"),
     *                  required={"payment_type", "payment_platform_id", "amount"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Deposit"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function store(ManualDepositRequest $request)
    {
        $isAgent = $request->is_agent ?? false;
        if ($isAgent) {
            $user = UserRepository::findAffiliateByName($request->user_name);
        } else {
            $user = UserRepository::findByName($request->user_name);
        }

        if (!$user) {
            return $this->response->error('no user.', 422);
        }

        $data            = $request->input();
        $data['user_ip'] = $request->getClientIp();
        $data['device']  = $request->header('device');
        if (isset($data['amount'])) {
            $data['amount'] = format_number($data['amount'], 2);
        } else {
            $data['amount'] = 0;
        }
        DepositRepository::checkDepositLimit($user->currency, $data['payment_platform_id'], $data['amount']);
        DepositRepository::checkDepositPendingLimit($user);
        $result = DB::transaction(function () use ($data, $user) {
            $service = new DepositService($data['payment_platform_id']);
            $result  = $service->deposit($user, $data);

            $deposit = $result['deposit'];
            # 关联图片
            if (isset($data["receipts"])) {
                $receipts_array = explode(',', $data['receipts']);
                ImageRepository::updatePatch($this->user, $receipts_array, $deposit);
            }
            DepositLog::add($this->user()->name, $deposit->id, DepositLog::TYPE_CREATED);
            $result['deposit_id'] = $deposit->id;
            unset($result['deposit']);

            return $result;
        });

        return $this->response->array($result);
    }

    /**
     * @OA\Get(
     *      path="/backstage/payment_platform/menus",
     *      operationId="backstage.payment_platform.menus",
     *      tags={"Backstage-充值渠道"},
     *      summary="充值渠道菜单",
     *      @OA\Parameter(name="currency", in="header", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="show_type", in="query", description="显示类型 1:ALL 2:USER 3:AFFILIATE", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/PaymentPlatformMenu"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function menu(Request $request)
    {
        $showType = $request->input('show_type', PaymentPlatform::SHOW_TYPE_ALL);
        $paymentPlatformsBank = PaymentPlatformRepository::getByCurrencies([$request->currency])
            ->whereIn('show_type', PaymentPlatform::$showTypeMapping[$showType])
            ->where("payment_type", PaymentPlatform::PAYMENT_TYPE_BANKCARD)
            ->where('status', true)->take(1)->get();
        $paymentPlatformsOther = PaymentPlatformRepository::getByCurrencies([$request->currency])
            ->whereIn('show_type', PaymentPlatform::$showTypeMapping[$showType])
            ->whereIn("payment_type", [PaymentPlatform::PAYMENT_TYPE_SCRATCH_CARD, PaymentPlatform::PAYMENT_TYPE_MPAY, PaymentPlatform::PAYMENT_TYPE_LINEPAY])
            ->where('status', true)->get();

        $paymentPlatforms = $paymentPlatformsBank->merge($paymentPlatformsOther)->sortByDesc('sort');

        return $this->response->collection($paymentPlatforms, new PaymentPlatformMenuTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/payment_platform/index?include=companyBankAccount,companyBankAccount.bank",
     *      operationId="backstage.payment_platforms.payment_platforms",
     *      tags={"Backstage-充值渠道"},
     *      summary="充值渠道列表",
     *      @OA\Parameter(name="currency", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[id]", in="query", description="渠道id", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[payment_type]", in="query", description="渠道类型", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/PaymentPlatformSimple"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function paymentPlatform(PaymentPlatformRequest $request)
    {
        $paymentPlatforms = PaymentPlatformRepository::getByCurrencies([$request->currency]);
        $paymentPlatforms = QueryBuilder::for($paymentPlatforms)
            ->where('status', true)
            ->allowedFilters(
                Filter::scope('id'),
                Filter::exact('payment_type')
            )->get();
        # 暂时筛选
        $paymentPlatforms = $paymentPlatforms->filter(function($platform) {

            if ($platform->companyBankAccount && $platform->companyBankAccount->isInactive()) {
                return false;
            }

            return true;
        });

        return $this->response->collection($paymentPlatforms, new PaymentPlatformSimpleTransformer('front'));
    }

    /**
     * @OA\Post(
     *      path="/backstage/check/username",
     *      operationId="backstage.check.username",
     *      tags={"Backstage-充值"},
     *      summary="检查用户名，获取用户信息，充值需要",
     *      @OA\Parameter(name="is_agent", in="query", description="是否是代理， 不传则是会员", @OA\Schema(type="boolean")),
     *      @OA\Parameter(name="name", in="query", description="用户名", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/User"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function checkUsername(CheckUsernameRequest $request)
    {
        $isAgent = $request->is_agent ?? false;
        if ($isAgent) {
            $user = UserRepository::findAffiliateByName($request->name);
        } else {
            $user = UserRepository::findByName($request->name);
        }
        return $this->response->item($user, new UserTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/get/user/bank/{user}",
     *      operationId="backstage.get.userBank",
     *      tags={"Backstage-充值"},
     *      summary="检查用户名，获取用户信息，充值需要",
     *      @OA\Parameter(name="user", in="path", description="用户ID", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/UserBankAccount"),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function getUserBank(User $user)
    {
        $userBankAccounts = $user->bankAccounts()->active()->latest('last_used_at')->get();

        if (!$userBankAccounts) {
            return $this->response->noContent();
        }

        return $this->response->collection($userBankAccounts, new UserBankAccountTransformer('affiliate_bank_account'));
    }
}
