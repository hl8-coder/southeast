<?php


namespace App\Transformers;

use App\Models\BatchRemark;

/**
 * @OA\Schema(
 *   schema="BatchRemark",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="操作id"),
 *   @OA\Property(property="file", type="string", description="文件传相对地址"),
 *   @OA\Property(property="upload_by", type="string", description="上传的人"),
 *   @OA\Property(property="fail_num", type="integer", description="失败的条数"),
 *   @OA\Property(property="created_at", type="string", description="上传时间"),
 * )
 */
class BatchRemarkTransformer extends Transformer
{

    public function transform(BatchRemark $batchRemark)
    {
        return [
            'id'         => $batchRemark->id,
            'file'       => $batchRemark->file,
            'upload_by'  => $batchRemark->upload_by,
            'fail_num'   => $batchRemark->fail_num,
            'created_at' => convert_time($batchRemark->created_at),
        ];
    }
}