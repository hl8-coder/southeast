<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\DepositRequest;
use App\Jobs\AutoDepositJob;
use App\Models\Deposit;
use App\Repositories\DepositRepository;
use App\Services\DepositService;
use App\Repositories\CompanyBankAccountRepository;
use App\Transformers\DepositTransformer;
use App\Transformers\CompanyBankAccountTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Filter;

class DepositsController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/deposits",
     *      operationId="api.deposits.index",
     *      tags={"Api-充值"},
     *      summary="充值列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
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
    public function index(Request $request)
    {
        $deposits = QueryBuilder::for(Deposit::class)
            ->where('user_id', $this->user()->id)
            ->allowedFilters(Filter::scope('start_at'), Filter::scope('end_at'))
            ->latest()
            ->paginate($request->per_page);

        return $this->response->paginator($deposits, new DepositTransformer());
    }

    /**
     * @OA\Post(
     *      path="/deposits",
     *      operationId="api.deposits.store",
     *      tags={"Api-充值"},
     *      summary="发起充值",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
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
    public function store(DepositRequest $request)
    {
        $data = $request->input();
        $data['user_ip'] = $request->getClientIp();
        $data['device'] = $request->header('device');
        # 截取2位小数
        if(isset($data['amount'])) {
            $data['amount'] = format_number($data['amount'], 2);
        } else {
            $data['amount'] = 0;
        }
        DepositRepository::checkDepositLimit($this->user->currency, $data['payment_platform_id'], $data['amount']);
        DepositRepository::checkDepositPendingLimit($this->user);

        $deposit = null;
        $result = DB::transaction(function() use ($data, &$deposit) {
            $service = new DepositService($data['payment_platform_id']);
            $result = $service->deposit($this->user, $data);

            $deposit = $result['deposit'];
            $result['deposit_id'] = $deposit->id;
            unset($result['deposit']);

            return $result;
        });

        dispatch(new AutoDepositJob($deposit))->onQueue('auto_deposit');

        return $this->response->array($result);
    }

    /**
     * @OA\Get(
     *      path="/deposits/company_bank_accounts",
     *      operationId="api.deposits.company_bank_accounts.index",
     *      tags={"Api-充值"},
     *      summary="公司银行卡列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/CompanyBankAccount"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function companyBankAccounts(Request $request)
    {
        # payment group 如果未设定使用预设值(未完全完成)
        $companyBankAccounts = CompanyBankAccountRepository::getDepositAccounts($this->user->payment_group_id);

        return $this->response->collection($companyBankAccounts, new CompanyBankAccountTransformer());
    }
}
