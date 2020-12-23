<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\GamePlatformPullReportSchedule;

class GamePlatformPullReportScheduleRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => 'required|in:' . get_validate_in_string(GamePlatformPullReportSchedule::$statuses),
        ];
    }
}
