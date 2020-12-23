<?php


namespace App\Transformers;


use App\Models\BatchAdjustment;

class BatchAdjustmentTransformer extends Transformer
{

    public function transform(BatchAdjustment $adjustment)
    {
        return [
            'id'         => $adjustment->id,
            'type'       => $adjustment->type,
            'file'       => $adjustment->file,
            'upload_by'  => $adjustment->upload_by,
            'created_at' => convert_time($adjustment->created_at),
        ];
    }
}