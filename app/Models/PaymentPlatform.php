<?php

namespace App\Models;

use App\Models\Traits\FlushCache;
use App\Models\Traits\RememberCache;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class PaymentPlatform extends Model implements Auditable
{
    use SoftDeletes, FlushCache, RememberCache, \OwenIt\Auditing\Auditable;

    protected $rememberCacheTag = 'payment_platform';

    public static $cacheExpireInMinutes = 43200;

    protected $auditInclude = [
        'remark',
        'max_deposit',
        'min_deposit',
        'is_fee',
        'fee_rebate',
        'max_fee',
        'min_fee',
        'sort',
        'status',
        'show_type',
        'related_name',
        'related_no',
    ];

    protected $dates = [
        'deleted_at',
    ];

    protected $casts = [
        'is_quickly_amount'    => 'boolean',
        'is_need_bank'         => 'boolean',
        'is_check_ip'          => 'boolean',
        'is_default'           => 'boolean',
        'is_fee'               => 'boolean',
        'is_sort'              => 'boolean',
        'is_no_empty'          => 'boolean',
        'max_deposit'          => 'float',
        'min_deposit'          => 'float',
        'fee_rebate'           => 'float',
        'min_fee'              => 'float',
        'max_fee'              => 'float',
        'configs'              => 'array',
        'front_mapping_fields' => 'array',
    ];

    protected $guarded = [];

    # 支付类型
    const PAYMENT_TYPE_BANKCARD = 1;
    const PAYMENT_TYPE_QUICKPAY = 2;
    const PAYMENT_TYPE_MPAY = 3;
    const PAYMENT_TYPE_SCRATCH_CARD = 4;
    const PAYMENT_TYPE_LINEPAY = 5;

    public static $paymentTypes = [
        self::PAYMENT_TYPE_BANKCARD     => 'Online Banking',
        self::PAYMENT_TYPE_QUICKPAY     => 'Quickpay',
        self::PAYMENT_TYPE_MPAY         => 'Mpay',
        self::PAYMENT_TYPE_SCRATCH_CARD => 'Scratch Card',
        self::PAYMENT_TYPE_LINEPAY      => 'LinePay',
    ];

    public static $paymentTypesForTranslation = [
        self::PAYMENT_TYPE_BANKCARD     => 'PAYMENT_TYPE_BANKCARD',
        self::PAYMENT_TYPE_QUICKPAY     => 'PAYMENT_TYPE_QUICKPAY',
        self::PAYMENT_TYPE_MPAY         => 'PAYMENT_TYPE_MPAY',
        self::PAYMENT_TYPE_SCRATCH_CARD => 'PAYMENT_TYPE_SCRATCH_CARD',
        self::PAYMENT_TYPE_LINEPAY      => 'PAYMENT_TYPE_LINEPAY',
    ];

    # Online Banking 类型
    const ONLINE_BANKING_CHANNEL_ATM                = 1;
    const ONLINE_BANKING_CHANNEL_INTERNET_BANKING   = 2;
    const ONLINE_BANKING_CHANNEL_MOBILE_BANKING     = 3;
    const ONLINE_BANKING_CHANNEL_OVER_THE_COUNTER   = 4;
    const ONLINE_BANKING_CHANNEL_CASH_DEPOSIT       = 5;

    public static $onlineBankingChannels = [
        self::ONLINE_BANKING_CHANNEL_ATM              => 'ATM',
        self::ONLINE_BANKING_CHANNEL_INTERNET_BANKING => 'Internet Banking',
        self::ONLINE_BANKING_CHANNEL_MOBILE_BANKING   => 'Mobile Banking',
        self::ONLINE_BANKING_CHANNEL_OVER_THE_COUNTER => 'Over the Counter',
        self::ONLINE_BANKING_CHANNEL_CASH_DEPOSIT     => 'Cash Deposit Machine',
    ];

    public static $onlineBankingChannelsForTranslation = [
        self::ONLINE_BANKING_CHANNEL_ATM              => 'ONLINE_BANKING_CHANNEL_ATM',
        self::ONLINE_BANKING_CHANNEL_INTERNET_BANKING => 'ONLINE_BANKING_CHANNEL_INTERNET_BANKING',
        self::ONLINE_BANKING_CHANNEL_MOBILE_BANKING   => 'ONLINE_BANKING_CHANNEL_MOBILE_BANKING',
        self::ONLINE_BANKING_CHANNEL_OVER_THE_COUNTER => 'ONLINE_BANKING_CHANNEL_OVER_THE_COUNTER',
        self::ONLINE_BANKING_CHANNEL_CASH_DEPOSIT     => 'ONLINE_BANKING_CHANNEL_CASH_DEPOSIT',
    ];

    public static $onlineTHBBankingChannelsForTranslation = [
        self::ONLINE_BANKING_CHANNEL_ATM              => 'ONLINE_BANKING_CHANNEL_ATM',
        self::ONLINE_BANKING_CHANNEL_INTERNET_BANKING => 'ONLINE_BANKING_CHANNEL_INTERNET_BANKING',
        self::ONLINE_BANKING_CHANNEL_MOBILE_BANKING   => 'ONLINE_BANKING_CHANNEL_MOBILE_BANKING',
    ];

    # 请求类型
    const REQUEST_TYPE_FROM = 1;
    const REQUEST_TYPE_QRCODE_URL = 2;
    const REQUEST_TYPE_QRCODE_IMG_URL = 3;
    const REQUEST_TYPE_QRCODE_BASE64 = 4;
    const REQUEST_TYPE_REDIRECT = 5;
    const REQUEST_TYPE_MESSAGE = 6;
    const REQUEST_TYPE_GET = 7;

    public static $requestTypes = [
        self::REQUEST_TYPE_FROM           => '前端表单提交',
        self::REQUEST_TYPE_QRCODE_URL     => 'QR Code 网页链接',
        self::REQUEST_TYPE_QRCODE_IMG_URL => 'QR Code 图片链接',
        self::REQUEST_TYPE_QRCODE_BASE64  => 'QR Code BASE64',
        self::REQUEST_TYPE_REDIRECT       => '前端导页',
        self::REQUEST_TYPE_MESSAGE        => '纯讯息',
        self::REQUEST_TYPE_GET            => '前端GET提交',
    ];

    # 请求方式 Start
    const REQUEST_METHOD_GET = 1;
    const REQUEST_METHOD_POST = 2;
    const REQUEST_METHOD_JSON_POST = 3;
    const REQUEST_METHOD_BODY_POST = 4;
    const REQUEST_METHOD_XML_POST = 5;


    public static $requestMethods = [
        self::REQUEST_METHOD_GET       => 'GET',
        self::REQUEST_METHOD_POST      => 'POST',
        self::REQUEST_METHOD_JSON_POST => 'JSON POST',
        self::REQUEST_METHOD_BODY_POST => 'BODY POST',
        self::REQUEST_METHOD_XML_POST  => 'XML POST',
    ];

    # 显示类型 start
    const SHOW_TYPE_ALL         = 1;
    const SHOW_TYPE_USER        = 2;
    const SHOW_TYPE_AFFILIATE   = 3;

    public static $showTypes = [
        self::SHOW_TYPE_ALL         => 'All',
        self::SHOW_TYPE_USER        => 'User',
        self::SHOW_TYPE_AFFILIATE   => 'Affiliate',
    ];

    public static $showTypeMapping = [
        self::SHOW_TYPE_ALL => [
            self::SHOW_TYPE_ALL,
            self::SHOW_TYPE_USER,
            self::SHOW_TYPE_AFFILIATE,
        ],
        self::SHOW_TYPE_USER => [
            self::SHOW_TYPE_ALL,
            self::SHOW_TYPE_USER,
        ],
        self::SHOW_TYPE_AFFILIATE => [
            self::SHOW_TYPE_ALL,
            self::SHOW_TYPE_AFFILIATE,
        ],
    ];

    # 模型关联 start
    public function banks()
    {
        return $this->belongsToMany(Bank::class)->withPivot('bank_code');
    }

    public function companyBankAccount()
    {
        return $this->belongsTo(CompanyBankAccount::class, 'id', 'platform_id');
    }
    # 模型关联 end

    # 属性修改器 start
    public function getDevicesAttribute($value)
    {
        return explode(',', $value);
    }

    public function setDevicesAttribute($value)
    {
        $this->attributes['devices'] = implode(',', $value);
    }

    public function getCountriesAttribute($value)
    {
        return explode(',', $value);
    }

    public function setCountriesAttribute($value)
    {
        $this->attributes['countries'] = implode(',', $value);
    }

    public function getRequestFieldsAttribute($value)
    {
        return explode(',', $value);
    }

    public function setRequestFieldsAttribute($value)
    {
        $this->attributes['request_fields'] = implode(',', $value);
    }

    public function getSignSkipFieldsAttribute($value)
    {
        return explode(',', $value);
    }

    public function setSignSkipFieldsAttribute($value)
    {
        $this->attributes['sign_skip_fields'] = implode(',', $value);
    }
    # 属性修改器 end

    # scope start
    public function scopeId($query, $value)
    {
        if ($value) {
            return $query->where('id', $value);
        } else {
            return $query;
        }
    }
    # scope end

    # 方法 start
    public static function findByCode($code)
    {
        return static::query()->where('code', $code)->first();
    }

    public static function findByRelatedNo($relatedNo)
    {
        return static::query()->where('related_no', $relatedNo)->first();
    }

    public function isActive()
    {
        return $this->status;
    }
    # 方法 end

    # 针对第三方通道 创建第三方对应的pg_account表数据.
    public static function boot()
    {
        parent::boot();

        # platform创建成功, 创建第三方.
        static::saved(function ($model) {
            # 通道类型为第三方通道类型 非银行卡类型.
            if(in_array($model->payment_type, [self::PAYMENT_TYPE_QUICKPAY, self::PAYMENT_TYPE_MPAY, self::PAYMENT_TYPE_SCRATCH_CARD, self::PAYMENT_TYPE_LINEPAY])) {

                $pgAccountModel = new PgAccount();

                # 判断是否存在
                $isExist = $pgAccountModel->where('payment_platform_code', $model->code)->first();

                if (empty($isExist)) {
                    $pgAccountModel->fill(['payment_platform_code' => $model->code, "current_balance" => 0, 'status' => $model->status ? true : false]);
                    $pgAccountModel->save();
                } else {
                    $isExist->status = $model->status ? true : false;
                    $isExist->save();
                }

            }
            $model->flushCache();

        });
    }

    public function pgAccount()
    {
        return $this->hasOne(PgAccount::class, 'payment_platform_code', 'code');
    }

    public function scopeCurrencies($query, $value)
    {
        return $query->where('currencies', 'like', '%' . $value . '%');
    }
}
