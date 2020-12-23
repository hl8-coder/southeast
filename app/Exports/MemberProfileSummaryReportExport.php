<?php


namespace App\Exports;


use App\Models\GameBetDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * @OA\Schema(
 *   schema="MemberProfileSummaryReportExport",
 *   type="object",
 *   @OA\Property(property="Member Code", type="string", description="会员名称", format="date-time"),
 *   @OA\Property(property="Created Date", type="string", description="会员注册时间"),
 *   @OA\Property(property="First Time Deposit Date", type="string", description="首次存款时间"),
 *   @OA\Property(property="Sign Up IP/Site", type="string", description="注册IP/网址"),
 *   @OA\Property(property="Currency", type="integer", description="币别"),
 *   @OA\Property(property="Account Status", type="string", description="账户状态"),
 * )
 */
class MemberProfileSummaryReportExport implements WithMapping, ShouldAutoSize, FromCollection, WithHeadings
{
    use \Maatwebsite\Excel\Concerns\Exportable, SerializesModels;

    private   $request;
    private   $affiliate;
    protected $start;
    protected $end;

    public function __construct($request, $affiliate)
    {
        $this->request   = $request;
        $this->affiliate = $affiliate;
    }

    public function collection()
    {
        $month = $this->request->month;
        if ($month) {
            $start = Carbon::parse($month)->firstOfMonth()->toDateTimeString();
            $end   = Carbon::parse($month)->endOfMonth()->toDateTimeString();
        } else {
            $start = now()->firstOfMonth()->toDateTimeString();
            $end   = now()->endOfMonth()->toDateTimeString();
        }

        $this->start = $start;
        $this->end   = $end;

        $affiliate = $this->affiliate;

        $users = $affiliate->subUsers()
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->limit(20000)->get();

        return $users;
    }

    public function map($row): array
    {
        $start = $this->start;
        $end   = $this->end;

        $firstDepositDate = $row->deposits()
            ->where(function ($query) use ($start, $end) {
                if ($start) {
                    $query->where('created_at', '>=', $start);
                }
                if ($end) {
                    $query->where('created_at', '<=', $end);
                }
            })
            ->orderBy('created_at', 'asc')
            ->first();
        $time             = '';
        if ($firstDepositDate) {
            $time = convert_time($firstDepositDate->created_at);
        }

        return [
            'name'               => $row->name,
            'created_at'         => $row->created_at,
            'first_deposit_time' => $time,
            'sign_up'            => '[' . $row->register_ip .'] ' . $row->register_url,
            'currency'           => $row->currency,
            'account_status'     => transfer_show_value($row->status, User::$statuses),
        ];
    }

    public function headings(): array
    {
        return [
            __('affiliate.MEMBER_CODE'),
            __('affiliate.CREATED_DATE'),
            __('affiliate.FIRST_TIME_DEPOSIT_DATE'),
            __('affiliate.SIGN_UP_IP_OR_SITE'),
            __('affiliate.CURRENCY'),
            __('affiliate.ACCOUNT_STATUS'),
        ];
    }
}
