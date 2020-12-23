<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Dingo\Api\Routing\Helpers;

class BackstageController extends Controller
{
    use Helpers;


    /**
     * 根据上传图片ID获取图片地址，即目标字段需要保存的内容
     * @param int $id
     * @return string
     */
    public function getImagePathByImageId(int $id):string
    {
        return Image::find($id)->path;
    }

    public function getImageInfoById(int $id)
    {
        return Image::find($id);
    }
}
