<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\NotificationRequest;
use App\Imports\UserMessageImport;
use App\Models\DatabaseNotification;
use App\Models\NotificationMessage;
use App\Models\User;
use App\Services\NotificationService;
use App\Transformers\NotificationMessageTransformer;
use App\Transformers\NotificationReplyTransformer;
use App\Transformers\NotificationTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Filter;

class NotificationsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/notifications",
     *      operationId="backstage.notifications.index",
     *      tags={"Backstage-消息通知"},
     *      summary="获取消息通知列表",
     *      @OA\Parameter(name="filter[start_at]", in="query", description="创建查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[category]", in="query", description="分类", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[user_name]", in="query", description="用户名", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="创建查询结束时间", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/NotificationMessage"),
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
        $notifications = QueryBuilder::for(NotificationMessage::class)
            ->allowedFilters(
                Filter::exact('category'),
                Filter::scope('start_at'),
                Filter::scope('user_name'),
                Filter::scope('currency'),
                Filter::scope('end_at')
            )
            ->latest()
            ->paginate($request->per_page);

        return $this->response->paginator($notifications, new NotificationMessageTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/notifications",
     *      operationId="backstage.notifications.store",
     *      tags={"Backstage-消息通知"},
     *      summary="添加消息通知",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="names", type="string", description="接收会员名名称，支持传入多个名称批量发送，当批量发送时每个用户名用逗号间隔", @OA\Items()),
     *                  @OA\Property(property="excel", type="file", description="接收会员名名称，支持Excel导入"),
     *                  @OA\Property(property="category", description="分类", type="string"),
     *                  @OA\Property(property="message", description="内容", type="string"),
     *                  @OA\Property(property="start_at", description="开始时间", type="date"),
     *                  @OA\Property(property="end_at", description="结束时间", type="date"),
     *                  @OA\Property(property="member_status", type="int", description="会员状态"),
     *                  required={"category", "message", "member_status"},
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="successful operation",
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function store(NotificationRequest $request)
    {
        $data                = remove_null($request->all());
        $usernameArray       = $this->getUsernameArray($request);
        $total               = count($usernameArray);
        $users               = User::query()
            ->where(function ($query) use ($request) {
                if (!empty($request->start_at)) {
                    $query->where('created_at', '>=', $request->start_at);
                }
                if (!empty($request->end_at)) {
                    $query->where('created_at', '<=', $request->end_at);
                }
                $query->where('status', $request->status);
            })
            ->whereIn('name', $usernameArray)
            ->get();
        $failureNum = $total -  count($users);
        $notificationService = new NotificationService();
        $notificationMessage = $notificationService->notificationMessageStore($this->user, $data, $total, $failureNum);
        if (!is_object($notificationMessage)) {
            return $this->response->error('notification message error', 422);
        }

        $notification = 'App\\Notifications\\NotificationMsg';

        Notification::send($users, new $notification($request->message, $this->user, $notificationMessage));

        return $this->response->noContent();
    }

    public function getUsernameArray(Request $request)
    {
        if (isset($request->names)) {
            $usernameArray = $request->names;
        } else {
            $usernameArray = explode(',', $request->name);
        }
        return $usernameArray;
    }

    /**
     * @OA\Get(
     *      path="/backstage/notifications/{notificationMessage}?include=user",
     *      operationId="backstage.users.notifications.show",
     *      tags={"Backstage-消息通知"},
     *      summary="获取消息通知详情",
     *     @OA\Parameter(
     *         name="notificationMessage",
     *         in="path",
     *         description="消息通知id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Notification"),
     *          ),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function show(NotificationMessage $notificationMessage)
    {
        $notifications = DatabaseNotification::query()->where('notification_message_id', $notificationMessage->id)
            ->latest()
            ->paginate(request()->per_page);
        return $this->response->paginator($notifications, new NotificationTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/notifications/{notification}/reply",
     *      operationId="backstage.notifications.reply",
     *      tags={"Backstage-消息通知"},
     *      summary="回复消息通知",
     *       @OA\Parameter(
     *         name="notification",
     *         in="path",
     *         description="消息通知id",
     *         required=true,
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
     *                  @OA\Property(property="message", type="string", description="回复内容"),
     *                  required={"message"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/NotificationReply"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function reply(DatabaseNotification $notification, NotificationRequest $request)
    {
        $reply = $notification->replies()->create([
            'message'    => $request->message,
            'admin_name' => $this->user->name,
        ]);

        return $this->response->item($reply, new NotificationReplyTransformer());
    }


    /**
     * @OA\Get(
     *      path="/backstage/notifications/{notification}/detail?include=user,replies",
     *      operationId="backstage.notifications.detail",
     *      tags={"Backstage-消息通知"},
     *      summary="回复消息通知",
     *       @OA\Parameter(
     *         name="notification",
     *         in="path",
     *         description="消息通知id",
     *         required=true,
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
     *                  @OA\Property(property="message", type="string", description="消息详情"),
     *                  required={"message"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/NotificationReply"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function detail(DatabaseNotification $notification, NotificationRequest $request)
    {
        return $this->response()->item($notification, new NotificationTransformer());
    }
}
