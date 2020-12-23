<?php
namespace App\Repositories;

use App\Models\Image;

class ImageRepository
{
    /**
     * 创建图片
     *
     * @param   $userModel
     * @param   string      $name       文件原始文件名
     * @param   string      $path       文件存储地址
     * @param   null $model
     * @return Image
     */
    public static function create($userModel, $path, $name='', $model=null)
    {
        if (empty($path)){
            error_response(422, 'Image can not be empty!');
        }
        $image = new Image();
        $image->user_type   = get_class($userModel);
        $image->user_id     = $userModel->id;
        $image->name        = $name;
        $image->path        = $path;

        if ($model) {
            $image->imageable_type  = get_class($model);
            $image->imageable_id    = $model->id;
        }

        $image->save();

        return $image;
    }

    public static function updatePatch($userModel, $ids, $model)
    {
        foreach ($ids as $id) {
             $image = Image::where("user_type", get_class($userModel))
                    ->where("user_id", $userModel->id)
                    ->where("id", $id)
                    ->where("imageable_id", null)->first();

            if($image)
            {
                $image->imageable_type = get_class($model);
                $image->imageable_id = $model->id;
                $image->save();
            }
        }
    }
}
