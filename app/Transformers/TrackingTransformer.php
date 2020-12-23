<?php


namespace App\Transformers;

class TrackingTransformer extends Transformer
{
    public function transform($data)
    {
        return [
            'tracking_name' => $data['tracking_name'],
            'date'          => $data['date'],
            'total_click'   => $data['click'],
            'unique_click'  => $data['unique_click'],
        ];
    }
}