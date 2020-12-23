<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchRemarkFail extends Model
{
    public $fillable = [
        'batch_remark_id', 'user_name','type','category','sub_category','reason'
    ];

}
