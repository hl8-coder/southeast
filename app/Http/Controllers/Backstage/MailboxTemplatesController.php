<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\MailboxTemplateRequest;
use App\Models\Image;
use App\Models\MailboxTemplate;
use App\Transformers\MailboxTemplateTransformer;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class MailboxTemplatesController extends BackstageController
{
    /**
     * @OA\Get(
     *     path="/backstage/mailbox_templates",
     *     operationId="backstage.mailbox_templates.index",
     *     tags={"Backstage-邮件模板"},
     *     summary="邮件模板",
     *     description="邮件模板列表",
     *     @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/MailboxTemplate"),
     *          ),
     *      ),
     * )
     */
    public function index(Request $request)
    {
        $mailboxTemplates = QueryBuilder::for(MailboxTemplate::class)
            ->where('is_affiliate', false)
            ->paginate($request->per_page);

        return $this->response->paginator($mailboxTemplates, new MailboxTemplateTransformer());
    }

    /**
     * @OA\Post(
     *     path="/backstage/mailbox_templates",
     *     operationId="backstage.mailbox_templates.store",
     *     tags={"Backstage-邮件模板"},
     *     summary="添加邮件模板",
     *     description="添加邮件模板",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="type", type="integer", description="邮件类型"),
     *                  @OA\Property(property="langauges", type="array", description="多语言",
     *                      @OA\Items(
     *                          @OA\Property(property="language", type="string", description="语言"),
     *                          @OA\Property(property="title", type="string", description="标题"),
     *                          @OA\Property(property="body", type="string", description="内容"),
     *                      )
     *                  ),
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/MailboxTemplate"),
     *          ),
     *      ),
     * )
     */
    public function store(MailboxTemplateRequest $request)
    {
        $data = remove_null($request->all());

        $list = [];
        if (is_array($data['languages'])) {
            foreach ($data['languages'] as $language) {
                $list[] = $this->getImgPath($language);
            }
        }

        $data['languages'] = [];
        $data['languages'] = $list;

        $mailboxTemplate = MailboxTemplate::query()->create($data);

        return $this->response->item($mailboxTemplate->refresh(), new MailboxTemplateTransformer());
    }

    /**
     * @OA\Patch(
     *     path="/backstage/mailbox_templates/{mailboxTemplate}",
     *     operationId="backstage.mailbox_templates.update",
     *     tags={"Backstage-邮件模板"},
     *     summary="更新邮件模板",
     *     description="更新邮件模板",
     *     @OA\Parameter(
     *         name="mailboxTemplate",
     *         in="path",
     *         description="邮件模板id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="type", type="integer", description="邮件类型"),
     *                  @OA\Property(property="langauges", type="array", description="多语言",
     *                      @OA\Items(
     *                          @OA\Property(property="language", type="string", description="语言"),
     *                          @OA\Property(property="title", type="string", description="标题"),
     *                          @OA\Property(property="body", type="string", description="内容"),
     *                      )
     *                  ),
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/MailboxTemplate"),
     *          ),
     *     ),
     *     @OA\Response(response=401, description="授权不通过"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=422, description="验证错误"),
     *     security={
     *           {"bearer": {}}
     *     }
     * )
     */
    public function update(MailboxTemplateRequest $request, MailboxTemplate $mailboxTemplate)
    {
        $data = remove_null($request->except(['type', 'is_affiliate']));

        $data['last_update_by'] = $this->user->name;

        $list = [];
        if (is_array($data['languages'])) {
            foreach ($data['languages'] as $language) {
                $list[] = $this->getImgPath($language);
            }
        }

        $data['languages'] = [];
        $data['languages'] = $list;

        $mailboxTemplate->update($data);

        return $this->response->item($mailboxTemplate->refresh(), new MailboxTemplateTransformer());
    }

    /**
     * @OA\Delete(
     *      path="/backstage/mailbox_templates/{mailboxTemplate}",
     *      operationId="backstage.mailbox_templates.delete",
     *      tags={"Backstage-邮件模板"},
     *      summary="删除邮件模板",
     *      @OA\Parameter(
     *         name="mailboxTemplate",
     *         in="path",
     *         description="邮件模板id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(response=204,description="No Content"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function destroy(MailboxTemplate $mailboxTemplate)
    {
        $mailboxTemplate->delete();

        return $this->response->noContent();
    }

    protected function getImgPath($data)
    {
        $lan             = [];
        $lan['title']    = $data['title'];
        $lan['language'] = $data['language'];
        if (!empty($data['image_id'])) {
            $path = Image::find($data['image_id'])->path;
            strstr($path, 'http') == false ? config('app.url') . '/' . $path : $path;
            $path         = config('app.url') . '/' . $path;
            $image        = "<img src='{$path}' style='width:100%;'>";
            $data['body'] = $image . $data['body'];
        }
        $lan['body'] = $data['body'];

        return $lan;
    }
}
