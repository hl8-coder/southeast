<?php

namespace App\Http\Controllers\Backstage;

use App\Exports\ExcelTemplateExport;
use App\Http\Requests\Backstage\UserRequest;
use App\Jobs\SendEmailJob;
use App\Models\GamePlatformProduct;
use App\Models\MailboxTemplate;
use App\Models\ProfileRemark;
use App\Models\TrackingStatistic;
use App\Models\TrackingStatisticLog;
use App\Models\TransferDetail;
use App\Models\UserBankAccount;
use App\Models\UserProductDailyReport;
use App\Repositories\UserProductDailyRepository;
use App\Services\AffiliateService;
use App\Services\UserService;
use App\Transformers\AffiliateProfitInfoTransformer;
use App\Transformers\AuditTransformer;
use App\Transformers\TrackingStatisticLogTransformer;
use App\Transformers\TrackingStatisticTransformer;
use App\Transformers\TransferDetailTransformer;
use App\Transformers\UserBankAccountTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\BackstageController;
use App\Models\User;
use App\Models\Affiliate;
use App\Models\AffiliateRemark;
use App\Models\AffiliateCommission;
use App\Transformers\UserTransformer;
use App\Transformers\AffiliateTransformer;
use App\Transformers\AffiliateRemarkTransformer;
use App\Transformers\AffiliateCommissionTransformer;
use App\Transformers\ReleaseCommissionsDownloadTransformer;
use App\Http\Requests\Backstage\AffiliateRequestApproveRequest;
use App\Http\Requests\Backstage\AffiliateUpdateRequest;
use App\Http\Requests\Backstage\AffiliateRemarkRequest;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;
use Carbon\Carbon;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class AffiliatesController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/affiliates/{affiliate}?include=user,userInfo,bankAccount,commissions",
     *      operationId="backstage.affiliates.show",
     *      tags={"Backstage-代理"},
     *      summary="代理详情",
     *      @OA\Parameter(
     *         name="affiliate",
     *         in="path",
     *         description="代理ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Affiliate"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *         {"bearer": {}}
     *      }
     *  )
     */
    public function show(Affiliate $affiliate)
    {
        return $this->response->item($affiliate, new AffiliateTransformer('backstage_show_item'));
    }

    /**
     * @OA\Get(
     *      path="/backstage/affiliates?include=user,userInfo,bankAccount,commissions",
     *      operationId="backstage.affiliates.index",
     *      tags={"Backstage-代理"},
     *      summary="代理列表",
     *      description="代理列表",
     *      @OA\Parameter(name="filter[code]", in="query", description="代理号", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[name]", in="query", description="帐号", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[email]", in="query", description="Email", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[created_at]", in="query", description="建立日期", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Affiliate"),
     *          ),
     *      ),
     *      security={
     *           {"bearer": {}}
     *       }
     *  )
     */
    public function index(Request $request)
    {
        $fields = [
            'id',
            'user_id',
            'code',
            'refer_by_code',
            'is_fund_open',
            'cs_status',
            'admin_name',
        ];

        $affiliates = QueryBuilder::for(Affiliate::class)
            ->select($fields)
            ->where("cs_status", Affiliate::CS_STATUS_APPROVED)
            ->allowedFilters(
                Filter::exact('code'),
                Filter::scope('status'),
                Filter::scope('currency'),
                Filter::scope('name'),
                Filter::scope('web_url'),
                Filter::scope('email'),
                Filter::scope('end_at'),
                Filter::scope('start_at')
            )
            ->orderBy('id', 'desc')
            ->paginate($request->per_page);
        return $this->response->paginator($affiliates, new AffiliateTransformer('backstage_index'));
    }

    /**
     * @OA\Patch(
     *      path="/backstage/affiliates/{affiliate}?include=userInfo",
     *      operationId="backstage.users.update",
     *      tags={"Backstage-代理"},
     *      summary="更新代理信息",
     *      @OA\Parameter(
     *         name="affiliate",
     *         in="path",
     *         description="代理id",
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
     *                  @OA\Property(property="full_name", type="string", description="全名"),
     *                  @OA\Property(property="email", type="string", description="邮箱"),
     *                  @OA\Property(property="avatar_id", type="string", description="头像"),
     *                  @OA\Property(property="status", type="string", description="状态"),
     *                  @OA\Property(property="phone", type="string", description="电话号码"),
     *                  @OA\Property(property="password", type="string", description="密码"),
     *                  @OA\Property(property="is_fund_open", type="string", description="是否开启转帐"),
     *                  @OA\Property(property="commission_setting", type="string", description="佣金设定"),
     *                  @OA\Property(property="describe", type="array", description="自我描述", @OA\Items(
     *                     @OA\Property(property="language", type="string", description="语言"),
     *                     @OA\Property(property="content", type="string", description="内容"),
     *                  )),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Affiliate"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function update(Affiliate $affiliate, AffiliateUpdateRequest $request, UserService $service)
    {
        try {
            $affiliate = DB::transaction(function () use ($affiliate, $request, $service) {

                $affiliateData = $request->only(['is_fund_open', 'commission_setting']);

                foreach ($affiliateData as $key => $value) {
                    switch ($key) {
                        case 'is_fund_open':
                            $remark = "Fund managerment Open";
                            break;
                        case 'commission_setting':
                            $remark = "Commission Update";
                            break;
                        default:
                            $remark = "";
                            break;
                    }

                    AffiliateRemark::store($affiliate->id, "Profile Changes", $remark, $this->user()->name);
                }

                if ($request->is_fund_open && $affiliate->is_fund_open) {
                    return $this->response->error('Fund managerment has been open.', 422);
                }

                $affiliateData["commission_setting"] = $request->commission_setting;

                $affiliateData = remove_null($affiliateData);
                $affiliate->update($affiliateData);

                $userData = $request->only(['password', 'status']);

                foreach ($userData as $key => $value) {
                    switch ($key) {
                        case 'password':
                            $remark = "Password Update";
                            break;
                        case 'status':
                            $remark = "Status Update";
                            if ($value == User::STATUS_BLOCKED) {
                                # 设置旧token过期
                                $service->setTokenInvalidate($affiliate->user);
                            }
                            break;
                        default:
                            $remark = "";
                            break;
                    }
                    AffiliateRemark::store($affiliate->id, "Profile Changes", $remark, $this->user()->name);
                }

                if ($request->password) {
                    $userData["password"] = bcrypt($userData["password"]);
                }
                $userData = remove_null($userData);
                $affiliate->user->update($userData);

                $infoData = $request->only(['full_name', 'phone', 'email', 'web_url', 'birth_at', 'avatar_id', 'describe']);

                foreach ($infoData as $key => $value) {
                    switch ($key) {
                        case 'full_name':
                            $remark = "Full Name Update";
                            break;
                        case 'phone':
                            $remark = "Phone Update";
                            break;
                        case 'email':
                            $remark = "Email Update";
                            break;
                        case 'web_url':
                            $remark = "Web Url Update";
                            break;
                        case 'birth_at':
                            $remark = "Birth Date Update";
                            break;
                        case 'describe':
                            if ($request->exists('describe')){
                                $infoData['describe'] = $request->input('describe') == null ? '' : $request->input('describe');
                            }
                            $remark = "Describe Update";
                            break;
                        case 'avatar_id':
                            $imagePath = $this->getImageInfoById($value);
                            if ($imagePath){
                                $infoData['avatar'] = $imagePath->path;
                                $remark = 'Update Avatar';
                                unset($infoData['avatar_id']);
                            }else{
                                $infoData['avatar'] = '';
                                $remark = 'Update Avatar';
                                unset($infoData['avatar_id']);
                            }
                            break;
                        default:
                            $remark = "";
                            break;
                    }
                    if ($value == $affiliate->user->info->$key) {
                        unset($infoData[$key]);
                    } else {
                        AffiliateRemark::store($affiliate->id, "Profile Changes", $remark, $this->user()->name);
                    }
                }

                $infoData = remove_null($infoData);
                $affiliate->user->info->update($infoData);

                return $affiliate;
            });
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 400);
        }

        return $this->response->item($affiliate, new AffiliateTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/affiliates/{affiliate}/reset_password",
     *      operationId="backstage.affiliate.reset_password",
     *      tags={"Backstage-代理"},
     *      summary="重置密码",
     *      @OA\Parameter(name="affiliate", in="path", description="代理ID", @OA\Schema(type="integer")),
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
    public function resetPassword(Affiliate $affiliate, UserRequest $request)
    {
        # 获取初始密码
        if ('auto' == $request->type) {
            $password = str_random(8);
        } else {
            $password = $request->new_password;
        }

        $user = $affiliate->user;
        if ($user->updatePassword($password)) {
            $user->setNeedChangePassword();
            ProfileRemark::add($user->id, ProfileRemark::CATEGORY_ACCOUNT, 'reset password', $this->user->name);
        }

        if ('auto' == $request->type) {
            dispatch(new SendEmailJob(MailboxTemplate::FORGET_PASSWORD, $user->info->email, $user->currency, $user->is_agent, $user->language, $password))->onQueue('send_email');
        }
        $remark = "Password Update";
        AffiliateRemark::store($affiliate->id, "Profile Changes", $remark, $this->user()->name);

        return $this->response->noContent();
    }

    /**
     * @OA\Get(
     *      path="/backstage/affiliates/{affiliate}/remarks",
     *      operationId="backstage.affiliates.remarks.index",
     *      tags={"Backstage-代理"},
     *      summary="备注列表",
     *      @OA\Parameter(
     *         name="affiliate",
     *         in="path",
     *         description="代理ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Parameter(name="filter[remark]", in="query", description="备注", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/AffiliateRemark"),
     *          ),
     *      ),
     *      security={
     *           {"bearer": {}}
     *       }
     *  )
     */
    public function remarks(Affiliate $affiliate, Request $request)
    {
        $remarks = QueryBuilder::for(AffiliateRemark::class)
            ->where("affiliate_id", $affiliate->id)
            ->allowedFilters(
                Filter::scope('remark')
            )
            ->orderBy("id", "desc")
            ->paginate($request->per_page);

        return $this->response->paginator($remarks, new AffiliateRemarkTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/affiliates/{affiliate}/remarks",
     *      operationId="backstage.affiliates.remarks.store",
     *      tags={"Backstage-代理"},
     *      summary="添加备注",
     *      @OA\Parameter(
     *         name="affiliate",
     *         in="path",
     *         description="代理ID",
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
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *                  required={"remark", "type", "category", "reason"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/AffiliateRemark"),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function remarksStore(Affiliate $affiliate, AffiliateRemarkRequest $request)
    {
        $data                 = remove_null($request->all());
        $data['affiliate_id'] = $affiliate->id;
        $data['reason']       = "Custom";
        $data['admin_name']   = $this->user->name;

        $remark = AffiliateRemark::query()->create($data);

        return $this->response->item($remark, new AffiliateRemarkTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *      path="/backstage/affiliates/{affiliate}/sub_users?include=info",
     *      operationId="backstage.affiliates.sub_users.index",
     *      tags={"Backstage-代理"},
     *      summary="代理的会员",
     *      @OA\Parameter(
     *         name="affiliate",
     *         in="path",
     *         description="代理ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/User"),
     *          ),
     *      ),
     *      security={
     *           {"bearer": {}}
     *       }
     *  )
     */
    public function subUsers(Affiliate $affiliate, Request $request)
    {
        $remarks = QueryBuilder::for(User::class)
            ->where("parent_id", $affiliate->user_id)
            ->where("is_agent", false)
            ->orderBy("id", "desc")
            ->paginate($request->per_page);

        return $this->response->paginator($remarks, new UserTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/affiliates/{affiliate}/profit_info",
     *      operationId="backstage.affiliates.profit_info.index",
     *      tags={"Backstage-代理"},
     *      summary="代理当月的盈亏资讯",
     *      @OA\Parameter(
     *         name="affiliate",
     *         in="path",
     *         description="代理ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/AffiliateProfitInfo"),
     *          ),
     *      ),
     *      security={
     *           {"bearer": {}}
     *       }
     *  )
     */
    public function profitInfo(Affiliate $affiliate, Request $request)
    {
        $profitInfo = UserProductDailyRepository::getProfitInfoByAffiliate($affiliate);

        return $this->response->collection($profitInfo, new AffiliateProfitInfoTransformer());

    }

    /**
     * @OA\Get(
     *      path="/backstage/affiliates/{affiliate}/commissions",
     *      operationId="backstage.affiliates.commissions.index",
     *      tags={"Backstage-代理"},
     *      summary="分红列表",
     *      @OA\Parameter(name="affiliate", in="path", description="代理ID", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[month]", in="query", description="月份", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/AffiliateCommission"),
     *          ),
     *      ),
     *      security={
     *           {"bearer": {}}
     *       }
     *  )
     */
    public function commissions(Affiliate $affiliate, Request $request, AffiliateService $service)
    {
        $commissions = $service->getCommissionByMonth($affiliate, $request->filter['month']);

        return $this->response->collection($commissions, new AffiliateCommissionTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/affiliates/requests?include=user,userInfo,bankAccount",
     *      operationId="backstage.affiliates.request.index",
     *      tags={"Backstage-代理"},
     *      summary="代理申请列表",
     *      description="代理申请列表",
     *      @OA\Parameter(name="filter[name]", in="query", description="帐号", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[code]", in="query", description="代码", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[email]", in="query", description="Email", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[cs_status]", in="query", description="状态", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="启始日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="结束日期", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Affiliate"),
     *          ),
     *      ),
     *      security={
     *           {"bearer": {}}
     *       }
     *  )
     */
    public function requestIndex(Request $request)
    {

        $fields = [
            'id',
            'user_id',
            'code',
            'commission_setting',
            'refer_by_code',
            'is_fund_open',
            'cs_status',
            'admin_name',
        ];

        $affiliates = QueryBuilder::for(Affiliate::class)
            ->select($fields)
            ->where("cs_status", "<>", Affiliate::CS_STATUS_APPROVED)
            ->allowedFilters(
                Filter::scope('phone'),
                Filter::scope('web_url'),
                Filter::scope('name'),
                Filter::exact('code'),
                Filter::scope('currency'),
                Filter::scope('email'),
                Filter::exact('cs_status'),
                Filter::scope('start_at'),
                Filter::scope('end_at')
            )
            ->paginate($request->per_page);

        return $this->response->paginator($affiliates, new AffiliateTransformer('backstage_index'));
    }

    /**
     * @OA\Patch(
     *      path="/backstage/affiliates/{affiliate}/request_approve",
     *      operationId="backstage.affiliates.requests.approve",
     *      tags={"Backstage-代理"},
     *      summary="代理申请同意",
     *      @OA\Parameter(
     *         name="affiliate",
     *         in="path",
     *         description="代理ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Affiliate"),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function requestApprove(Affiliate $affiliate, AffiliateRequestApproveRequest $request)
    {
        if ($affiliate->cs_status != Affiliate::CS_STATUS_PENDING) {
            return $this->response->error('Status error.', 422);
        }

        $affiliate->commission_setting = $request->commission_setting;

        $affiliate->cs_status  = Affiliate::CS_STATUS_APPROVED;
        $affiliate->admin_name = $this->user->name;
        $affiliate->save();

        $affiliate->user->status = User::STATUS_ACTIVE;
        $affiliate->user->save();
        dispatch(new SendEmailJob(MailboxTemplate::APPROVE_AFF, $affiliate->user->info->email, $affiliate->user->currency, $affiliate->user->is_agent, $affiliate->user->language, '', $affiliate->user->name, '', $affiliate->user->info->full_name, $affiliate->user->affiliate_code))->onQueue('send_email');

        return $this->response->item($affiliate, new AffiliateTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Patch(
     *      path="/backstage/affiliates/{affiliate}/request_reject",
     *      operationId="backstage.affiliates.requests.reject",
     *      tags={"Backstage-代理"},
     *      summary="代理申请拒绝",
     *      @OA\Parameter(
     *         name="affiliate",
     *         in="path",
     *         description="代理ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Affiliate"),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function requestReject(Affiliate $affiliate, Request $request)
    {
        if ($affiliate->cs_status != Affiliate::CS_STATUS_PENDING) {
            return $this->response->error('Status error.', 422);
        }

        $affiliate->cs_status = Affiliate::CS_STATUS_REJECTED;
        $affiliate->admin_name = $this->user->name;
        $affiliate->save();

        return $this->response->item($affiliate, new AffiliateTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *      path="/backstage/affiliates/funds",
     *      operationId="backstage.affiliates.funds.index",
     *      tags={"Backstage-代理"},
     *      summary="代理转帐列表",
     *      description="代理转帐列表",
     *      @OA\Parameter(name="order", in="query", description="排序", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[code]", in="query", description="代码", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[to_user_name]", in="query", description="收款会员", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="启始日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="结束日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/TransferDetail"),
     *          ),
     *      ),
     *      security={
     *           {"bearer": {}}
     *       }
     *  )
     */
    public function fundsIndex(Request $request)
    {
        $ORM = TransferDetail::query();
        # 设定排序
        if ($request->order) {
            $order    = explode('_', $request->order);
            $sortType = array_pop($order);
            $sortKey  = implode($order, "_");
            $ORM->orderBy(implode('_', $order), $sortType);
        }
        $funds = QueryBuilder::for($ORM)
            ->allowedFilters([
                Filter::scope('code'),
                Filter::exact('status'),
                Filter::exact('user_name'),
                Filter::exact('to_user_name'),
                Filter::scope('start_at'),
                Filter::scope('end_at'),
                Filter::scope('currency'),
            ])
            ->paginate($request->per_page);

        return $this->response->paginator($funds, new TransferDetailTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/affiliates/subs?include=user,userInfo,bankAccount",
     *      operationId="backstage.affiliates.subs.index",
     *      tags={"Backstage-代理"},
     *      summary="子代理列表",
     *      description="子代理列表",
     *      @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[refer_by_code]", in="query", description="上级代理代码", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[parent_name]", in="query", description="上级帐号", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="启始日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="结束日期", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Affiliate"),
     *          ),
     *      ),
     *      security={
     *           {"bearer": {}}
     *       }
     *  )
     */
    public function subsIndex(Request $request)
    {

        $affiliates = QueryBuilder::for(Affiliate::class)
            ->where("refer_by_code", "!=", "")
            ->where("cs_status", Affiliate::CS_STATUS_APPROVED)
            ->allowedFilters(
                Filter::scope('currency'),
                Filter::scope('status'),
                Filter::exact('refer_by_code'),
                Filter::scope('parent_name'),
                Filter::scope('start_at'),
                Filter::scope('end_at')
            )
            ->paginate($request->per_page);

        return $this->response->paginator($affiliates, new AffiliateTransformer('backstage_index'));
    }

    /**
     * @OA\Get(
     *      path="/backstage/affiliates/commissions/pending",
     *      operationId="backstage.affiliates.commissions.pending.index",
     *      tags={"Backstage-代理"},
     *      summary="创建的分红奖励列表",
     *      @OA\Parameter(name="filter[user_name]", in="query", description="代理名", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[month]", in="query", description="月份", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[monthly]", in="query", description="月份", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/AffiliateCommission"),
     *          ),
     *      ),
     *      security={
     *           {"bearer": {}}
     *       }
     *  )
     */
    public function pendingCommissions(Request $request)
    {
        $commissions = QueryBuilder::for(AffiliateCommission::class)
            ->where('status', AffiliateCommission::STATUS_PENDING)
            ->allowedFilters(
                Filter::exact('user_name'),
                Filter::scope('month'),
                Filter::exact('monthly'),
                Filter::exact('active_count'),
                Filter::exact('currency')
            )
            ->with(['userInfo', 'affiliate', 'bank'])
            ->paginate($request->per_page);

        # 获取所有affiliate_user_id
        $affiliateUserIds = collect($commissions->items())->pluck('user_id')->toArray();

        $data['new_sign_count'] = User::query()->where('is_agent', false)
            ->whereIn('parent_id', $affiliateUserIds)
            ->groupBy([
                'parent_id',
                DB::raw("left(created_at, 7)"),
            ])->get([
                'parent_id',
                DB::raw("left(created_at, 7) as month"),
                DB::raw("Count(*) as total_member"),
            ]);

        $data['total_member'] = User::query()->where('is_agent', false)
            ->whereIn('parent_id', $affiliateUserIds)
            ->groupBy('parent_id')
            ->get([
                'parent_id',
                DB::raw("Count(*) as total_member"),
            ]);


        return $this->response->paginator($commissions, new AffiliateCommissionTransformer('pending', $data));
    }

    /**
     * @OA\Patch(
     *      path="/backstage/affiliates/commissions/release",
     *      operationId="backstage.affiliates.commissions.pending.update",
     *      tags={"Backstage-代理"},
     *      summary="奖历发布列表",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="ids", type="string", description="分红id，逗号隔开"),
     *                  required={"ids"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/AffiliateCommission"),
     *          ),
     *      ),
     *      security={
     *           {"bearer": {}}
     *       }
     *  )
     */
    public function releaseCommissions(Request $request)
    {
        if (!$ids = $request->ids) {
            return $this->response->error('Please select id', 422);
        }

        $ids = explode(',', $ids);
        AffiliateCommission::query()->whereIn('id', $ids)->update([
            'status'           => AffiliateCommission::STATUS_RELEASE,
            'last_access_at'   => now(),
            'last_access_name' => $this->user()->name,
        ]);

        return $this->response->noContent();
    }

    /**
     * @OA\Get(
     *      path="/backstage/affiliates/commissions/pending/download",
     *      operationId="backstage.affiliates.commissions.pending.download",
     *      tags={"Backstage-代理"},
     *      summary="pending状态代理分红列表下载",
     *      @OA\Parameter(name="filter[month]", in="query", description="月份", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[monthly]", in="query", description="月份", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *      ),
     *      security={
     *           {"bearer": {}}
     *      }
     *  )
     */
    public function downloadPendingCommissions(Request $request)
    {
        $commissions = QueryBuilder::for(AffiliateCommission::class)
            ->where('status', AffiliateCommission::STATUS_PENDING)
            ->allowedFilters([
                Filter::exact('user_name'),
                Filter::scope('month'),
                Filter::exact('monthly'),
                Filter::exact('active_count'),
                Filter::exact('currency'),
            ])
            ->get();

        # 获取所有affiliate_user_id
        $affiliateUserIds = collect($commissions)->pluck('user_id')->toArray();

        $data['new_sign_count'] = User::query()->where('is_agent', false)
            ->whereIn('parent_id', $affiliateUserIds)
            ->groupBy([
                'parent_id',
                DB::raw("left(created_at, 7)"),
            ])->get([
                'parent_id',
                DB::raw("left(created_at, 7) as month"),
                DB::raw("Count(*) as total_member"),
            ]);

        $data['total_member'] = User::query()->where('is_agent', false)
            ->whereIn('parent_id', $affiliateUserIds)
            ->groupBy('parent_id')
            ->get([
                'parent_id',
                DB::raw("Count(*) as total_member"),
            ]);

        $headings =  [
            'Currency',
            'Affiliate ID',
            'UAP',
            'Total Member',
            'New Sign Count',
            'Total Deposit',
            'Total Withdrawal',
            'Total Rake Amount',
            'Adjustment',
            'Total Stake',
            'Win/Loss' ,
            'Rebate',
            'Promotion',
            'Transaction Cost',
            'Net Loss',
            'Commission Rate',
            'Previous Balance',
            'Payout Comm' ,
            'Sub Aff Payout %',
            'Sub AFF Payout',
            'B/F',
        ];

        foreach ($commissions as $commission) {
            $result[] = ((new ReleaseCommissionsDownloadTransformer('', $data))->transform($commission));
        }

        return Excel::download(new ExcelTemplateExport($result, $headings), 'release_commissions.xlsx');
    }

    /**
     * @OA\Get(
     *      path="/backstage/affiliates/commissions/payout",
     *      operationId="backstage.affiliates.commissions.payout",
     *      tags={"Backstage-代理"},
     *      summary="代理分红支付列表",
     *      @OA\Parameter(name="filter[user_name]", in="query", description="帐号", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[month]", in="query", description="月份", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[monthly]", in="query", description="月份", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="int")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/AffiliateCommission"),
     *          ),
     *      ),
     *      security={
     *           {"bearer": {}}
     *       }
     *  )
     */
    public function payoutCommissions(Request $request)
    {
        $commissions = QueryBuilder::for(AffiliateCommission::class)
            ->where('status', '!=', AffiliateCommission::STATUS_PENDING)
            ->allowedFilters(
                Filter::exact('user_name'),
                Filter::scope('month'),
                Filter::exact('monthly'),
                Filter::scope('status'),
                Filter::exact('currency')
            )
            ->get();

        return $this->response->collection($commissions, new AffiliateCommissionTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/affiliates/commissions/payout/download",
     *      operationId="backstage.affiliates.commissions.payout.download",
     *      tags={"Backstage-代理"},
     *      summary="代理分红支付列表下载",
     *      @OA\Parameter(name="filter[name]", in="query", description="帐号", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[month]", in="query", description="月份", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[monthly]", in="query", description="月份", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="int")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *      ),
     *      security={
     *           {"bearer": {}}
     *      }
     *  )
     */
    public function downloadPayoutCommissions(Request $request)
    {
        $commissions = QueryBuilder::for(AffiliateCommission::class)
            ->where('status', '!=', AffiliateCommission::STATUS_PENDING)
            ->allowedFilters(
                Filter::exact('user_name'),
                Filter::scope('month'),
                Filter::exact('monthly'),
                Filter::scope('status'),
                Filter::exact('currency')
            )
            ->get();

        $headings = [
            'Currency',
            'Affiliate ID',
            'Full Name',
            'Payout Comm',
            'Account Name',
            'Bank account',
            'Bank Name ',
            'Bank brand',
            'Bank Address',
            'Status',
            'NET/LOSS'
        ];

        $exportData = [];

        if (count($commissions)) {
            foreach ($commissions as $commission) {

                $affiliate_id = $commission->affiliate_id;
                $currency = $commission->currency;
                $full_name = $commission->user->info->full_name;
                $payout_commission = thousands_number($commission->payout_commission);
                $account_name = $commission->account_name;
                $account_no = $commission->account_no;
                $bank_name = isset($commission->bank) ? $commission->bank->name:"-";
                $bank_branch =  $commission->branch;
                $bank_address = $commission->province . ' ' .$commission->city;
                $status = transfer_show_value($commission->status, AffiliateCommission::$statuses);
                $net_loss = thousands_number($commission->net_loss);

                $exportData[] = [
                    'currency' => $currency,
                    'affiliate_id' => $affiliate_id,
                    'full_name' => $full_name,
                    'payout_commission' => $payout_commission,
                    'account_name' => $account_name,
                    'account_no' => $account_no,
                    'bank_name' => $bank_name,
                    'bank_branch' => $bank_branch,
                    'bank_address' => $bank_address,
                    'status' => $status,
                    'net_loss' => $net_loss
                ];

            }
        }

        return Excel::download(new ExcelTemplateExport($exportData, $headings), 'commissions_payout.xlsx');

    }


    /**
     * @OA\Get(
     *      path="/backstage/affiliates/commissions/formula",
     *      operationId="backstage.affiliates.commissions.formula",
     *      tags={"Backstage-代理"},
     *      summary="计算公式说明文字",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(type="string"),
     *          ),
     *      ),
     *      security={
     *           {"bearer": {}}
     *       }
     *  )
     */
    public function formulaCommissions(Request $request)
    {
        //未来透过权限控管决定是否可显示, 目前没有限制
        $formula                = array();
        $formula['payout_comm'] = "[(Net/Loss) * commission rate] - [(Net/Loss) * Product license fee] + Previous BL + total comm Sub-AFF";

        return $this->response->array($formula);
    }

    /**
     * @OA\Get(
     *      path="/backstage/affiliates/game_platform_product_details/{user}",
     *      operationId="backstage.affiliates.game_platform_product_details.show",
     *      tags={"Backstage-代理"},
     *      summary="代理产品报表",
     *      @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="user ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="*",
     *                  type="string",
     *                  description="产品类型",
     *                  @OA\Items(
     *                      @OA\Property(property="code", type="string", description="产品code"),
     *                      @OA\Property(property="img", type="string", description="图片地址"),
     *                      @OA\Property(property="total_effective_profit", type="", description="公司盈亏"),
     *                      @OA\Property(property="active", type="number", description="活跃数量"),
     *                      @OA\Property(property="total_bet", type="number", description="总投注，暂时使用"),
     *                      @OA\Property(property="total_effective_bet", type="number", description="流水"),
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *           {"bearer": {}}
     *       }
     *  )
     */
    public function productDetailsBy(User $user)
    {
        $subUserIds            = $user->subUsers()->pluck('id');
        $gamePlatformProducts  = GamePlatformProduct::getAll();
        $data                  = [];
        $productDailyReportORM = UserProductDailyReport::query()
            ->whereIn('user_id', $subUserIds);
        foreach (GamePlatformProduct::$types as $key => $type) {
            $products = $gamePlatformProducts->where('type', $key);
            foreach ($products as $product) {
                $info          = $productDailyReportORM->where([
                    [
                        'platform_code', $product->platform_code
                    ],
                    [
                        'product_code', $product->code
                    ]
                ])
                    ->select(DB::raw("sum(effective_bet) as total_effective_bet, sum(profit) as total_effective_profit, sum(stake) as total_bet, COUNT(DISTINCT user_id) as active"))
                    ->first();
                $data[$type][] = [
                    'code'                   => $product->code,
                    'img'                    => $product->one_web_img_path,
                    'total_effective_profit' => $info->total_effective_profit,
                    'active'                 => $info->active,
                    'total_bet'              => $info->total_bet,
                    'total_effective_bet'    => $info->total_effective_bet,
                ];
            }
        }
        return $data;
    }

    /**
     * @OA\Get(
     *      path="/backstage/tracking_statistic?include=user,user.info",
     *      operationId="backstage.affiliates.tracking_statistic_logs",
     *      tags={"Backstage-代理"},
     *      summary="资源点击",
     *      @OA\Parameter(name="filter[name]", in="query", description="代理ID", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="开始", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="结束", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[code]", in="query", description="代理号", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/TrackingStatistic"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function creativeReport(Request $request)
    {
        $trackingStatistics = QueryBuilder::for(TrackingStatistic::class)
            ->with('trackingStatisticLogs')
            ->allowedFilters(
                Filter::scope('name'),
                Filter::scope('status'),
                Filter::scope('currency'),
                Filter::scope('start_at'),
                Filter::scope('end_at'),
                Filter::scope('code')
            )
            ->paginate($request->per_page);
        $date = [
            'start_at' => $request->filter['start_at'],
            'end_at' => $request->filter['end_at'],
        ];
        return $this->response->paginator($trackingStatistics, new TrackingStatisticTransformer('backend_index', $date));
    }

    /**
     * @OA\Get(
     *      path="/backstage/tracking_statistic/{statistic}?include=user,user.info",
     *      operationId="backstage.affiliates.tracking_statistic_logs.detail",
     *      tags={"Backstage-代理"},
     *      summary="资源点击详情",
     *      @OA\Parameter(
     *         name="statistic",
     *         in="path",
     *         description="statistic ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Parameter(name="start_at", in="query", description="开始", @OA\Schema(type="string")),
     *      @OA\Parameter(name="end_at", in="query", description="结束", @OA\Schema(type="string")),
     *      @OA\Parameter(name="type", in="query", description="类型", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/TrackingStatistic"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function getInfo(TrackingStatistic $statistic, Request $request)
    {
        $type  = $request->type;
        $start = $request->start_at;
        $end   = $request->end_at;

        if (!$type) {
            return $this->response->error('Please select a type', 422);
        }
        $ORM = $statistic->trackingStatisticLogs()->where(function ($query) use ($start, $end) {
            if (!empty($start)) {
                $query->where('created_at', '>=', convert_time(Carbon::parse($start)->startOfDay()));
            }
            if (!empty($end)) {
                $query->where('created_at', '<=', convert_time(Carbon::parse($end)->endOfDay()));
            }
        });

        switch ($type) {
            case 'url':
                return $ORM->where('url', '!=', '')
                    ->select(
                        DB::raw("count(url) as count"),
                        DB::raw("url as log")
                    )
                    ->groupBy("log")
                    ->get();
                break;
            case 'unique_clicks':
                return $ORM->where('ip', '!=', '')
                    ->select(
                        DB::raw("count(ip) as count"),
                        DB::raw("ip as log")
                    )
                    ->groupBy("log")
                    ->get();
                break;
            default:
                return [];
                break;
        }
    }

    /**
     * @OA\Get(
     *      path="/backstage/affiliate/bank/{account}/audit",
     *      operationId="backstage.affiliate.bank.audit",
     *      tags={"Backstage-代理"},
     *      summary="代理银行卡修改记录",
     *      @OA\Parameter(name="account", in="path", description="银行卡ID", @OA\Schema(type="integer")),
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
    public function affiliateBankAudit(UserBankAccount $account, Request $request)
    {
        $field = $request->field;

        $audits = $account->audits()->whereRaw("FIND_IN_SET(?, tags)", $field)->get();

        foreach ($audits as $audit) {
            $audit->new_value = $field == 'status' ? UserBankAccount::$statuses[$audit->new_values[$field]] : $audit->new_values[$field];
            $audit->old_value = $field == 'status' ? UserBankAccount::$statuses[$audit->old_values[$field]] : $audit->old_values[$field];
        }

        return $this->response->collection($audits, new AuditTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/affiliate/bank/history/{affiliate}?include=bank",
     *      operationId="api.affiliates.user_bank_accounts.store",
     *      tags={"Backstage-代理"},
     *      summary="代理历史使用银行卡",
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
    public function affiliateBankHistory(Affiliate $affiliate)
    {
        $userBankAccounts = UserBankAccount::query()->where('user_id', $affiliate->user_id)->get();

        if (!$userBankAccounts) {
            return $this->response->noContent();
        }

        return $this->response->collection($userBankAccounts, new UserBankAccountTransformer('affiliate_bank_account'));
    }

    /**
     * @OA\Get(
     *      path="/backstage/tracking/statistic/logs",
     *      operationId="backstage.tracking.statistic.logs",
     *      tags={"Backstage-代理"},
     *      summary="banner的访问日志",
     *      @OA\Parameter(name="filter[nauser_nameme]", in="query", description="代理ID", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[tracking_name]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="状态", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="开始", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="结束", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[code]", in="query", description="代理号", @OA\Schema(type="string")),
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
    public function trackingStatisticLogs(Request $request)
    {
        $trackingStatisticLogs = QueryBuilder::for(TrackingStatisticLog::class)
            ->allowedFilters(
                Filter::scope('user_name'),
                Filter::scope('tracking_name'),
                Filter::scope('currency'),
                Filter::scope('start_at'),
                Filter::scope('end_at'),
                Filter::scope('code')
            )
            ->orderByDesc('created_at')
            ->paginate($request->per_page);
        return $this->response->paginator($trackingStatisticLogs, new TrackingStatisticLogTransformer());
    }
}
