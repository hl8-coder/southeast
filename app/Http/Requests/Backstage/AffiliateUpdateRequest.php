<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\Language;
use Illuminate\Validation\Rule;

class AffiliateUpdateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // $language = array_keys(Language::getDropList());
        $user = $this->route('affiliate');
        return [
            'is_fund_open'                => 'nullable|in:0,1',
            'commission_setting'          => 'nullable|array',
            'commission_setting.*.tier'   => 'nullable|integer',
            'commission_setting.*.title'  => 'nullable|string',
            'commission_setting.*.value'  => 'nullable|integer',
            'commission_setting.*.profit' => 'nullable|integer',
            'password'                    => 'nullable|string',
            'full_name'                   => 'nullable|string',
            'email'                       => [
                'nullable',
                'email',
                Rule::unique('user_info')->where(function ($query) {
                    return $query->where('is_agent', true);
                })->ignore($user->id, 'user_id'),
            ],
            'phone'                       => [
                'nullable',
                'integer',
                Rule::unique('user_info')->where(function ($query) {
                    return $query->where('is_agent', true);
                })->ignore($user->id, 'user_id'),
            ],
            'status'                      => 'nullable|in:1,2,3,4',
            'birth_at'                    => 'nullable|date',
            'describe'                    => 'nullable|array',
            'describe.*.language'         => 'required|string|in:' . implode(',', array_keys(Language::getDropList())),
            'describe.*.content'          => 'required|string',
        ];
    }
}
