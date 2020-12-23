<?php

namespace App\Http\Controllers\Backstage;

use App\Exports\ExcelTemplateExport;
use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\UserMessageRequest;
use App\Imports\UserMessageImport;
use App\Models\UserMessageDetail;
use App\Transformers\UserMessageDetailsTransformer;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\UserMessage;
use App\Repositories\UserRepository;
use App\Services\UserMessageService;
use App\Transformers\UserMessageTransformer;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Filter;

class UserMessagesController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/user_messages",
     *      operationId="backstage.user_messages.index",
     *      tags={"Backstage-会员短信"},
     *      summary="获取系统发给会员的信息列表",
     *      @OA\Parameter(name="filter[start_at]", in="query", description="创建查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="创建查询结束时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[category]", in="query", description="短信分类", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/UserMessage"),
     *          ),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function index(Request $request)
    {
        $news = QueryBuilder::for(UserMessage::class)
            ->allowedFilters(
                Filter::exact('category'),
                Filter::scope('start_at'),
                Filter::scope('end_at'),
                Filter::scope('currency')
            )
            ->latest()
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page);

        return $this->response->paginator($news, new UserMessageTransformer('index'));
    }

    /**
     * @OA\Get(
     *      path="/backstage/user_messages/{userMessage}",
     *      operationId="backstage.user_messages.show",
     *      tags={"Backstage-会员短信"},
     *      summary="获取会员短信详情",
     *      @OA\Parameter(
     *         name="userMessage",
     *         in="path",
     *         description="会员短信id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/UserMessageDetail")
     *      ),
     *      @OA\Response(response=403, description="会员信息不存在"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function show(UserMessage $userMessage)
    {
        $userMessageDetails = UserMessageDetail::where('user_message_id', $userMessage->id)
            ->paginate(request()->per_page);
        return $this->response->paginator($userMessageDetails, new UserMessageDetailsTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/user_messages",
     *      operationId="backstage.user_messages.store",
     *      tags={"Backstage-会员短信"},
     *      summary="发送会员短信",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="user_name", type="string", description="接收会员名名称，支持传入多个名称批量发送，当批量发送时每个用户名用逗号间隔"),
     *                  @OA\Property(property="excel", type="file", description="接收会员名名称，支持Excel导入"),
     *                  @OA\Property(property="category", type="int", description="分类"),
     *                  @OA\Property(property="content", type="string", description="内容"),
     *                  @OA\Property(property="member_status", type="int", description="会员状态"),
     *                  required={"category", "content", "member_status"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="发送结果",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="total_count", description="发送总数量", type="integer"),
     *              @OA\Property(property="failed_count", description="失败总数量", type="integer"),
     *              @OA\Property(property="failed_infos", description="失败明细", type="object",
     *                  @OA\Property(property="invalid_phone", description="无效会员电话列表", type="object"),
     *                  @OA\Property(property="error_name", description="无效会员列表", type="object"),
     *                  @OA\Property(property="error_save", description="保存失败会员列表", type="object"),
     *                  @OA\Property(property="error_status", description="不是选择的状态的会员列表", type="object"),
     *              ),
     *          ),
     *       ),
     *
     *       @OA\Property(
     *          example={"total_count":10,"faild_count":1,"failed_infosa":{"invalid_phone":{"zhangsan","lisi"}}}
     *      ),
     *
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="消息错误"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function store(UserMessageRequest $request)
    {
        $data = remove_null($request->all());

        if ($request->file('excel')) {
            $users = Excel::toArray(new UserMessageImport(), $request->file('excel'));
            foreach ($users[0] as $key => $value) {
                if (0 == $key) {
                    continue;
                }

                $usernameArray[] = $value[0];
            }
        } else {
            $usernameArray = explode(',', $data['user_name']);
        }

        $errorUsername  = $invalidPhone = $errorSave = $errorStatus = [];
        $total          = count($usernameArray);
        $failed         = 0;
        $messageService = new UserMessageService();
        $userMessage    = $messageService->userMessageStore($request->user(), $data);
        if (!is_object($userMessage)) {
            return $this->response->error(__('userMessage.USER_MESSAGE_ERROR'), 422);
        }
        foreach ($usernameArray as $username) {
            $user = UserRepository::findByName($username);
            if (!is_object($user)) {
                $failed++;
                $errorUsername[] = $username;
                continue;
            }
            if ($user->status != $data['member_status']) {
                $failed++;
                $errorStatus[] = $username;
                continue;
            }
            $phone = $user->info->phone;
            if (!$user->phone_verified_at && empty($phone)) {
                $failed++;
                $invalidPhone[] = $username;
                continue;
            }

            $userMessageDetails = $messageService->userMessageDetailsStore($user, $phone, $userMessage);
            if (!is_object($userMessageDetails)) {
                $failed++;
                $errorSave[] = $username;
                continue;
            }
            $userMessage->increment('number');
            $userMessageDetails->addSendJob();
        }

        $result = [
            'total_count'  => $total,
            'failed_count' => $failed,
            'failed_infos' => [
                'invalid_phone' => $invalidPhone,
                'error_name'    => $errorUsername,
                'error_save'    => $errorSave,
                'error_status'  => $errorStatus,
            ],
        ];

        return $this->response->array($result);
    }

    /**
     * @OA\Get(
     *      path="/backstage/user_messages/excel/download",
     *      operationId="backstage.users.user_messages.downloadExcelTemplate",
     *      tags={"Backstage-会员短信"},
     *      summary="下载模板",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\MediaType(
     *              mediaType="application/vnd.ms-excel",
     *              @OA\Items(ref="#/components/schemas/ExcelTemplateExport"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function downloadUserMessageExcelTemplate()
    {

        $headings = [
            'Member_code'
        ];
        return Excel::download(new ExcelTemplateExport([], $headings), 'message.xlsx');
    }
}
