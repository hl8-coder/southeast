<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\VerificationCodeRequest;
use App\Jobs\NexmoJob;
use App\Jobs\SendEmailJob;
use App\Models\MailboxTemplate;
use App\Services\SMSService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class VerificationController extends ApiController
{
    /**
     * @OA\get(
     *      path="/user/send_phone_code",
     *      operationId="api.send_phone_code",
     *      tags={"Api-会员"},
     *      summary="发送手机验证码",
     *      @OA\Response(
     *          response=201,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="key", type="string", description="唯一标识"),
     *              @OA\Property(property="expired_at", type="string", description="过期时间"),
     *          )
     *      ),
     *      @OA\Response(response=422, description="短信发送失败"),
     * )
     */
    public function sendPhoneCode()
    {
        $user = $this->user();

        $phoneNum = $user->info->phone;

        $this->checkLimitCodeCache($phoneNum);

        $countryCode = $user->info->country_code;
        # 根据API文档，需要去掉加号
        $countryCode = str_replace('+', '', $countryCode);
        # 拼接手机号
        $phone = $countryCode.$phoneNum;
        # 生成4位随机数，左侧补0
        $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);

        Log::stack(['fly_one_talk'])->info('用户名: '. $user->name . ', 手机号: '. $phone);

        # 越南使用nexmo，泰国使用fly_one_talk
        $isNexmo = $user->currency == 'VND' ? true : false;
        dispatch(new NexmoJob($phone, $code, $user->currency, $isNexmo))->onQueue('send_message');

        $this->putLimitCodeCache($phoneNum);

        return $this->createCode('phone', $phone, $code);
    }

    /**
     * @OA\get(
     *      path="/user/send_nexmo_code",
     *      operationId="api.send_nexmo_code",
     *      tags={"Api-会员"},
     *      summary="发送手机验证码-Nexmo",
     *      @OA\Response(
     *          response=201,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="key", type="string", description="唯一标识"),
     *              @OA\Property(property="expired_at", type="string", description="过期时间"),
     *          )
     *      ),
     *      @OA\Response(response=422, description="短信发送失败"),
     * )
     */
    public function sendNexmoCode()
    {
        $user = $this->user();

        $phoneNum = $user->info->phone;

        $this->checkLimitCodeCache($phoneNum);

        $countryCode = $user->info->country_code;
        # 根据API文档，需要去掉加号
        $countryCode = str_replace('+', '', $countryCode);

        # 拼接手机号
        $phone = $countryCode.$phoneNum;
        # 生成4位随机数，左侧补0
        $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);

        Log::stack(['nexmo'])->info('用户名: '. $user->name . ', 手机号: '. $phone);

        dispatch(new NexmoJob($phone, $code, $user->currency))->onQueue('send_nexmo');

        $this->putLimitCodeCache($phoneNum);

        return $this->createCode('phone', $phone, $code);
    }

    /**
     * @OA\get(
     *      path="/user/send_email_code",
     *      operationId="api.send_email_code",
     *      tags={"Api-会员"},
     *      summary="发送邮箱验证码",
     *      @OA\Response(
     *          response=201,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="key", type="string", description="唯一标识"),
     *              @OA\Property(property="expired_at", type="string", description="过期时间"),
     *          )
     *      ),
     *      @OA\Response(response=422, description="邮箱发送失败"),
     * )
     */
    public function sendEmailCode()
    {
        $user = $this->user();

        $email = $user->info->email;

        $this->checkLimitCodeCache($email);

        # 生成4位随机数，左侧补0
        $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);

        Log::stack(['email'])->info('用户名: '. $user->name . ', 邮箱: '. $email);

        dispatch(new SendEmailJob(MailboxTemplate::VERIFY_EMAIL, $email, $user->currency, $user->is_agent, $user->language, $code,$user->name))->onQueue('send_email');

        $this->putLimitCodeCache($email);

        return $this->createCode('email', $email, $code);
    }

    public function createCode($item, $value, $code)
    {
        $key = 'verificationCode_'.str_random(11).'_'.$item;

        $expiredAt = now()->addMinutes(20);

        # 缓存验证码 20分钟过期。
        Cache::put($key, [$item => $value, 'code' => $code], $expiredAt);

        return $this->response->array([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }

    /**
     * 检查1分钟限制
     *
     * @param $code
     */
    public function checkLimitCodeCache($code)
    {
        $key = 'limitCodeCache_' . $code;

        if (Cache::has($key)) {
            error_response(422, __('verification.verification_code_too_often'));
        }
    }

    /**
     * 保存1分钟限制
     *
     * @param $code
     */
    public function putLimitCodeCache($code)
    {
        $key = 'limitCodeCache_' . $code;

        $expiredAt = now()->addMinute();

        # 缓存验证码 1分钟过期。
        Cache::put($key, now(), $expiredAt);
    }

    /**
     * @OA\post(
     *      path="/user/verify_code",
     *      operationId="api.verify_code",
     *      tags={"Api-会员"},
     *      summary="校验验证码",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="type", type="string", description="验证的类型，可选参数['email', 'phone']"),
     *                  @OA\Property(property="verification_key", type="string", description="唯一标识"),
     *                  @OA\Property(property="code", type="integer", description="验证码"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=204,description="no content"),
     *      @OA\Response(response=401, description="无效token"),
     *      @OA\Response(response=422, description="验证失败"),
     * )
     */
    public function verificationCode(VerificationCodeRequest $request)
    {
        $verifyData = Cache::get($request->verification_key);

        if (!$verifyData) {
            return $this->response->error(__('verification.VERIFICATION_CODE_EXPIRED'), 422);
        }

        if (!hash_equals($verifyData['code'], $request->code)) {
            return $this->response->error(__('verification.VERIFICATION_CODE_ERROR'), 422);
        }

        $type = $request->type;

        $user = $this->user();

        switch ($type)
        {
            case 'phone':
                $user->info->phone_verified_at = now();
                break;
            case 'email':
                $user->info->email_verified_at = now();
                break;
        }

        $user->info->save();

        # 清除验证码缓存
        Cache::forget($request->verification_key);

        return $this->response->noContent();
    }
}
