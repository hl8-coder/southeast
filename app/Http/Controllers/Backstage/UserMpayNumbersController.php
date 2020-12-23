<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Models\UserMpayNumber;
use App\Repositories\UserRepository;
use App\Transformers\UserMpayNumberTransformer;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class UserMpayNumbersController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/user_mpay_numbers",
     *      operationId="backstage.user_mpay_numbers.index",
     *      tags={"Backstage-会员银行卡"},
     *      summary="Mapy账号列表",
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[user_name]", in="query", description="会员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[number]", in="query", description="账号", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/UserMpayNumber"),
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
        $userMpayNumbers = QueryBuilder::for (UserMpayNumber::class)
            ->allowedFilters([
                Filter::scope('user_name'),
                Filter::exact('number'),
                Filter::scope('currency'),
            ])
            ->latest()
            ->with('user')
            ->paginate($request->per_page);

        return $this->response->paginator($userMpayNumbers, new UserMpayNumberTransformer());
    }

    /**
     * @OA\Delete(
     *      path="/backstage/user_mpay_numbers/{user_mpay_number}",
     *      operationId="backstage.user_mpay_numbers.destroy",
     *      tags={"Backstage-会员银行卡"},
     *      summary="删除Mapy账号列表",
     *      @OA\Response(
     *          response=204,
     *          description="no content"
     *       ),
     *       @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function destroy(UserMpayNumber $userMpayNumber)
    {
        $userMpayNumber->delete();
        return $this->response->noContent();
    }

    /**
     * @OA\Get(
     *      path="/backstage/user_mpay_numbers/user_index",
     *      operationId="backstage.user_mpay_numbers.user_index",
     *      tags={"Backstage-会员银行卡"},
     *      summary="会员Mapy账号列表",
     *       @OA\Parameter(name="is_agent", in="query", description="是否是代理， 不传则是会员", @OA\Schema(type="boolean")),
     *      @OA\Parameter(name="user_name", in="query", description="用户名", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/UserMpayNumber"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function userIndex(Request $request)
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

        $userMpayNumbers = UserMpayNumber::getActiveByUserId($user->id);
        $data = [];
        foreach ($userMpayNumbers as $key => $userMpay) {
            $data['user_mpay_numbers'][$key]['country_code']   = $userMpay->area_code;
            $data['user_mpay_numbers'][$key]['number']         = hidden_number($userMpay->number, 4);
        }

        return $this->response->array($data);
    }
}
