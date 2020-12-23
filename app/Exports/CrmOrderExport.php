<?php

namespace App\Exports;

use App\Models\CrmCallLog;
use App\Models\CrmOrder;
use App\Models\PaymentGroup;
use App\Models\RiskGroup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @OA\Schema(
 *   schema="CrmOrderReport",
 *   type="object",
 *   @OA\Property(property="call_at", type="string", description="call 时间", format="date-time"),
 *   @OA\Property(property="member_code", type="integer", description="会员 ID"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="status", type="integer", description="会员状态"),
 *   @OA\Property(property="source", type="integer", description="客户来源"),
 *   @OA\Property(property="call_status", type="integer", description="联络状态"),
 *   @OA\Property(property="type", type="integer", description="纪录类型"),
 *   @OA\Property(property="reason", type="string", description="会员原因"),
 *   @OA\Property(property="prefer_product", type="integer", description="产品活动优惠"),
 *   @OA\Property(property="prefer_bank", type="integer", description="偏好银行"),
 *   @OA\Property(property="remark", type="string", description="备注"),
 *   @OA\Property(property="agent_name", type="string", description="代理名称"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 * )
 */
class CrmOrderExport implements FromCollection, WithMapping, ShouldAutoSize, WithEvents
{
    use Exportable;

    private $date;
    private $type;

    public function __construct($request)
    {
        $this->type = $request->input('filter.type');
        $this->date = $request->all();
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        $collection = QueryBuilder::for(CrmOrder::class)
            ->allowedFilters(
                Filter::exact('type'),
                Filter::scope('name'),
                Filter::scope('full_name'),
                Filter::scope('email'),
                Filter::scope('phone'),
                Filter::scope('currency'),
                Filter::scope('user_status'),
                Filter::scope('risk_group_id'),
                Filter::scope('payment_group_id'),
                Filter::scope('affiliated_code'),
                Filter::scope('register_start'),
                Filter::scope('register_end'),
                Filter::scope('last_login_start'),
                Filter::scope('last_login_end'),
                Filter::scope('last_deposit_start'),
                Filter::scope('last_deposit_end'),
                Filter::scope('tag_start'),
                Filter::scope('tag_end'),
                Filter::scope('last_save_start'),
                Filter::scope('last_save_end'),
                Filter::scope('register_ip'),
                Filter::scope('deposit'),
                Filter::exact('status'),
                Filter::exact('call_status'),
                Filter::exact('admin_name'),
                Filter::exact('tag_admin_name')
            )
            ->with(['user', 'userInfo'])
            ->defaultSort('created_at')
            ->limit(7000)->get(); // 本地测试极限是 7000 行，7100 行不行，上线后带测试，需要调整，线上服务器可能不支持这么多数据导出
        return $collection;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        $headerList = $this->getHeaderList($this->type);
        $header     = [
            AfterSheet::class => function (AfterSheet $event) use ($headerList) {
                $event->sheet->getDelegate()->insertNewRowBefore(1);
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(1, 1, 'ID');
                $i = 2;
                foreach ($headerList as $headerName) {
                    $event->sheet->getDelegate()->setCellValueByColumnAndRow($i, 1, $headerName);
                    $i++;
                }
            },
        ];
        return $header;
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($crmOrder): array
    {
        switch ($this->type) {
            case CrmOrder::TYPE_WELCOME:
                return $this->dataWelcome($crmOrder);
                break;
            case CrmOrder::TYPE_NON_DEPOSIT:
                return $this->dataNonDeposit($crmOrder);
                break;
            case CrmOrder::TYPE_DAILY_RETENTION:
                return $this->dataDailyRetention($crmOrder);
                break;
            case CrmOrder::TYPE_RETENTION:
                return $this->dataRetention($crmOrder);
                break;
        }
    }

    protected function getHeaderList($type = CrmOrder::TYPE_WELCOME)
    {
        $header = [
            CrmOrder::TYPE_WELCOME         => ['Member Code', 'Name', 'Currency', 'Status', 'Register IP', 'Deposit', 'Phone', 'Email', 'Risk', 'Payment', 'AFF', 'Sign In', 'Login', 'FTD Amt', 'FTD Time', 'Call Status', 'Tag User', 'Tag Time', 'BO User', 'Last Save', 'Last Save Time',],
            CrmOrder::TYPE_NON_DEPOSIT     => ['Member Code', 'Name', 'Currency', 'Status', 'Register IP', 'Phone', 'Email', 'Risk', 'Payment', 'AFF', 'Sign In', 'Login', 'Call Status', 'Tag User', 'Tag Time', 'BO User', 'Last Save', 'Last Save Time',],
            CrmOrder::TYPE_DAILY_RETENTION => ['Member Code', 'Name', 'Currency', 'Status', 'Register IP', 'Phone', 'Email', 'Risk', 'Payment', 'AFF', 'Sign In', 'Login', 'LTD Amt', 'LTD Time', 'Call Status', 'Tag User', 'Tag Time', 'BO User', 'Last Save', 'Last Save Time',],
            CrmOrder::TYPE_RETENTION       => ['Member Code', 'Name', 'Currency', 'Status', 'Register IP', 'Phone', 'Email', 'Risk', 'Payment', 'AFF', 'Sign In', 'Login', 'LTD Amt', 'LTD Time', 'Call Status', 'Tag User', 'Tag Time', 'BO User', 'Last Save', 'Last Save Time',],
        ];
        return $header[$type];
    }

    protected function dataWelcome($crmOrder)
    {
        $firstDeposit = $crmOrder->user->depositsSuccessFirst->first();
        return [
            'id'              => $crmOrder->id,
            'user_name'       => $crmOrder->user->name,
            'full_name'       => $crmOrder->userInfo->full_name,
            'currency'        => $crmOrder->user->currency,
            'user_status'     => User::$statuses[$crmOrder->user->status],
            'register_ip'     => $crmOrder->userInfo->register_ip,
            'deposit'         => CrmOrder::$booleanDropList[!is_null($crmOrder->user->first_deposit_at)],
            'phone'           => $crmOrder->userInfo->phone,
            'email'           => $crmOrder->userInfo->email,
            'risk_group'      => transfer_show_value($crmOrder->user->risk_group_id, RiskGroup::getDropList()),
            'payment_group'   => transfer_show_value($crmOrder->user->payment_group_id, PaymentGroup::getDropList()),
            'affiliated_code' => $crmOrder->affiliated_code,
            'sign_in'         => $crmOrder->user->created_at,
            'last_login_in'   => $crmOrder->userInfo->last_login_at,
            'ftd_amt'         => empty($firstDeposit) ? '' : thousands_number($firstDeposit->amount),
            'ftd_time'        => empty($firstDeposit) ? '' : $firstDeposit->deposit_at,
            'call_status'     => transfer_show_value($crmOrder->call_status, CrmOrder::$call_statuses), // isset(CrmOrder::$call_statuses[$crmOrder->call_status]) ? CrmOrder::$call_statuses[$crmOrder->call_status] : "-"
            'tag_admin_name'  => $crmOrder->tag_admin_name,
            'tag_at'          => convert_time($crmOrder->tag_at) ? '' : $crmOrder->tag_at,
            'admin_name'      => $crmOrder->admin_name,
            'last_save_by'    => $crmOrder->last_save_case_admin_name,
            'last_save_at'    => convert_time($crmOrder->last_save_case_at) ? '' : $crmOrder->last_save_case_at,
        ];
    }

    protected function dataNonDeposit($crmOrder)
    {
        return [
            'id'              => $crmOrder->id,
            'user_name'       => $crmOrder->user->name,
            'full_name'       => $crmOrder->userInfo->full_name,
            'currency'        => $crmOrder->user->currency,
            'user_status'     => User::$statuses[$crmOrder->user->status],
            'register_ip'     => $crmOrder->userInfo->register_ip,
            'phone'           => $crmOrder->userInfo->phone,
            'email'           => $crmOrder->userInfo->email,
            'risk_group'      => transfer_show_value($crmOrder->user->risk_group_id, RiskGroup::getDropList()),
            'payment_group'   => transfer_show_value($crmOrder->user->payment_group_id, PaymentGroup::getDropList()),
            'affiliated_code' => $crmOrder->affiliated_code,
            'sign_in'         => $crmOrder->user->created_at,
            'last_login_in'   => $crmOrder->userInfo->last_login_at,
            'call_status'     => transfer_show_value($crmOrder->call_status, CrmOrder::$call_statuses), // isset(CrmOrder::$call_statuses[$crmOrder->call_status]) ? CrmOrder::$call_statuses[$crmOrder->call_status] : "-"
            'tag_admin_name'  => $crmOrder->tag_admin_name,
            'tag_at'          => convert_time($crmOrder->tag_at) ? '' : $crmOrder->tag_at,
            'admin_name'      => $crmOrder->admin_name,
            'last_save_by'    => $crmOrder->last_save_case_admin_name,
            'last_save_at'    => convert_time($crmOrder->last_save_case_at) ? '' : $crmOrder->last_save_case_at,
        ];
    }

    protected function dataDailyRetention($crmOrder)
    {
        $lastDeposit = $crmOrder->user->depositsSuccessLatest->first();
        return [
            'id'              => $crmOrder->id,
            'user_name'       => $crmOrder->user->name,
            'full_name'       => $crmOrder->userInfo->full_name,
            'currency'        => $crmOrder->user->currency,
            'user_status'     => User::$statuses[$crmOrder->user->status],
            'register_ip'     => $crmOrder->userInfo->register_ip,
            'phone'           => $crmOrder->userInfo->phone,
            'email'           => $crmOrder->userInfo->email,
            'risk_group'      => transfer_show_value($crmOrder->user->risk_group_id, RiskGroup::getDropList()),
            'payment_group'   => transfer_show_value($crmOrder->user->payment_group_id, PaymentGroup::getDropList()),
            'affiliated_code' => $crmOrder->affiliated_code,
            'sign_in'         => $crmOrder->user->created_at,
            'last_login_in'   => $crmOrder->userInfo->last_login_at,
            'ltd_amt'         => empty($lastDeposit) ? '' : thousands_number($lastDeposit->amount),
            'ltd_time'        => empty($lastDeposit) ? '' : $lastDeposit->deposit_at,
            'call_status'     => transfer_show_value($crmOrder->call_status, CrmOrder::$call_statuses), // isset(CrmOrder::$call_statuses[$crmOrder->call_status]) ? CrmOrder::$call_statuses[$crmOrder->call_status] : "-"
            'tag_admin_name'  => $crmOrder->tag_admin_name,
            'tag_at'          => convert_time($crmOrder->tag_at) ? '' : $crmOrder->tag_at,
            'admin_name'      => $crmOrder->admin_name,
            'last_save_by'    => $crmOrder->last_save_case_admin_name,
            'last_save_at'    => convert_time($crmOrder->last_save_case_at) ? '' : $crmOrder->last_save_case_at,
        ];
    }

    protected function dataRetention($crmOrder)
    {
        $lastDeposit = $crmOrder->user->depositsSuccessLatest->first();
        return [
            'id'              => $crmOrder->id,
            'user_name'       => $crmOrder->user->name,
            'full_name'       => $crmOrder->userInfo->full_name,
            'currency'        => $crmOrder->user->currency,
            'user_status'     => User::$statuses[$crmOrder->user->status],
            'register_ip'     => $crmOrder->userInfo->register_ip,
            'phone'           => $crmOrder->userInfo->phone,
            'email'           => $crmOrder->userInfo->email,
            'risk_group'      => transfer_show_value($crmOrder->user->risk_group_id, RiskGroup::getDropList()),
            'payment_group'   => transfer_show_value($crmOrder->user->payment_group_id, PaymentGroup::getDropList()),
            'affiliated_code' => $crmOrder->affiliated_code,
            'sign_in'         => $crmOrder->user->created_at,
            'last_login_in'   => $crmOrder->userInfo->last_login_at,
            'ltd_amt'         => empty($lastDeposit) ? '' : thousands_number($lastDeposit->amount),
            'ltd_time'        => empty($lastDeposit) ? '' : $lastDeposit->deposit_at,
            'call_status'     => transfer_show_value($crmOrder->call_status, CrmOrder::$call_statuses), // isset(CrmOrder::$call_statuses[$crmOrder->call_status]) ? CrmOrder::$call_statuses[$crmOrder->call_status] : "-"
            'tag_admin_name'  => $crmOrder->tag_admin_name,
            'tag_at'          => convert_time($crmOrder->tag_at) ? '' : $crmOrder->tag_at,
            'admin_name'      => $crmOrder->admin_name,
            'last_save_by'    => $crmOrder->last_save_case_admin_name,
            'last_save_at'    => convert_time($crmOrder->last_save_case_at) ? '' : $crmOrder->last_save_case_at,
        ];
    }

}
