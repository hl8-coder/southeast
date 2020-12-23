<?php


namespace App\Http\Requests\Backstage;


use App\Http\Requests\Request;
use App\Models\CrmDailyReport;
use App\Models\CrmWeeklyReport;

class CrmReportsRequest extends Request
{
    public function rules()
    {
        switch ($this->getRequestMethod()) {
            case 'weeklyReport':
                return [
                    'week'       => 'nullable|integer',
                    'admin_name' => 'nullable|exists:admins,name',
                    'type'       => 'nullable|in:' . get_validate_in_string(CrmWeeklyReport::$type),
                ];
                break;
            case 'dailyReport':
                return [
                    'week'       => 'nullable|integer',
                    'date'       => 'nullable|date',
                    'admin_name' => 'nullable|exists:admins,name',
                    'type'       => 'nullable|in:' . get_validate_in_string(CrmDailyReport::$type),
                ];
                break;
            default:
                return [];
        }
    }
}
