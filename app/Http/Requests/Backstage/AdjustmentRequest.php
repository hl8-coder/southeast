<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\Adjustment;
use App\Models\GamePlatform;
use App\Models\Remark;
use App\Models\User;
use App\Rules\GtZeroRule;
use Illuminate\Validation\Rule;

class AdjustmentRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *R
     * @return array
     */
    public function rules()
    {
        $methodName = $this->getRequestMethod();
        switch ($methodName) {
            case 'store':
                return [
                    'type'                  => 'required|integer|in:' . implode(',', array_keys(Adjustment::$types)),
                    'amount'                => ['required', 'numeric', new GtZeroRule()],
                    'category'              => 'required|integer|in:' . implode(',', array_keys(Adjustment::$categories)),
                    'platform_code'         => 'nullable|exists:game_platforms,code',
                    'product_code'          => 'nullable|exists:game_platform_products,code',
                    'related_order_no'      => 'nullable|string',
                    'is_agent'              => 'nullable|boolean',
                    'turnover_closed_value' => 'nullable|numeric|min:0',
                    'reason'                => 'required|string|max:2048',
                    'remark'                => 'nullable|string|max:2048',
                ];
                break;

            case 'reject':
                return [
                    'remark' => 'required|string|max:2048',
                ];
                break;
            case 'index':
            case 'adjustmentExport':
                $isAgent = (boolean)$this->input('filter.is_agent', false);
                if ($isAgent) {
                    return [
                        'filter.user_name' => ['nullable', function ($attribute, $value, $fail) {
                            $exists = app(User::class)->where('is_agent', true)->where('name', $value)->exists();
                            if (!$exists) {
                                $fail('The affiliate dosen\'t exists!');
                            }
                        }],
                    ];
                } else {
                    return [
                        'filter.user_name' => ['nullable', function ($attribute, $value, $fail) {
                            $exists = app(User::class)->where('is_agent', false)->where('name', $value)->exists();
                            if (!$exists) {
                                $fail('The member dosen\'t exists!');
                            }
                        }],
                    ];
                }
                break;

            case 'close':
                return [
                    'remark' => 'nullable|string|max:1024',
                ];
                break;
        }

    }

    public function messages()
    {
        $messages = [
            'filter.user_name.exists' => 'The member dosen\'t exists!',
        ];
        return $messages;
    }


    public function attributes()
    {
        $attributes = [
            'filter.user_name' => 'member\'s name',
        ];
        return $attributes;
    }
}
