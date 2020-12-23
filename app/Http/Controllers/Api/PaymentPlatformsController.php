<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Config;
use App\Models\PaymentGroup;
use App\Models\PaymentPlatform;
use App\Transformers\PaymentPlatformSimpleTransformer;
use App\Transformers\PaymentPlatformMenuTransformer;
use App\Repositories\PaymentPlatformRepository;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;

class PaymentPlatformsController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/payment_platforms?include=companyBankAccount,companyBankAccount.bank",
     *      operationId="api.payment_platforms.index",
     *      tags={"Api-充值渠道"},
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
    public function index()
    {
        $userPaymentGroup = $this->user()->paymentGroup;
        $paymentPlatforms = PaymentPlatformRepository::getByCurrencies([$this->user()->currency]);
        $impose           = Config::findValue('impose_deposit_channel', false);

        $ORM = QueryBuilder::for($paymentPlatforms)
            ->allowedFilters(
                Filter::scope('id'),
                Filter::exact('payment_type')
            );

        if ($impose) {
            # viet-192 用户充值增加支付组限制和筛选
            $userPaymentGroupCodes = $userPaymentGroup->account_code;
            $paymentPlatforms      = $ORM->whereIn('code', $userPaymentGroupCodes)->get();
        } else {
            $paymentPlatforms = $ORM->get();
        }

        # 暂时筛选
        $paymentPlatforms = $paymentPlatforms->filter(function ($platform) {

            if ($platform->companyBankAccount && $platform->companyBankAccount->isInactive()) {
                return false;
            }

            if (!$platform->isActive()) {
                return false;
            }

            return true;
        });

        $paymentGroups = PaymentGroup::all()->toArray();

        return $this->response->collection($paymentPlatforms, new PaymentPlatformSimpleTransformer('front', [
            'user'          => $this->user(),
            'currency'      => $this->user()->currency,
            'payment_group' => $paymentGroups,
        ]));
    }

    /**
     * @OA\Get(
     *      path="/payment_platforms/all",
     *      operationId="api.payment_platforms.all",
     *      tags={"Api-充值渠道"},
     *      summary="充值渠道纯显示列表",
     *      @OA\Parameter(name="currency", in="query", description="币别", @OA\Schema(type="string")),
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
    public function all(Request $request)
    {
        $currency         = $request->header('currency');
        $paymentPlatforms = PaymentPlatformRepository::getByCurrencies([$currency]);

        $paymentPlatforms = $paymentPlatforms->get();

        # 暂时筛选
        $bankCodes = [];

        $paymentPlatforms = $paymentPlatforms->filter(function ($platform) use ($bankCodes) {

            if (!$platform->isActive()) {
                return false;
            }

            if ($platform->companyBankAccount) {
                if ($platform->companyBankAccount->isInactive()) {
                    return false;
                } else {
                    if (in_array($platform->companyBankAccount->bank_code, $bankCodes)) {
                        return false;
                    } else {
                        $bankCodes[] = $platform->companyBankAccount->bank_code;
                        return true;
                    }
                }
            }

            return true;
        });
        return $this->response->collection($paymentPlatforms, new PaymentPlatformSimpleTransformer('all'));
    }

    /**
     * @OA\Get(
     *      path="/payment_platforms/menu",
     *      operationId="api.payment_platforms.menu",
     *      tags={"Api-充值渠道"},
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
        $showType              = $request->input('show_type', PaymentPlatform::SHOW_TYPE_ALL);
        $paymentPlatformsBank  = PaymentPlatformRepository::getByCurrencies([$this->user()->currency])
            ->whereIn('show_type', PaymentPlatform::$showTypeMapping[$showType])
            ->where("payment_type", PaymentPlatform::PAYMENT_TYPE_BANKCARD)
            ->where('status', true)->take(1)->get();
        $paymentPlatformsOther = PaymentPlatformRepository::getByCurrencies([$this->user()->currency])
            ->whereIn('show_type', PaymentPlatform::$showTypeMapping[$showType])
            ->where("payment_type", '<>', PaymentPlatform::PAYMENT_TYPE_BANKCARD)
            ->where('status', true)->get();

        $paymentPlatforms = $paymentPlatformsBank->merge($paymentPlatformsOther)->sortByDesc('sort');
        $workAsLimit      = Config::findValue('impose_deposit_channel', false);

        $userPaymentGroup = $this->user->paymentGroup;
        if ($userPaymentGroup) {
            $userShowAllowUsePaymentCodes = $userPaymentGroup->account_code ?? [];
        } else {
            $userShowAllowUsePaymentCodes = [];
        }

        if ($workAsLimit) {
            // 增加 支付组别筛选
            $paymentPlatforms = $paymentPlatforms->whereIn('code', $userShowAllowUsePaymentCodes);
        }

        return $this->response->collection($paymentPlatforms, new PaymentPlatformMenuTransformer());
    }
}
