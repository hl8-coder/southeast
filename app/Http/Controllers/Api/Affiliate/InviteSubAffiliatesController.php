<?php

namespace App\Http\Controllers\Api\Affiliate;

use App\Jobs\SendEmailJob;
use App\Models\MailboxTemplate;
use App\Http\Controllers\ApiController;
use App\Models\Url;
use App\Models\User;
use App\Transformers\MailboxTemplateTransformer;
use App\Http\Requests\Affiliate\InviteSubAffiliateRequest;

class InviteSubAffiliatesController extends ApiController
{
    /**
     * @OA\Post(
     *      path="/affiliate/send_invite_email",
     *      operationId="api.affiliates.send_invite_email",
     *      tags={"Affiliate-代理"},
     *      summary="发送邀请函",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="email", type="array", description="邮箱", @OA\Items()),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=204, description="No Content"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *         {"bearer": {}}
     *      }
     *  )
     */
    public function sendInviteEmail(InviteSubAffiliateRequest $request)
    {
        $user             = $this->user();
        $emails           = $request->email;
        $suffix           = Url::$suffix[1];
        $inviteSubAffLink = Url::where([
            [
                'currencies', 'like', '%' . $user->currency . '%'
            ],
            [
                'type', Url::TYPE_AFFILIATE
            ],
            [
                'device', User::DEVICE_PC
            ]
        ])
            ->first();
        $url              = empty($inviteSubAffLink) ? '' : $inviteSubAffLink->address . $suffix . '?affiliate_id=' . $user->affiliate->id . '&affiliate_code=' . $user->affiliate_code;

        foreach ($emails as $email) {
            dispatch(new SendEmailJob(MailboxTemplate::INVITE_SUB_AFF, $email, $user->currency, true, $user->language, $url))->onQueue('send_email');
        }

        return $this->response->noContent();
    }

    /**
     * @OA\Get(
     *      path="/affiliate/invite_email_template",
     *      operationId="api.affiliates.invite_email_template",
     *      tags={"Affiliate-代理"},
     *      summary="邀请函模板",
     *      @OA\Response(response=204, description="No Content"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *         {"bearer": {}}
     *      }
     *  )
     */
    public function inviteEmailTemplate()
    {
        $user = $this->user();

        $emailTemplate = MailboxTemplate::where('type', MailboxTemplate::INVITE_SUB_AFF)->first();
        return $this->response->item($emailTemplate, new MailboxTemplateTransformer($user->currency));
    }
}
