<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchRemark extends Model
{
    public $fillable = [
        'file', 'upload_by','success_num','fail_num','total_num'
    ];

    public function remarks()
    {
        return $this->hasMany(Remark::class, 'batch_remark_id');
    }

    public function fails()
    {
        return $this->hasMany(BatchRemarkFail::class, 'batch_remark_id');
    }
}
