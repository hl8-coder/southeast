<?php

namespace App\Transformers;

use App\Models\AffiliateAnnouncement;
use App\Models\Model;

/**
 * @OA\Schema(
 *   schema="AffiliateAnnouncement",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="公告id"),
 *   @OA\Property(property="name", type="string", description="标题"),
 *   @OA\Property(property="currencies", type="array", @OA\Items(), description="币别"),
 *   @OA\Property(property="content", type="array", @OA\Items(
 *         @OA\Property(property="language", type="string", description="语言"),
 *         @OA\Property(property="message", type="string", description="内容"),
 *      ),description="多语言内容"),
 *   @OA\Property(property="display_type", type="integer", description="公告会员组别类型(payment/vip)[backstage]"),
 *   @OA\Property(property="display_ids", type="integer", description="会员组别id数组[backstage]"),
 *   @OA\Property(property="category", type="integer", description="分类"),
 *   @OA\Property(property="display_category", type="string", description="分类"),
 *   @OA\Property(property="start_at", type="string", description="显示开始时间", format="date-time"),
 *   @OA\Property(property="end_at", type="string", description="显示结束时间", format="date-time"),
 *   @OA\Property(property="admin_name", type="string", description="管理员名称[backstage]"),
 *   @OA\Property(property="sort", type="string", description="排序"),
 *   @OA\Property(property="status", type="boolean", description="状态"),
 *   @OA\Property(property="pop_up", type="boolean", description="弹窗"),
 *   @OA\Property(property="display_pop_up", type="boolean", description="弹窗"),
 *   @OA\Property(property="display_status", type="boolean", description="状态"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 * )
 */
class AffiliateAnnouncementTransformer extends Transformer
{
    public function transform(AffiliateAnnouncement $announcement)
    {
        $data = [
            'id'               => $announcement->id,
            'name'             => $announcement->name,
            'currencies'       => $announcement->currencies,
            'content'          => $announcement->content,
            'category'         => $announcement->category,
            'display_category' => transfer_show_value($announcement->category, AffiliateAnnouncement::$categories),
            'start_at'         => convert_time($announcement->start_at),
            'end_at'           => convert_time($announcement->end_at),
            'admin_name'       => $announcement->admin_name,
            'sort'             => $announcement->sort,
            'status'           => $announcement->status,
            'pop_up'           => $announcement->pop_up,
            'display_pop_up'   => transfer_show_value($announcement->pop_up, Model::$booleanDropList),
            'display_status'   => transfer_show_value($announcement->status, Model::$booleanStatusesDropList),
            'created_at'       => convert_time($announcement->created_at),
        ];

        switch ($this->type) {
            default:
                return $data;
            case 'font_index':
                $languageSet     = $announcement->getLanguageSet();
                $data            = collect($data)->except(['admin_name', 'display_type', 'display_ids'])->toArray();
                $data['content'] = $languageSet['message'];
                $data['title']   = isset($languageSet['title']) ? $languageSet['title'] : $data['name'];
                return $data;
                break;
        }

        return $this->filter($data);
    }
}
