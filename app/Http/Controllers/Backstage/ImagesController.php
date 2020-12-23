<?php

namespace App\Http\Controllers\Backstage;

use App\Handlers\ImageUploadHandler;
use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\ImageRequest;
use App\Repositories\ImageRepository;
use App\Transformers\ImageTransformer;

class ImagesController extends BackstageController
{
    /**
     * @OA\Post(
     *      path="/backstage/images",
     *      operationId="backstage.images.store",
     *      tags={"Backstage-图片"},
     *      summary="上传图片资源",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="image", description="图片", type="file", format="file"),
     *                  required={"image"},
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="上传成功",
     *          @OA\JsonContent(ref="#/components/schemas/Image"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function store(ImageRequest $request, ImageUploadHandler $uploader)
    {
        $user = $this->user;

        $result = $uploader->save($request->image, $user->id);

        $image = ImageRepository::create($user, $result['path'], $request->image->getClientOriginalName());

        return $this->response->item($image, new ImageTransformer())->setStatusCode(201);
    }
}
