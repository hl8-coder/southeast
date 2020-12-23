<?php

namespace App\Http\Requests\Backstage;

use App\Models\CrmOrder;
use App\Http\Requests\Request;

class CrmOrderRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->getRequestMethod()) {
            case 'updateBatch':
                return [
                    'crm_order_ids'   => 'required|array',
                    'crm_order_ids.*' => 'required|exists:crm_orders,id',
                    'admin_id'        => 'integer|required_if:distribute,1',
                    'distribute'      => 'required|boolean',
                ];
                break;
            case 'index':
                return [
                    'type' => 'nullable|in:' . get_validate_in_string(CrmOrder::$type),
                ];
            case 'store':
                return [
                    'type'  => 'required|in:' . get_validate_in_string(CrmOrder::$type),
                    'excel' => 'required|file',
                ];
            default:
                return [];
        }
    }
}
