<?php

namespace App\Http\Controllers\Api;

use App\Handlers\ImageUploadHandler;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\ImageRequest;
use App\Repositories\ImageRepository;
use App\Transformers\ImageTransformer;
use Illuminate\Http\Request;

class ImagesController extends ApiController
{
    /**
     * @OA\Post(
     *      path="/images",
     *      operationId="api.images.store",
     *      tags={"Api-平台"},
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
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Image"),
     *       ),
     *       @OA\Response(response=400, description="Bad request"),
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
