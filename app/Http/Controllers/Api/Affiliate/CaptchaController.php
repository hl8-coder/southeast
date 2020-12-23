<?php

namespace App\Http\Controllers\Api\Affiliate;

use Illuminate\Http\Request;
use Gregwar\Captcha\CaptchaBuilder;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Cache;

class CaptchaController extends ApiController
{
    /**
     * @OA\Post(
     *      path="/affiliate/captcha",
     *      operationId="api.affiliate.captcha.store",
     *      tags={"Affiliate-代理"},
     *      summary="验证码",
     *      @OA\Response(
     *          response=201,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="captcha_key", type="string", description="验证标识"),
     *                  @OA\Property(property="expired_at", type="string", description="过期时间"),
     *                  @OA\Property(property="captcha_image_content", type="string", description="base 64 图片"),
     *          )
     *      ),
     *      @OA\Response(response=204,description="no content"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function store(Request $request, CaptchaBuilder $captchaBuilder)
    {
        $key = 'captcha-'.str_random(15);

        $captcha = $captchaBuilder->build();
        $expiredAt = now()->addMinutes(5);
        Cache::put($key, ['code' => $captcha->getPhrase()], $expiredAt);

        $result = [
            'captcha_key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
            'captcha_image_content' => $captcha->inline()
        ];

        return $this->response->array($result)->setStatusCode(201);
    }
}
