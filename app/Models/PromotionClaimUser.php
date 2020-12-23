<?php

namespace App\Models;

class PromotionClaimUser extends Model
{
    protected $guarded = [];

    # 状态 该属性下的值只能改变大小写，如若修改，请将翻译文件中的 key 一起修改
    const STATUS_CREATED  = 1;
    const STATUS_APPROVE  = 2;
    const STATUS_REJECT   = 3;

    public static $statuses = [
        self::STATUS_CREATED  => 'PENDING',
        self::STATUS_APPROVE  => 'APPROVE',
        self::STATUS_REJECT   => 'REJECT',
    ];

    public static $frontStatuses = [
        self::STATUS_CREATED  => 'PENDING',
        self::STATUS_APPROVE  => 'APPROVE',
        self::STATUS_REJECT   => 'REJECT',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    # 查询作用域 start
    public function scopeCurrency($query, $value)
    {
        return $query->whereHas('user', function($query) use ($value) {
            $query->where('currency', $value);
        });
    }

    public function scopeDateFrom($query, $value)
    {
        return $query->where('created_at', '>=', $value);
    }

    public function scopeDateTo($query, $value)
    {
        return $query->where('created_at', '<', $value);
    }
    # 查询作用域 end

    public function isCreated()
    {
        return static::STATUS_CREATED == $this->status;
    }

    public static function isAlreadyClaimed($promotionId, $userId)
    {
        return static::query()->where('user_id', $userId)->where('promotion_id', $promotionId)->exists();
    }

    public static function isClaimed($promotionId, $userId)
    {
        return static::query()->where('promotion_id', $promotionId)->where('user_id', $userId)->exists();
    }

    /**
     * 添加优惠报名
     *
     * @param Promotion $promotion
     * @param User $user
     * @param null $relatedModel
     * @param string $frontRemark
     * @return PromotionClaimUser
     */
    public static function add(Promotion $promotion, User $user, $relatedModel=null, $frontRemark='')
    {
        $promotionClaimUser = new static([
            'promotion_id'      => $promotion->id,
            'promotion_code'    => $promotion->code,
            'user_id'           => $user->id,
            'user_name'         => $user->name,
            'related_type'      => $promotion->related_type,
            'status'            => static::STATUS_CREATED,
            'front_remark'      => $frontRemark ?? '',
        ]);

        if ($relatedModel) {
            $promotionClaimUser->related_id = $relatedModel->id;
            $promotionClaimUser->related_code = $relatedModel->code;
        }

        $promotionClaimUser->save();

        return $promotionClaimUser;
    }

}
