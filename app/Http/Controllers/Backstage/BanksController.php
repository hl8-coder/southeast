<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\BankRequest;
use App\Models\Bank;
use App\Transformers\BankTransformer;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class BanksController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/banks?include=images",
     *      operationId="backstage.banks.index",
     *      tags={"Backstage-银行"},
     *      summary="银行列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Bank"),
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

        $banks = QueryBuilder::for(Bank::class)
            ->allowedFilters(
                Filter::exact('currency'),
                Filter::exact('name'),
                Filter::exact('code'),
                Filter::exact('status')
            )
            ->defaultSort('created_at')
            ->paginate($request->per_page);
        return $this->response->paginator($banks, new BankTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/banks",
     *      operationId="backstage.banks.store",
     *      tags={"Backstage-银行"},
     *      summary="添加银行",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="currency", type="string", description="币别"),
     *                  @OA\Property(property="name", type="string", description="银行名称"),
     *                  @OA\Property(property="languages", type="array", description="银行名称多语言设置", @OA\Items(
     *                       @OA\Property(property="language", type="string", description="语言"),
     *                       @OA\Property(property="front_name", type="string", description="银行前端显示名称"),
     *                       @OA\Property(property="maintenance_schedules", type="string", description="维护计划, ;号分割"),
     *                  )),
     *                  @OA\Property(property="code", type="string", description="银行编码"),
     *                  @OA\Property(property="min_balance", type="number", description="最小金额"),
     *                  @OA\Property(property="daily_limit", type="number", description="日限制金额"),
     *                  @OA\Property(property="annual_limit", type="number", description="总流水限制(存款+提款)"),
     *                  @OA\Property(property="is_auto_deposit", type="integer", description="是否开启自动充值"),
     *                  @OA\Property(property="image", type="string", description="图片id"),
     *                  @OA\Property(property="icon", type="string", description="图片id"),
     *                  @OA\Property(property="status", type="integer",description="状态"),
     *                  required={"currency", "name", "languages", "code"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/Bank"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function store(BankRequest $request)
    {
        $data = remove_null($request->all());

        $data['admin_name'] = $this->user->name;
        if (!empty($request->image)) {
            $data['image']  = $this->getImagePathByImageId($request->image);
        }
        if (!empty($request->icon)) {
            $data['icon']   = $this->getImagePathByImageId($request->icon);
        }
        $bank               = Bank::query()->create($data);
        return $this->response->item($bank, new BankTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Patch(
     *      path="/backstage/banks/{bank}",
     *      operationId="backstage.banks.update",
     *      tags={"Backstage-银行"},
     *      summary="更新银行信息",
     *      @OA\Parameter(
     *         name="bank",
     *         in="path",
     *         description="银行id",
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
     *                  @OA\Property(property="front_name", type="number", description="银行前端显示名称"),
     *                  @OA\Property(property="min_balance", type="number", description="最小金额"),
     *                  @OA\Property(property="daily_limit", type="number", description="日限制金额"),
     *                  @OA\Property(property="annual_limit", type="number", description="总流水限制(存款+提款)"),
     *                  @OA\Property(property="is_auto_deposit", type="integer", description="是否开启自动充值"),
     *                  @OA\Property(property="image", type="string", description="图片id"),
     *                  @OA\Property(property="status", type="integer",description="状态"),
     *                  @OA\Property(property="icon", type="string", description="图片id"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Bank"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function update(Bank $bank, BankRequest $request)
    {
        $data               = remove_null($request->all());
        if (!empty($request->image)) {
            $data['image']  = $this->getImagePathByImageId($request->image);
        }

        if (!empty($request->icon)) {
            $data['icon']   = $this->getImagePathByImageId($request->icon);
        }

        $data['admin_name'] = $this->user->name;
        $bank->update($data);

        return $this->response->item($bank, new BankTransformer());
    }
}
