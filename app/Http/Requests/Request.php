<?php

namespace App\Http\Requests;

use Dingo\Api\Http\FormRequest;

class Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function getRequestMethod()
    {
        $controller = $this->route()->action['uses'];

        return explode('@', $controller)[1];
    }

    protected function validationData()
    {
        $data = $this->all();
        $rules = $this->rules();
        # 自定意表单资料过滤条件
        if (empty($rules)){
            return $data;
        }
        
        foreach ($rules as $key => $value) {
            # numeric类，自动过滤千分位
            if((is_array($value) && in_array("numeric", $value))
                || (!is_array($value) && strrpos($value, "numeric") !== false)
            ) {
                if(isset($data[$key])) {
                    $newValue = remove_thousands_number($data[$key]);
                    $this->merge([$key => $newValue]);
                }
            }
        }
        
        return $this->all();
    }
}
