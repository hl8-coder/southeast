<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\NotificationRequest;
use App\Models\DatabaseNotification;
use App\Transformers\NotificationReplyTransformer;
use App\Transformers\NotificationTransformer;
use Illuminate\Http\Request;

class NotificationsController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/user/notifications?include=replies",
     *      operationId="api.notifications.index",
     *      tags={"Api-消息通知"},
     *      summary="消息通知列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Notification"),
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

        $notifications = DatabaseNotification::query()->where('notifiable_type', 'App\Models\User')
                            ->where('notifiable_id', $this->user->id)
                            ->whereNull('deleted_at')
                            ->latest()
                            ->paginate($request->per_page);

        return $this->response->paginator($notifications, new NotificationTransformer('front_index'));
    }

    /**
     * @OA\Patch(
     *      path="/user/notifications/read",
     *      operationId="api.notifications.read",
     *      tags={"Api-消息通知"},
     *      summary="标记消息通知读取",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="notification_ids", type="array", description="消息通知id数组", @OA\Items()),
     *                  required={"notification_ids"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=204, description="No Content"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function read(NotificationRequest $request)
    {
        $user = $this->user;

        $notifications = DatabaseNotification::query()->where('notifiable_id', $user->id)->whereIn('id', $request->notification_ids)->get();

        foreach ($notifications as $notification) {
            $user->markAsRead($notification);
        }
        return $this->response->noContent();
    }

    /**
     * @OA\Delete(
     *      path="/user/notifications",
     *      operationId="api.notifications.delete",
     *      tags={"Api-消息通知"},
     *      summary="删除消息通知",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="notification_ids", type="array", description="消息通知id数组", @OA\Items()),
     *                  required={"notification_ids"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=204, description="No Content"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function destroy(NotificationRequest $request)
    {
        $this->user->notifications()->whereIn('id', $request->notification_ids)->update([
            'deleted_at' => now(),
        ]);

        return $this->response->noContent();
    }

    /**
     * @OA\Patch(
     *      path="/notifications/{notification}/reply",
     *      operationId="api.notifications.reply",
     *      tags={"Api-消息通知"},
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
        $this->authorize('own', $notification);

        $reply = $notification->replies()->create([
            'message' => $request->message
        ]);

        return $this->response->item($reply, new NotificationReplyTransformer());
    }
}
