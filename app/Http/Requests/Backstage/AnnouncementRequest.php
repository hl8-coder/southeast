<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\Announcement;
use App\Models\Currency;
use App\Models\Language;

class AnnouncementRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $methodName = $this->getRequestMethod();
        $languages  = Language::getAll()->pluck('code')->toArray();
        $currencies = Currency::getAll()->pluck('code')->toArray();
        switch ($methodName) {
            case 'store':
                return [
                    'name'                                  => 'required|string',
                    'currencies'                            => 'required|array',
                    'currencies.*'                          => 'required|in:' . implode(',', $currencies),
                    'content'                               => 'required|array',
                    'content.*.language'                    => 'required|string|in:' . implode(',', $languages),
                    'content.*.message'                     => 'required|string',
                    'content.*.title'                       => 'required|string',
                    'show_type'                             => 'required|in:' . get_validate_in_string(Announcement::$showTypes),
                    'payment_group_ids'                     => 'required_if:show_type,' . Announcement::SHOW_TYPE_PAYMENT . '|array',
                    'vip_ids'                               => 'required_if:show_type,' . Announcement::SHOW_TYPE_VIP . '|array',
                    'category'                              => 'required|in:' . get_validate_in_string(Announcement::$categories),
                    'start_at'                              => 'required|date_format:Y-m-d H:i:s',
                    'end_at'                                => 'required|date_format:Y-m-d H:i:s',
                    'is_agent'                              => 'nullable|boolean',
                    'is_game'                               => 'nullable|boolean',
                    'sort'                                  => 'nullable|integer|min:0',
                    'status'                                => 'nullable|boolean',
                    'pop_up'                                => 'nullable|boolean',
                    'content_type'                          => 'required|integer|in:'. get_validate_in_string(Announcement::$contentTypes),
                    'content.*.mobile_img_id'               => 'nullable|exists:images,id', // 内容为图片类型时必须.
                    'content.*.web_img_id'                  => 'nullable|exists:images,id', // 内容为图片类型时必须.
                    'pop_up_setting.frequency'              => 'required_if:pop_up,true,1|integer',  // 为弹窗时  弹窗频率必须  弹窗一次 多少小时内不会再弹.
                    'pop_up_setting.delay_sec'              => 'required_if:pop_up,true,1|integer',  // 为弹窗时  弹窗延迟必须  用户访问到具体页面时  多少秒才会再弹.
                    'pop_up_setting.mobile_redirect_url'    => 'required_if:content_type,'.Announcement::CONTENT_TYPE_IMAGE.'|string', // 内容为图片类型时必须.
                    'pop_up_setting.web_redirect_url'       => 'required_if:content_type,'.Announcement::CONTENT_TYPE_IMAGE.'|string', // 内容为图片类型时必须.
                    'access_pop_mobile_urls'                => 'nullable|array',  // 允许弹窗的地址.
                    'access_pop_pc_urls'                    => 'nullable|array',  // 允许弹窗的地址.
                ];
                break;

            case 'update':
                return [
                    'name'                                  => 'nullable|string',
                    'currencies'                            => 'nullable|array',
                    'currencies.*'                          => 'required|in:' . implode(',', $currencies),
                    'content'                               => 'nullable|array',
                    'content.*.language'                    => 'required|string|in:' . implode(',', $languages),
                    'content.*.message'                     => 'required|string',
                    'content.*.title'                       => 'required|string',
                    'show_type'                             => 'nullable|in:' . get_validate_in_string(Announcement::$showTypes),
                    'payment_group_ids'                     => 'required_if:show_type,' . Announcement::SHOW_TYPE_PAYMENT . '|array',
                    'vip_ids'                               => 'required_if:show_type,' . Announcement::SHOW_TYPE_VIP . '|array',
                    'category'                              => 'nullable|in:' . get_validate_in_string(Announcement::$categories),
                    'start_at'                              => 'nullable|date_format:Y-m-d H:i:s',
                    'end_at'                                => 'nullable|date_format:Y-m-d H:i:s',
                    'sort'                                  => 'nullable|integer|min:0',
                    'status'                                => 'nullable|boolean',
                    'pop_up'                                => 'nullable|boolean',
                    'is_agent'                              => 'nullable|boolean',
                    'is_game'                               => 'nullable|boolean',
                    'content_type'                          => 'nullable|integer|in:'. get_validate_in_string(Announcement::$contentTypes),
                    'content.*.mobile_img_id'               => 'nullable|exists:images,id', // 内容为图片类型时必须.
                    'content.*.web_img_id'                  => 'nullable|exists:images,id', // 内容为图片类型时必须.
                    'pop_up_setting.frequency'              => 'nullable|integer',  // 为弹窗时  弹窗频率必须  弹窗一次 多少小时内不会再弹.
                    'pop_up_setting.delay_sec'              => 'nullable|integer',  // 为弹窗时  弹窗延迟必须  用户访问到具体页面时  多少秒才会再弹.
                    'pop_up_setting.mobile_redirect_url'    => 'nullable|string', // 手机端跳转地址.
                    'pop_up_setting.web_redirect_url'       => 'nullable|string', // 电脑端跳转地址.
                    'access_pop_mobile_urls'                => 'nullable|array',  // 允许弹窗的地址.
                    'access_pop_pc_urls'                    => 'nullable|array',  // 允许弹窗的地址.
                ];
                break;
        }
    }
}
