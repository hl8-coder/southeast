<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Requests\Backstage\MailboxTemplateRequest;
use App\Models\MailboxTemplate;
use App\Transformers\MailboxTemplateTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Backstage\MailboxTemplatesController as Controller;
use Spatie\QueryBuilder\QueryBuilder;

class AffiliateMailboxTemplatesController extends Controller
{
    /**
     * @OA\Get(
     *     path="/backstage/affiliate/mailbox_templates",
     *     operationId="backstage.affiliate.mailbox_templates.index",
     *     tags={"Backstage-邮件模板"},
     *     summary="affiliate 邮件模板",
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
            ->where('is_affiliate', true)
            ->paginate($request->per_page);

        return $this->response->paginator($mailboxTemplates, new MailboxTemplateTransformer());
    }

    /**
     * @OA\Post(
     *     path="/backstage/affiliate/mailbox_templates",
     *     operationId="backstage.affiliate.mailbox_templates.store",
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
        $request['is_affiliate'] = true;
        return parent::store($request);
    }
}
