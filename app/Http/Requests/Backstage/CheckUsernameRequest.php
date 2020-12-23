<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CheckUsernameRequest extends Request
{
    public function rules()
    {
        $isAgent = $this->input("is_agent") ?? false;
        if ($isAgent) {
            $data = [
                'name' => [
                    'required',
                    'string',
                    Rule::exists('users')->where(function ($query) {
                        return $query->where('is_agent', true);
                    }),
                ]
            ];
        } else {
            $data = [
                'name' => [
                    'required',
                    'string',
                    Rule::exists('users')->where(function ($query) {
                        return $query->where('is_agent', false);
                    }),
                ]
            ];
        }
        return $data;
    }
}
