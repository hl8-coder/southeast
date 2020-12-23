<?php

namespace App\Transformers;

use App\Models\Announcement;
use App\Models\Model;

/**
 * @OA\Schema(
 *   schema="Announcement",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="公告id"),
 *   @OA\Property(property="name", type="string", description="标题"),
 *   @OA\Property(property="is_agent", type="string", description="是否为代理公告"),
 *   @OA\Property(property="display_is_agent", type="string", description="显示是否为代理公告"),
 *   @OA\Property(property="currencies", type="array", description="币别",@OA\Items()),
 *   @OA\Property(property="currency", type="string", description="币别显示"),
 *   @OA\Property(property="content", type="array", @OA\Items(
 *       @OA\Property(property="language", type="string", description="语言"),
 *       @OA\Property(property="message", type="string", description="内容"),
 *       @OA\Property(property="title", type="string", description="标题"),
 *   ),description="多语言内容"),
 *   @OA\Property(property="pop_up_setting", type="array", @OA\Items(
 *       @OA\Property(property="mobile_redirect_url", type="string", description="当content_type=2时 必须 mobile弹窗点击跳转地址"),
 *       @OA\Property(property="web_redirect_url", type="string", description="当content_type=2时 必须 web端弹窗点击跳转地址"),
 *       @OA\Property(property="frequency", type="integer", description="当pop_up=1时 必须 弹窗频率 单位:分钟 比如 60 代表 60分钟弹窗一次后不会再弹"),
 *       @OA\Property(property="delay_sec", type="integer", description="当pop_up=1时 必须 弹窗延时 单位:秒 当用户在具体的弹窗页面停留多少秒 才会执行弹窗"),
 *   ),description="弹窗设置"),
 *   @OA\Property(property="access_pop_mobile_urls", type="array", description="手机端允许弹窗的前端页面",@OA\Items()),
 *   @OA\Property(property="access_pop_pc_urls", type="array", description="pc端允许弹窗的前端页面",@OA\Items()),

 *   @OA\Property(property="show_type", type="integer", description="公告会员组别类型(payment/vip)"),
 *   @OA\Property(property="display_show_type", type="integer", description="公告会员组别类型(payment/vip)显示"),
 *   @OA\Property(property="payment_group_ids", type="array", @OA\Items(), description="会员支付组别id数组"),
 *   @OA\Property(property="vip_ids", type="array", @OA\Items(), description="vip id数组"),
 *   @OA\Property(property="category", type="integer", description="分类"),
 *   @OA\Property(property="display_category", type="integer", description="分类显示"),
 *   @OA\Property(property="start_at", type="string", description="显示开始时间", format="date-time"),
 *   @OA\Property(property="end_at", type="string", description="显示结束时间", format="date-time"),
 *   @OA\Property(property="admin_name", type="string", description="管理员名称[backstage]"),
 *   @OA\Property(property="sort", type="string", description="排序"),
 *   @OA\Property(property="status", type="boolean", description="状态"),
 *   @OA\Property(property="display_status", type="string", description="状态显示"),
 *   @OA\Property(property="pop_up", type="boolean", description="公告置顶时是否允许弹窗"),
 *   @OA\Property(property="is_login_pop_up", type="boolean", description="是否是刚登录成功时才弹窗"),
 *   @OA\Property(property="is_game", type="boolean", description="是否按照游戏跳转"),
 *   @OA\Property(property="display_pop_up", type="string", description="公告置顶时是否允许弹窗"),
 *   @OA\Property(property="web_ima_path", type="string", description="pc端弹窗图片地址"),
 *   @OA\Property(property="mobile_img_path", type="string", description="手机端弹窗图片地址"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 * )
 */
class AnnouncementTransformer extends Transformer
{
    public function transform(Announcement $announcement)
    {
        $data = [
            'id'                        => $announcement->id,
            'name'                      => $announcement->name,
            'is_agent'                  => $announcement->is_agent,
            'display_is_agent'          => transfer_show_value($announcement->is_agent, Announcement::$booleanDropList),
            'currencies'                => $announcement->currencies,
            'currency'                  => implode(',', $announcement->currencies),
            'content_type'              => $announcement->content_type,
            'content'                   => $announcement->content,
            'pop_up_setting'            => $announcement->pop_up_setting,
            'access_pop_mobile_urls'    => $announcement->access_pop_mobile_urls,
            'access_pop_pc_urls'        => $announcement->access_pop_pc_urls,
            'is_login_pop_up'           => $announcement->is_login_pop_up,
            'is_game'                   => $announcement->is_game,
            'show_type'                 => $announcement->show_type,
            'display_show_type'         => transfer_show_value($announcement->show_type, Announcement::$showTypes),
            'payment_group_ids'         => $announcement->payment_group_ids,
            'vip_ids'                   => $announcement->vip_ids,
            'category'                  => $announcement->category,
            'display_category'          => transfer_show_value($announcement->category, Announcement::$categories),
            'start_at'                  => convert_time($announcement->start_at),
            'end_at'                    => convert_time($announcement->end_at),
            'admin_name'                => $announcement->admin_name,
            'sort'                      => $announcement->sort,
            'status'                    => $announcement->status,
            'display_status'            => transfer_show_value($announcement->status, Model::$booleanStatusesDropList),
            'pop_up'                    => $announcement->pop_up,
            'display_pop_up'            => transfer_show_value($announcement->pop_up, Model::$booleanDropList),
            'created_at'                => convert_time($announcement->created_at),
        ];

        switch ($this->type) {
            default:
                return $data;
            case 'front_index':
                unset($data['currencies']);
                $languageSet                = $announcement->getLanguageSet(app()->getLocale());
                $data                       = collect($data)->except(['admin_name', 'show_type', 'display_show_type', 'vip_ids', 'payment_group_ids'])->toArray();
                $data['content']            = $languageSet['message'];
                $data['title']              = isset($languageSet['title']) ? $languageSet['title'] : $data['name'];
                $data['web_img_path']       = isset($languageSet['web_img_path']) ? get_image_url($languageSet['web_img_path']): "";
                $data['mobile_img_path']    = isset($languageSet['mobile_img_path']) ? get_image_url($languageSet['mobile_img_path']): "";
                $data['content_type']       =  transfer_show_value($announcement->content_type, Announcement::$contentTypes);
                return $data;
                break;
        }
    }
}
