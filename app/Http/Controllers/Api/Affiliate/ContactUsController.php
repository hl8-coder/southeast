<?php

namespace App\Http\Controllers\Api\Affiliate;

use App\Http\Controllers\ApiController;
use App\Models\ContactInformation;
use App\Transformers\ContactInformationTransformer;
use Illuminate\Http\Request;

class ContactUsController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/affiliate/contact_us",
     *      operationId="api.affiliate.contact_us.index",
     *      tags={"Affiliate-代理"},
     *      summary="联系我们",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/ContactInformation"),
     *          ),
     *       ),
     *      @OA\Response(response=204,description="no content"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function index(Request $request)
    {
        $currency = $request->header('currency', 'VND');
        $info = ContactInformation::getAll()
            ->where("is_affiliate", true)
            ->where("is_enable", true)
            ->filter(function ($value) use ($currency) {
                return $value->checkCurrencySet($currency);
            });
        return $this->response->collection($info, new ContactInformationTransformer('front_index'));
    }
}
