<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateRemark extends Model
{
    protected $fillable = [
        'affiliate_id', 'reason','remark','admin_name'
    ];

    public static function store($affiliateId, $reason, $remark, $adminName)
    {
        $affiliateRemark = new static();
        $affiliateRemark->affiliate_id    = $affiliateId;
        $affiliateRemark->reason  		  = $reason;
        $affiliateRemark->remark          = $remark;
        $affiliateRemark->admin_name       = $adminName;
        $affiliateRemark->save();

    }

    # 查询作用域 start
    public function scopeRemark($query, $value)
    {
        return $query->where("remark" , "like", "%" . $value . "%");
    }
    # 查询作用域 end
}
