<?php


namespace App\Http\Requests\Backstage;


use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class KpiReportRequest extends Request
{
    public function rules()
    {
        switch ($this->method()) {
            case 'index':
                return [
                    'start_date' => 'nullable|date',
                    'end_date'   => 'nullable|date',
                ];
                break;
            case 'excelReport':
                return [
                    'start_date' => 'nullable|date',
                    'end_date'   => 'nullable|date',
                ];
                break;
            default:
                return [];
                break;
        }
    }
}
