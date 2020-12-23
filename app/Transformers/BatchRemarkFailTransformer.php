<?php

namespace App\Transformers;

use App\Models\BatchRemarkFail;

/**
 * @OA\Schema(
 *   schema="FailRemark",
 *   type="object",
 *   @OA\Property(property="user_name", type="string", description="member Code"),
 *   @OA\Property(property="type", type="string", description="类型"),
 *   @OA\Property(property="category", type="string", description="分类"),
 *   @OA\Property(property="sub_category", type="string", description="子分类"),
 *   @OA\Property(property="reason", type="string", description="原因"),
 * )
 */
class BatchRemarkFailTransformer extends Transformer
{

    public function transform(BatchRemarkFail $remark)
    {
        $data =  [
            'user_name' => !empty($remark->user_name) ? $remark->user_name : "",
            'type' => !empty($remark->type) ? $remark->type : "",
            'category' => !empty($remark->category) ? $remark->category : "",
            'sub_category' => !empty($remark->sub_category) ? $remark->sub_category : "",
            'reason' => !empty($remark->reason) ? $remark->reason : "",
        ];

        return $data;
    }

}