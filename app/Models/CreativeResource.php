<?php

namespace App\Models;

class CreativeResource extends Model
{
    protected $fillable = [
        'type', 'group', 'size', 'currency', 'code',
        'banner_path', 'banner_url', 'last_update_by'
    ];

    protected $casts = [
        'currency' => 'array',
    ];

    # type
    const TYPE_PC     = 1;
    const TYPE_MOBILE = 2;

    public static $type = [
        self::TYPE_PC     => 'PC',
        self::TYPE_MOBILE => 'MOBILE',
    ];

    public static $platformForTranslation = [
        self::TYPE_PC       => 'desktop',
        self::TYPE_MOBILE   => 'mobile',
    ];

    # Group
    const GROUP_GENERAL   = 1;
    const GROUP_SIGN_UP   = 2;
    const GROUP_COCKFIGHT = 3;
    const GROUP_CASINO    = 4;
    const GROUP_LOTTERY   = 5;

    public static $group = [
        self::GROUP_GENERAL   => 'General',
        self::GROUP_SIGN_UP   => 'Sign Up',
        self::GROUP_COCKFIGHT => 'Cockfight',
        self::GROUP_CASINO    => 'Casino',
        self::GROUP_LOTTERY   => 'Lottery',
    ];

    #Size
    public static $size = [
        null, "50 x 50", "60 x 60", "80 x 35", "80 x 60", "80 x 80", "80 x 170", "84 x 84",
        "85 x 35", "88 x 31", "90 x 90", "90 x 190", "100 x 60", "100 x 100", "100 x 300",
        "110 x 420", "120 x 20", "120 x 50", "120 x 60", "120 x 90", "120 x 120", "120 x 240",
        "120 x 300", "120 x 350", "120 x 378", "120 x 400", "120 x 500", "120 x 600", "125 x 125",
        "140 x 250", "140 x 600", "145 x 50", "145 x 380", "150 x 120", "150 x 150", "150 x 450",
        "150 x 500", "156 x 30", "156 x 35", "160 x 90", "160 x 160", "160 x 300", "160 x 500",
        "160 x 600", "175 x 110", "180 x 90", "180 x 150", "180 x 180", "180 x 480", "180 x 600",
        "180 x 720", "192 x 142", "200 x 60", "200 x 90", "200 x 200", "200 x 300", "210 x 120",
        "220 x 124", "220 x 220", "224 x 33", "234 x 60", "240 x 400", "250 x 60", "250 x 80",
        "250 x 250", "250 x 500", "260 x 120", "272 x 234", "283 x 549", "285 x 175", "300 x 40",
        "300 x 50", "300 x 60", "300 x 100", "300 x 120", "300 x 180", "300 x 200", "300 x 240",
        "300 x 250", "300 x 300", "300 x 600", "300 x 900", "315 x 60", "315 x 850", "320 x 50",
        "320 x 60", "320 x 100", "320 x 280", "330 x 280", "336 x 280", "350 x 80", "350 x 160",
        "360 x 700", "370 x 80", "377 x 250", "380 x 60", "384 x 284", "400 x 60", "400 x 180",
        "400 x 195", "428 x 60", "460 x 60", "468 x 60", "468 x 70", "468 x 90", "470 x 60",
        "480 x 70", "490 x 80", "500 x 60", "500 x 90", "500 x 166", "500 x 260", "500 x 350",
        "510 x 100", "515 x 100", "520 x 100", "550 x 60", "550 x 70", "550 x 150", "565 x 90",
        "570 x 60", "590 x 75", "600 x 60", "600 x 120", "600 x 140", "600 x 400", "600 x 430",
        "626 x 80", "626 x 86", "630 x 70", "650 x 90", "660 x 24", "660 x 25", "663 x 100",
        "665 x 90", "669 x 90", "720 x 90", "720 x 300", "725 x 55", "728 x 80", "728 x 90",
        "728 x 180", "728 x 290", "748 x 70", "760 x 107", "770 x 90", "776 x 60", "782 x 90",
        "790 x 100", "800 x 75", "818 x 60", "890 x 90", "900 x 300", "935 x 60", "940 x 100",
        "950 x 100", "950 x 150", "960 x 60", "960 x 200", "960 x 300", "980 x 45", "980 x 90",
        "980 x 100", "980 x 120", "980 x 130", "980 x 184", "995 x 300", "1000 x 50",
        "1000 x 60", "1000 x 70", "1000 x 90", "1000 x 140", "1000 x 180", "1000 x 250",
        "1006 x 80", "1010 x 250", "1024 x 100", "1920 x 200",
    ];

    # 查询作用域 start
    public function scopeCurrency($query, $value)
    {
        return $query->where('currency', 'like', '%' . $value . '%');
    }

    public function scopeStartAt($query, $value)
    {
        return $query->where('created_at', '>=', $value);
    }

    public function scopeEndAt($query, $value)
    {
        return $query->where('created_at', '<=', $value);
    }

    # 查询作用域 end

    public function checkCurrencySet($currency)
    {
        return in_array($currency, $this->currencies);
    }
}
