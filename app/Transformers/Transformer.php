<?php
namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class Transformer extends TransformerAbstract
{
    protected $type;
    protected $data;

    public function __construct($type='', $data=[])
    {
        $this->type = $type;
        $this->data = $data;
    }
}