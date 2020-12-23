<?php


namespace App\Transformers;


use App\Models\CrmDailyReport;

class CrmDailyReportTransformer extends Transformer
{
    /**
     * @OA\Schema(
     *   schema="CrmDailyReport",
     *   type="object",
     *   @OA\Property(property="id", type="integer", description="ID"),
     *   @OA\Property(property="week", type="integer", description="第几周"),
     *   @OA\Property(property="date", type="string", description="日期"),
     *   @OA\Property(property="type", type="integer", description="统计的订单类型"),
     *   @OA\Property(property="total_orders", type="integer", description="日订单总量"),
     *   @OA\Property(property="total_calls", type="integer", description="日呼叫总量"),
     *   @OA\Property(property="total_type_orders", type="integer", description="日类型订单总量"),
     *   @OA\Property(property="total_type_calls", type="integer", description="日类型订单总量"),
     *   @OA\Property(property="personal_total_orders", type="integer", description="个人日订单总量"),
     *   @OA\Property(property="personal_total_calls", type="integer", description="个人日呼叫总量"),
     *   @OA\Property(property="personal_total_type_orders", type="integer", description="个人日订单类型总量"),
     *   @OA\Property(property="personal_total_type_calls", type="integer", description="个人日订单类型呼叫总量"),
     *   @OA\Property(property="successful", type="integer", description="成功"),
     *   @OA\Property(property="fail", type="integer", description="失败"),
     *   @OA\Property(property="success", type="integer", description="营销成功"),
     *   @OA\Property(property="voice_mail", type="integer", description="语音信箱"),
     *   @OA\Property(property="hand_up", type="integer", description="挂断"),
     *   @OA\Property(property="no_pick_up", type="integer", description="未接听"),
     *   @OA\Property(property="invalid_number", type="integer", description="无效号码"),
     *   @OA\Property(property="not_own_number", type="integer", description="非号机主"),
     *   @OA\Property(property="call_back", type="integer", description="回拨"),
     *   @OA\Property(property="not_answer", type="integer", description="无应答"),
     *   @OA\Property(property="not_interested_in", type="integer", description="没兴趣"),
     *   @OA\Property(property="other", type="integer", description="其他"),
     *   @OA\Property(property="admin_id", type="string", description="电销人员对应管理员ID"),
     *   @OA\Property(property="admin_name", type="string", description="电销人员对应管理员"),
     * )
     */
    public function transform(CrmDailyReport $report)
    {
        # todo report system need to rebuild
        // 第几周与日期区间，周总通话次数，类型通话周总数【welcome】，个人周通话总数，成功通话次数和其他通话状态统计数 通话后首充笔数，通话后首充金额，成功通话与充值比，订单类型，手动调额总额【四大分类总数，需要写入到数据库
        return [
            'id'                       => $report->id,
            'week'                     => $report->week,
            'date'                     => $report->date,
            'type'                     => transfer_show_value($report->type, CrmDailyReport::$type),
            'total_orders'             => $report->total_orders,
            'total_calls'              => $report->successful + $report->fail, // $report->total_calls,
            'total_type_orders'        => $report->total_type_orders,
            'total_type_calls'         => $report->total_type_calls,
            'person_total_orders'      => $report->person_total_orders,
            'person_total_calls'       => $report->person_total_calls,
            'person_total_type_orders' => $report->person_total_type_orders,
            'person_total_type_calls'  => $report->person_total_type_calls,
            'successful'               => $report->successful,
            'fail'                     => $report->fail,
            'type_successful'          => $report->success + $report->not_own_number + $report->call_back + $report->not_interested_in,
            'type_fail'                => $report->voice_mail + $report->hand_up + $report->no_pick_up
                                        + $report->invalid_number + $report->not_answer + $report->other,
            'voice_mail'               => $report->voice_mail,
            'success'                  => $report->success,
            'hand_up'                  => $report->hand_up,
            'no_pick_up'               => $report->no_pick_up,
            'invalid_number'           => $report->invalid_number,
            'not_own_number'           => $report->not_own_number,
            'call_back'                => $report->call_back,
            'not_answer'               => $report->not_answer,
            'not_interested_in'        => $report->not_interested_in,
            'other'                    => $report->other,
            'admin_id'                 => $report->admin_id,
            'admin_name'               => $report->admin_name,
        ];
    }
}
