<?php

namespace App\Http\Controllers\Api;

use App\Models\Adjustment;
use App\Models\AffiliateLink;
use App\Models\Announcement;
use App\Models\Bank;
use App\Models\CreativeResource;
use App\Models\Currency;
use App\Models\GamePlatform;
use App\Models\Language;
use App\Models\TrackingStatistic;
use App\Models\TransferDetail;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\UserInfo;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Models\PaymentPlatform;
use App\Models\GamePlatformProduct;

class DropListController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/drop_list/{code}",
     *      operationId="api.drop_list.index",
     *      tags={"Api-平台"},
     *      summary="下拉列表",
     *      @OA\Parameter(
     *         name="code",
     *         in="path",
     *         description="模块code",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(),
     *          ),
     *      ),
     *     @OA\Response(response=401, description="授权不通过"),
     *     @OA\Response(response=422, description="验证错误"),
     *  )
     */
    public function index(Request $request)
    {
        $data = [];
        $code = to_camel_case($request->route('code'));
        if (method_exists($this, $code)) {
            $data = $this->$code();
        }

        foreach ($data as $key => $value) {
            $data[$key] = transform_list($value);
        }

        return $this->response->array($data);
    }

    public function paymentPlatform()
    {
        $data                           = [];
        $data['payment_type']           = transfer_lang_value('dropList', PaymentPlatform::$paymentTypesForTranslation);

        $user = Auth::guard('api')->user();
        if ($user && 'THB' == $user->currency) {
                $data['online_banking_channel'] = transfer_lang_value('dropList', PaymentPlatform::$onlineTHBBankingChannelsForTranslation);
        } else {
            $data['online_banking_channel'] = transfer_lang_value('dropList', PaymentPlatform::$onlineBankingChannelsForTranslation);
        }

        return $data;
    }

    public function transfer()
    {
        $data = [];

        if ($user = Auth::guard('api')->user()) {
            $data['platform_code'] = UserRepository::getActiveGamePlatformDropList($user, true);
        } else {
            $data['platform_code'] = GamePlatform::getDropListContainMainWallet();
        }
        return $data;
    }

    public function historyDepositWithdrawal()
    {
        $data           = [];
        $data['status'] = [
            1 => __('history.successful'),
            2 => __('history.failed'),
            3 => __('history.pending'),
            4 => __('history.cancel'),
            5 => __('history.processing'),
        ];

        $data['type'] = [
            'deposit'    => __('history.deposit'),
            'withdrawal' => __('history.withdrawal'),
        ];

        return $data;
    }

    public function affiliateHistoryDepositWithdrawal()
    {
        $data           = [];
        $data['status'] = [
            1 => __('history.successful'),
            2 => __('history.failed'),
            3 => __('history.pending'),
        ];

        $data['type'] = [
            'deposit'    => __('history.deposit'),
        ];

        return $data;
    }

    public function historyFundTransfer()
    {
        $data              = [];
        $data['fo_status'] = [
            1 => __('history.successful'),
            2 => __('history.failed'),
            3 => __('history.pending'),
        ];

        if ($user = Auth::guard('api')->user()) {
            $platformCodes = UserRepository::getActiveGamePlatformDropList($user);
        } else {
            $platformCodes = GamePlatform::getDropListContainMainWallet();
        }
        unset($platformCodes[UserAccount::MAIN_WALLET]);

        $data['platform_code'] = $platformCodes;

        return $data;
    }

    public function historyAdjustment()
    {
        $data              = [];
        $data["fo_status"] = [
            1 => __('history.successful'),
            2 => __('history.failed'),
            3 => __('history.pending'),
        ];
        $data['type']      = Adjustment::$frontTypes;

        return $data;
    }

    public function historyRebate()
    {
        $data = [];

        $data['product'] = GamePlatformProduct::getFrontDropList();

        return $data;
    }

    public function user()
    {
        $data = [];

        $data['gender']            = transfer_lang_value('dropList', UserInfo::$gendersForTranslation);
        $data['odds']              = transfer_lang_value('dropList', User::$oddsForTranslation);
        $data['security_question'] = transfer_lang_value('dropList', User::$securityQuestionForTranslation);
        $data['country_code']      = Currency::getAll()->pluck('country_code', 'country_code')->toArray();
        $data['language']          = Language::getTranslationDropList(true);
        if ($user = Auth::guard('api')->user()){
            $data['platform_code']     = UserRepository::getActiveGamePlatformDropList($user, true);
            unset($data['platform_code'][UserAccount::MAIN_WALLET]);
        }else{
            $data['platform_code'] = [];
        }

        return $data;
    }

    public function affiliate()
    {
        $data = [];

        $data['gender']            = transfer_lang_value('dropList', UserInfo::$gendersForTranslation);
        $data['odds']              = transfer_lang_value('dropList', User::$oddsForTranslation);
        $data['security_question'] = transfer_lang_value('dropList', User::$securityQuestionForTranslation);
        $data['country_code']      = Currency::getAll()->pluck('country', 'country_code')->toArray();
        $currency                  = Currency::getDropList('preset_language', 'name');
        unset($currency['CNY']);
        $data['currency']          = $currency;

        return $data;
    }


    public function bank()
    {
        $data         = [];
        $currency     = request()->header('currency');
        $data['bank'] = Bank::getFrontDropList($currency);
        return $data;
    }

    public function creativeResource()
    {
        $data['type']        = transfer_lang_value('dropList', CreativeResource::$platformForTranslation);
        $data['size']        = remove_null(CreativeResource::$size);
        $data['group']       = transfer_lang_value('dropList', CreativeResource::$group);
        $data['currency']    = Currency::getDropList();
        return $data;
    }

    public function trackingStatisticLog()
    {
        $data['id'] = TrackingStatistic::getDropList();
        return $data;
    }

    public function downLineManagement()
    {
        $data['is_agent'] = transfer_lang_value('dropList', User::$agent);
        return $data;
    }

    public function fundManagement()
    {
        $data['status']   = transfer_lang_value('dropList', TransferDetail::$statuses);
        $data['is_agent'] = transfer_lang_value('dropList', User::$agent);
        return $data;
    }

    public function product()
    {
        $data                           = [];
        $data['product_type']           = transfer_lang_value('dropList', GamePlatformProduct::$typesForTranslation);
        $data['affiliate_product_type'] = transfer_lang_value('dropList', GamePlatformProduct::$typesForTranslation);

        return $data;
    }

    public function announcement()
    {
        $data               = [];
        $data['category']   = transfer_lang_value('dropList', Announcement::$categoryForTranslation);

        return $data;
    }

    public function affiliateLink()
    {
        $data             = [];
        $data['type']     = transfer_lang_value('dropList', AffiliateLink::$typesForTranslation);
        $data['platform'] = transfer_lang_value('dropList', AffiliateLink::$platformForTranslation);
        $data['status']   = AffiliateLink::$status;
        $data['currency'] = Currency::getDropList();
        $data['language'] = Language::getDropList();
        return $data;
    }
}
