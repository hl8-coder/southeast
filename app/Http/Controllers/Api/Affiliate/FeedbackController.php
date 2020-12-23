<?php

namespace App\Http\Controllers\Api\Affiliate;

use App\Models\Currency;
use App\Jobs\SendEmailJob;
use App\Models\MailboxTemplate;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Affiliate\FeedbackRequest;

class FeedbackController extends ApiController
{
    /**
     * @OA\Post(
     *      path="/affiliate/feedback",
     *      operationId="api.affiliate.feedback.store",
     *      tags={"Affiliate-代理"},
     *      summary="添加反馈",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="name", type="string", description="用户名"),
     *                  @OA\Property(property="user_name", type="string", description="代理名"),
     *                  @OA\Property(property="email", type="string", description="邮箱"),
     *                  @OA\Property(property="phone", type="string", description="电话号码"),
     *                  @OA\Property(property="message", type="string", description="内容"),
     *                  @OA\Property(property="captcha_key", type="string", description="验证标识"),
     *                  @OA\Property(property="captcha_code", type="string", description="验证码"),
     *                  required={"name", "email", "message", "captcha_key", "captcha_code"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=204,description="no content"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function store(FeedbackRequest $request)
    {
        $data = remove_null($request->all());

        $currencyCode = $request->header('currency', 'VND');

        $emailConfig = config('multiple_mail')[$currencyCode];
        $emailConfig = $emailConfig['Affiliate'];

        $email = $emailConfig['username'];

        $currency     = Currency::findByCodeFromCache($currencyCode);
        $language     = 'en-US';

        if (!empty($currency)) {
            $language = $currency->preset_language;
        }

        $captchaData = Cache::get($data['captcha_key']);

        if (!$captchaData) {
            return $this->response->error('Verification code has expired', 422);
        }

        if (!hash_equals($captchaData['code'], $data['captcha_code'])) {

            Cache::forget($request->captcha_key);
            return $this->response->errorUnauthorized('Verification code error');
        }

        $text = '';
        foreach ($data as $key => $datum) {
            if ($key != 'captcha_key' && $key != 'captcha_code' && $key != 'path') {
                $text .= to_camel_case(ucfirst($key));
                $text .= ': ';
                $text .= $datum['value'];
                $text .= '<br>';
            }
        }

        $path = '';
        if (isset($data['path'])) {
            $path =  $data['path'];
        }

        dispatch(new SendEmailJob(MailboxTemplate::FEEDBACK, $email, $currencyCode, true, $language, $text, '', $path))->onQueue('send_email');

        return $this->response->noContent();
    }
}
