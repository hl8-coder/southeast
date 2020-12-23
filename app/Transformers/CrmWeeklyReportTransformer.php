<?php


namespace App\Transformers;


use App\Models\CrmWeeklyReport;
use Carbon\Carbon;

class CrmWeeklyReportTransformer extends Transformer
{
    /**
     * @OA\Schema(
     *   schema="CrmWeeklyReport",
     *   type="object",
     *   @OA\Property(property="id", type="integer", description="ID"),
     *   @OA\Property(property="week", type="integer", description="第几周"),
     *   @OA\Property(property="week_start_at", type="string", description="周起始日期"),
     *   @OA\Property(property="week_end_at", type="string", description="周结束日期"),
     *   @OA\Property(property="type", type="integer", description="统计的订单类型"),
     *   @OA\Property(property="total_orders", type="integer", description="周订单总量"),
     *   @OA\Property(property="total_calls", type="integer", description="周呼叫总量"),
     *   @OA\Property(property="total_type_orders", type="integer", description="周类型订单总量"),
     *   @OA\Property(property="total_type_calls", type="integer", description="周类型订单总量"),
     *   @OA\Property(property="personal_total_orders", type="integer", description="个人周订单总量"),
     *   @OA\Property(property="personal_total_calls", type="integer", description="个人周呼叫总量"),
     *   @OA\Property(property="personal_total_type_orders", type="integer", description="个人周订单类型总量"),
     *   @OA\Property(property="personal_total_type_calls", type="integer", description="个人周订单类型呼叫总量"),
     *   @OA\Property(property="successful", type="integer", description="成功"),
     *   @OA\Property(property="fail", type="integer", description="失败"),
     *   @OA\Property(property="voice_mail", type="integer", description="语音信箱"),
     *   @OA\Property(property="hand_up", type="integer", description="挂断"),
     *   @OA\Property(property="no_pick_up", type="integer", description="未接听"),
     *   @OA\Property(property="success", type="integer", description="营销成功"),
     *   @OA\Property(property="invalid_number", type="integer", description="无效号码"),
     *   @OA\Property(property="not_own_number", type="integer", description="非号机主"),
     *   @OA\Property(property="call_back", type="integer", description="回拨"),
     *   @OA\Property(property="not_answer", type="integer", description="无应答"),
     *   @OA\Property(property="not_interested_in", type="integer", description="没兴趣"),
     *   @OA\Property(property="other", type="integer", description="其他"),
     *   @OA\Property(property="register", type="integer", description="注册人数"),
     *   @OA\Property(property="ftd_member", type="integer", description="电销后首充人数"),
     *   @OA\Property(property="ftd_amount", type="string", description="电销后首充总金额"),
     *   @OA\Property(property="adjustment_amount", type="string", description="电销后调额总额"),
     *   @OA\Property(property="deposit_percent", type="string", description="充值人数百分比"),
     *   @OA\Property(property="admin_id", type="string", description="电销人员对应管理员ID"),
     *   @OA\Property(property="admin_name", type="string", description="电销人员对应管理员"),
     * )
     */
    public function transform(CrmWeeklyReport $crmWeeklyReport)
    {
        // 第几周与日期区间，周总通话次数，类型通话周总数【welcome】，个人周通话总数，成功通话次数和其他通话状态统计数 通话后首充笔数，通话后首充金额，成功通话与充值比，订单类型，手动调额总额【四大分类总数，需要写入到数据库
        return [
            'id'                       => $crmWeeklyReport->id,
            'week'                     => $crmWeeklyReport->week,
            'date'                     => $crmWeeklyReport->week_start_at . ' to ' . $crmWeeklyReport->week_end_at,
            'week_start_at'            => $crmWeeklyReport->week_start_at,
            'week_end_at'              => $crmWeeklyReport->week_end_at,
            'type'                     => CrmWeeklyReport::$type[$crmWeeklyReport->type],
            'total_orders'             => $crmWeeklyReport->total_orders, // for template $crmWeeklyReport->total_orders,
            'total_calls'              => $crmWeeklyReport->total_calls,
            'total_type_orders'        => $crmWeeklyReport->total_type_orders,
            'total_type_calls'         => $crmWeeklyReport->total_type_calls,
            'person_total_orders'      => $crmWeeklyReport->person_total_orders,
            'person_total_calls'       => $crmWeeklyReport->person_total_calls,
            'person_total_type_orders' => $crmWeeklyReport->person_total_type_orders,
            'person_total_type_calls'  => $crmWeeklyReport->person_total_type_calls,
            'successful'               => $crmWeeklyReport->successful,
            'fail'                     => $crmWeeklyReport->fail,
            'type_successful'          => $crmWeeklyReport->success + $crmWeeklyReport->not_own_number + $crmWeeklyReport->call_back + $crmWeeklyReport->not_interested_in,
            'type_fail'                => $crmWeeklyReport->voice_mail + $crmWeeklyReport->hand_up + $crmWeeklyReport->no_pick_up
                                        + $crmWeeklyReport->invalid_number + $crmWeeklyReport->not_answer  + $crmWeeklyReport->other,
            'voice_mail'               => $crmWeeklyReport->voice_mail,
            'success'                  => $crmWeeklyReport->success,
            'hand_up'                  => $crmWeeklyReport->hand_up,
            'no_pick_up'               => $crmWeeklyReport->no_pick_up,
            'invalid_number'           => $crmWeeklyReport->invalid_number,
            'not_own_number'           => $crmWeeklyReport->not_own_number,
            'call_back'                => $crmWeeklyReport->call_back,
            'not_answer'               => $crmWeeklyReport->not_answer,
            'not_interested_in'        => $crmWeeklyReport->not_interested_in,
            'other'                    => $crmWeeklyReport->other,
            'register'                 => $crmWeeklyReport->register,
            'ftd_member'               => $crmWeeklyReport->ftd_member,
            'ftd_amount'               => thousands_number($crmWeeklyReport->ftd_amount),
            'deposit_percent'          => $crmWeeklyReport->success > 0 ? format_number($crmWeeklyReport->ftd_member / $crmWeeklyReport->success, 2) . ' %' : 0,
            'adjustment_amount'        => thousands_number($crmWeeklyReport->adjustment_amount),
            'admin_id'                 => $crmWeeklyReport->admin_id,
            'admin_name'               => $crmWeeklyReport->admin_name,
        ];
    }
}
