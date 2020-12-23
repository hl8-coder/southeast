<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\Remark;
use Illuminate\Validation\Rule;

class RemarkRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $methodName = $this->getRequestMethod();
        switch ($methodName) {
            case 'store':
                return [
                    'user_id'      => 'required|integer|exists:users,id',
                    'type'         => 'required|in:' . implode(',', array_keys(Remark::$types)),
                    'category'     => 'required|in:' . implode(',', array_keys(Remark::$categories)),
                    'sub_category' => 'nullable|in:' . implode(',', array_keys(Remark::$subCategories)),
                    'reason'       => 'required|string|max:1024',
                ];
                break;
            case 'storeRemarkByUsername':
                return [
                    'name'    => [
                        'required',
                        Rule::exists('users')->where(function ($query) {
                            return $query->where('is_agent', false);
                        }),
                    ],
                    'type'         => 'required|in:' . implode(',', array_keys(Remark::$types)),
                    'category'     => 'required|in:' . implode(',', array_keys(Remark::$categories)),
                    'sub_category' => 'nullable|in:' . implode(',', array_keys(Remark::$subCategories)),
                    'reason'       => 'required|string|max:1024',
                ];
                break;

            case 'destroy':
                return [
                    'remove_reason' => 'required|string|max:1024',
                ];
                break;
            case 'index':
                return [
                    'filter.user_name' => 'nullable|exists:users,name'
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
