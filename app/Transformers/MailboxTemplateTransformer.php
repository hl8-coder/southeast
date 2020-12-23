<?php


namespace App\Transformers;


use App\Models\MailboxTemplate;

class MailboxTemplateTransformer extends Transformer
{
    /**
     * @OA\Schema(
     *   schema="MailboxTemplate",
     *   type="object",
     *   @OA\Property(property="id", type="integer", description="邮件模板ID"),
     *   @OA\Property(property="type", type="integer", description="邮件类型"),
     *   @OA\Property(property="display_type", type="string", description="类型显示"),
     *   @OA\Property(property="last_update_by", type="string", description="最后更新Admin"),
     *   @OA\Property(property="last_update_at", type="string", description="最后更新时间"),
     *   @OA\Property(property="languages", type="array", description="邮件多语言", @OA\Items(
     *      @OA\Property(property="language", type="string", description="语言"),
     *      @OA\Property(property="title", type="string", description="邮件标题"),
     *      @OA\Property(property="body", type="string", description="邮件内容"),
     *   )),
     *   @OA\Property(property="content", type="string", description="模版内容"),
     *   @OA\Property(property="language", type="string", description="模版语言"),
     * )
     */
    public function transform(MailboxTemplate $mailboxTemplate)
    {
        $data = [
            'id'                   => $mailboxTemplate->id,
            'type'                 => $mailboxTemplate->type,
            'currencies'           => $mailboxTemplate->currencies,
            'display_type'         => transfer_show_value($mailboxTemplate->type, MailboxTemplate::$types),
            'last_update_by'       => $mailboxTemplate->last_update_by,
            'is_affiliate'         => $mailboxTemplate->is_affiliate,
            'display_is_affiliate' => transfer_show_value($mailboxTemplate->is_affiliate, MailboxTemplate::$booleanDropList),
            'last_update_at'       => convert_time($mailboxTemplate->updated_at),
            'languages'            => $mailboxTemplate->languages,
            'created_at'           => convert_time($mailboxTemplate->created_at),
        ];

        if (!empty($this->type)) {
            $languageSet      = $mailboxTemplate->getLanguageSet($this->type);
            $data['content']  = $languageSet['body'];
            $data['language'] = $languageSet['language'];
        }

        return $data;
    }
}
