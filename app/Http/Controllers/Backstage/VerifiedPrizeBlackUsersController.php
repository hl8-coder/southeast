<?php

namespace App\Http\Controllers\Backstage;

use App\Exports\ExcelTemplateExport;
use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\VerifiedPrizeBlackUsersRequest;
use App\Imports\VerifiedPrizeBlackUsersImport;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\VerifiedPrizeBlackUser;
use App\Transformers\VerifiedPrizeBlackUserTransformer;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class VerifiedPrizeBlackUsersController extends BackstageController
{

    /**
     * @OA\Get(
     *      path="/backstage/verified_prize_black_users",
     *      operationId="backstage.verified_prize_black_users.index",
     *      tags={"Backstage-会员"},
     *      summary="不可以领取资料验证奖黑名单",
     *      @OA\Parameter(name="filter[user_name_like]", in="query", description="用户名，模糊查找", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[admin_name_like]", in="query", description="管理员名称，模糊查找", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/VerifiedPrizeBlackUser"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function index(VerifiedPrizeBlackUsersRequest $request)
    {
        $data = QueryBuilder::for(VerifiedPrizeBlackUser::class)
            ->allowedFilters(
                Filter::scope('user_name_like'),
                Filter::scope('admin_name_like'),
                Filter::scope('currency'),
                Filter::exact('user_id'),
                Filter::exact('admin_id')
            )
            ->orderByDesc('add_at')
            ->paginate($request->per_page);
        return $this->response()->paginator($data, new VerifiedPrizeBlackUserTransformer());
    }


    /**
     * @OA\Post(
     *      path="/backstage/verified_prize_black_users",
     *      operationId="backstage.verified_prize_black_users.store",
     *      tags={"Backstage-会员"},
     *      summary="增加会员列入到黑名单中",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="user_name", type="string", description="会员名称"),
     *                  required={"admin_name"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/VerifiedPrizeBlackUser"),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *          security={
     *              {"bearer": {}}
     *          }
     *     )
     */
    public function store(VerifiedPrizeBlackUsersRequest $request, VerifiedPrizeBlackUser $blackUser)
    {
        $user  = User::query()->where('is_agent', 0)->where('name', $request->user_name)->first();
        $admin = $this->user;

        $blackUser->user_id         = $user->id;
        $blackUser->user_name       = $user->name;
        $blackUser->add_by          = $admin->name;
        $blackUser->add_by_admin_id = $admin->id;
        $blackUser->add_at          = now();

        try {
            $boolean = $blackUser->save();
        } catch (\Exception $e) {
            $this->response()->error('Can not add this member code, Please check!', 422);
        }

        if ($boolean) {
            $user->info()->update(['claimed_verify_prize_at' => now()]);
        }
        $blackUser->refresh();

        return $this->response()->item($blackUser, new VerifiedPrizeBlackUserTransformer());

    }



    /**
     * @OA\Get(
     *      path="/backstage/verified_prize_black_users/excel_template",
     *      operationId="backstage.crm_resources.excel_template",
     *      tags={"Backstage-会员"},
     *      summary="获取批量上传用户名单表格模版",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function excelTemplate(VerifiedPrizeBlackUsersRequest $request)
    {
        $headers = ['MemberCode'];
        return Excel::download(new ExcelTemplateExport([], $headers), 'template.xlsx');
    }


    /**
     * @OA\Post(
     *      path="/backstage/verified_prize_black_users/excel",
     *      operationId="backstage.verified_prize_black_users.import_by_excel",
     *      tags={"Backstage-会员"},
     *      summary="通过表格生批量上传不可领验证奖会员名单",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="excel", type="file", description="资源文件"),
     *                  required={"excel"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201,description="no content"),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function importByExcel(VerifiedPrizeBlackUsersRequest $request)
    {
        $data      = Excel::toArray(new VerifiedPrizeBlackUsersImport(), $request->file('excel'));
        $admin     = $this->user;
        $insert    = [];
        $userNames = Arr::flatten($data);

        $existsUsers = User::query()->whereIn('name', $userNames)->pluck('name', 'id')->toArray();

        if (count($userNames) != count($existsUsers)) {
            $wrongUsers = array_diff($userNames, $existsUsers);
            $message    = 'There are some wrong member code: ' . implode(', ', $wrongUsers);
            return $this->response()->accepted(null, ['message' => $message, 'status_code' => 202]);
        }

        foreach ($existsUsers as $userId => $userName) {
            $temp['user_id']         = $userId;
            $temp['user_name']       = $userName;
            $temp['add_by']          = $admin->name;
            $temp['add_by_admin_id'] = $admin->id;
            $temp['updated_at']      = now();
            $temp['created_at']      = now();
            $temp['add_at']          = now();
            $insert[]                = $temp;
        }

        try {
            $result = batch_insert(app(VerifiedPrizeBlackUser::class)->getTable(), $insert);
        }catch (\Exception $exception){
            $result = false;
        }


        if ($result == false) {
            $this->response()->error('Add Black User List Fail!', 422);
        } else {
            UserInfo::whereIn('user_id', array_keys($existsUsers))->update(['claimed_verify_prize_at' => now()]);
            return $this->response()->created();
        }

    }

    /**
     * @OA\Delete(
     *      path="/backstage/verified_prize_black_users/{verified_prize_black_user}",
     *      operationId="backstage.verified_prize_black_users.delete",
     *      tags={"Backstage-会员"},
     *      summary="删除黑名单",
     *      @OA\Parameter(
     *         name="verified_prize_black_user",
     *         in="path",
     *         description="黑名单id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(response=204,description="No Content"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function destroy(VerifiedPrizeBlackUser $verifiedPrizeBlackUser)
    {
        $verifiedPrizeBlackUser->user()->update([
            'status' => User::STATUS_ACTIVE,
        ]);

        $verifiedPrizeBlackUser->delete();

        return $this->response->noContent();
    }
}
