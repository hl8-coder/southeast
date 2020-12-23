<?php

namespace App\Http\Controllers\Backstage;

use App\Models\Image;
use App\Models\PaymentPlatform;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Controllers\BackstageController;
use App\Transformers\PaymentPlatformTransformer;
use App\Http\Requests\Backstage\PaymentPlatformRequest;

class PaymentPlatformsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/payment_platforms",
     *      operationId="backstage.payment_platform.index",
     *      tags={"Backstage-支付通道"},
     *      summary="支付通道列表",
     *      @OA\Parameter(name="filter[name]", in="query", description="名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[code]", in="query", description="平台代号", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[currencies]", in="query", description="类型", @OA\Schema(type="币别")),
     *      @OA\Parameter(name="filter[is_fee]", in="query", description="是否需要手续费", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/PaymentPlatform"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *         {"bearer": {}}
     *      }
     *  )
     */
    public function index(PaymentPlatformRequest $request)
    {
        $paymentPlatforms = QueryBuilder::for(PaymentPlatform::class)
            ->allowedFilters([
                Filter::exact('status'),
                'name',
                Filter::exact('code'),
                Filter::scope('currencies'),
                Filter::exact('is_fee'),
                Filter::exact('show_type'),
            ])
            ->latest('sort')
            ->paginate($request->per_page);

        return $this->response->paginator($paymentPlatforms, new PaymentPlatformTransformer());
    }


    /**
     * @OA\Get(
     *      path="/backstage/payment_platforms/{payment_platform}",
     *      operationId="backstage.payment_platforms.show",
     *      tags={"Backstage-支付通道"},
     *      summary="支付通道详情",
     *      @OA\Parameter(name="payment_platform", in="path", description="支付平台ID", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/PaymentPlatform"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *         {"bearer": {}}
     *      }
     *  )
     */
    public function show(PaymentPlatform $paymentPlatform)
    {
        return $this->response->item($paymentPlatform, new PaymentPlatformTransformer());
    }


    /**
     * @OA\Post(
     *      path="/backstage/payment_platforms",
     *      operationId="backstage.payment_platforms.store",
     *      tags={"Backstage-支付通道"},
     *      summary="添加支付通道",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="name", type="string", description="名称"),
     *                  @OA\Property(property="display_name", type="string", description="名称"),
     *                  @OA\Property(property="code", type="string", description="辨识码"),
     *                  @OA\Property(property="remarks", type="string", description="备注"),
     *                  @OA\Property(property="currencies", type="string", description="可用币别"),
     *                  @OA\Property(property="devices", type="array", description="支持设备", @OA\Items()),
     *                  @OA\Property(property="payment_type", type="integer", description="支付类型"),
     *                  @OA\Property(property="related_name", type="string", description="关联名称"),
     *                  @OA\Property(property="related_no", type="string", description="关联号码"),
     *                  @OA\Property(property="customer_id", type="string", description="商户id"),
     *                  @OA\Property(property="customer_key", type="string", description="商户私钥"),
     *                  @OA\Property(property="request_url", type="string", description="请求地址"),
     *                  @OA\Property(property="request_type", type="integer", description="请求类型"),
     *                  @OA\Property(property="max_deposit", type="integer", description="单笔最大充值金额"),
     *                  @OA\Property(property="min_deposit", type="integer", description="单笔最低充值金额"),
     *                  @OA\Property(property="max_fee", type="integer", description="最大手续费"),
     *                  @OA\Property(property="min_fee", type="integer", description="最小手续费"),
     *                  @OA\Property(property="is_need_type_amount", type="boolean", description="是否需要输入金额"),
     *                  @OA\Property(property="is_fee", type="boolean", description="是否需要手续费"),
     *                  @OA\Property(property="fee_rebate", type="integer", description="充值手续费百分比"),
     *                  @OA\Property(property="image_id", type="string", description="图片地址id"),
     *                  @OA\Property(property="show_type", type="integer", description="显示类型 1:All 2:User 3:Affiliate"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  @OA\Property(property="status", type="integer", description="状态"),
     *
     *                  required={"currencies", "name", "request_type", "payment_type", "request_url", "display_name", "code"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/PaymentPlatform"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function store(PaymentPlatformRequest $request, PaymentPlatform $paymentPlatform)
    {
        $data = remove_null($request->all());

        if (isset($data['image_id'])){
            unset($data['image_id']);
        }
        if ($request->image_id){
            $data['image_path'] = Image::find($request->image_id)->path;
        }
        $data['fee_rebate'] = $data['fee_rebate']/100;
        $data['currencies'] = implode(",", $request->currencies);

        try {
            // DB::transaction(function() use ($data, $paymentPlatform) {
                # 创建支付平台
                $paymentPlatformData = collect($data)->except(['banks'])->toArray();
                $paymentPlatform->fill($paymentPlatformData);
                $paymentPlatform->save();

                # 创建关联银行
                // if ($banks = collect($data)->only(['banks'])) {
                //     $banks = array_column($banks['banks'], null, 'bank_id');
                //     $paymentPlatform->banks()->sync($banks);
                // }

                // return $paymentPlatform;
            // });
        } catch (\Exception $e) {
            return $this->response->errorInternal('Creation failed');
        }

        return $this->response->item($paymentPlatform, new PaymentPlatformTransformer());
    }


    /**
     * @OA\Patch(
     *      path="/backstage/payment_platforms/{payment_platform}",
     *      operationId="backstage.payment_platform.update",
     *      tags={"Backstage-支付通道"},
     *      summary="更新支付通道",
     *      @OA\Parameter(
     *         name="PaymentPlatform",
     *         in="path",
     *         description="调整id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="name", type="string", description="支付平台名称"),
     *                  @OA\Property(property="devices", type="array", description="支持设备", @OA\Items()),
     *                  @OA\Property(property="currencies", type="string", description="适用币别"),
     *                  @OA\Property(property="payment_type", type="int", description="支付类型"),
     *                  @OA\Property(property="customer_id", type="string", description="商户ID"),
     *                  @OA\Property(property="customer_key", type="string", description="商户密钥"),
     *                  @OA\Property(property="request_url", type="string", description="请求地址"),
     *                  @OA\Property(property="request_type", type="int", description="请求类型"),
     *                  @OA\Property(property="is_need_type_amount", type="int", description="是否需要输入金额"),
     *                  @OA\Property(property="related_name", type="string", description="关联名称"),
     *                  @OA\Property(property="related_no", type="string", description="关联号码"),
     *                  @OA\Property(property="max_deposit", type="integer", description="单笔最大充值金额"),
     *                  @OA\Property(property="min_deposit", type="integer", description="单笔最低充值金额"),
     *                  @OA\Property(property="is_fee", type="boolean", description="是否需要手续费"),
     *                  @OA\Property(property="max_fee", type="integer", description="最大手续费"),
     *                  @OA\Property(property="min_fee", type="integer", description="最小手续费"),
     *                  @OA\Property(property="fee_rebate", type="integer", description="充值手续费百分比"),
     *                  @OA\Property(property="image_id", type="integer", description="图片地址id"),
     *                  @OA\Property(property="show_type", type="integer", description="显示类型 1:All 2:User 3:Affiliate"),
     *                  @OA\Property(property="status", type="integer", description="状态"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *              ),
     *          ),
     *     ),
     *      @OA\Response(
     *          response=204,
     *          description="PaymentPlatform",
     *          @OA\Items(ref="#/components/schemas/PaymentPlatform"),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function update(PaymentPlatform $paymentPlatform, PaymentPlatformRequest $request)
    {
        $data = $request->all([
            'name',
            'devices',
            'payment_type',
            'customer_id',
            'customer_key',
            'request_url',
            'request_type',
            'is_need_type_amount',
            'max_deposit',
            'min_deposit',
            'is_fee',
            'fee_rebate',
            'min_fee',
            'max_fee',
            'status',
            'sort',
            'show_type',
            'related_no',
            'related_name',
        ]);
        $data['currencies'] = implode(",", $request->currencies);
        $data = remove_null($data);
        if ($request->image_id){
            $data['image_path'] = Image::find($request->image_id)->path;
        }
        $paymentPlatform->update($data);
        return $this->response->item($paymentPlatform, new PaymentPlatformTransformer());
    }

    public function destroy(PaymentPlatform $paymentPlatform)
    {
        $paymentPlatform->delete();

        return $this->response->noContent();
    }

    /**
     * @OA\Get(
     *      path="/backstage/payment_platform/search",
     *      operationId="backstage.payment_platform.search",
     *      tags={"Backstage-支付通道"},
     *      summary="支付通道实时查询",
     *      @OA\Parameter(name="code", in="path", description="支付平台代号", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/PaymentPlatform"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *         {"bearer": {}}
     *      }
     *  )
     */
    public function searchInReadTime(PaymentPlatformRequest $request)
    {
        $result = PaymentPlatform::query()->where('code', 'like', '%' . $request->code . '%')->get();
        return $this->response->collection($result, new PaymentPlatformTransformer());
    }
}
