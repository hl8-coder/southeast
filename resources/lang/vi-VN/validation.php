<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages.
    |
    */

    'accepted'             => 'Quý Khách cần đồng ý và chấp nhận :attribute。',
    'active_url'           => ':attribute đường link không hợp lệ',
    'after'                => ':attribute cần trễ hơn ngày : date.',
    'after_or_equal'       => ':attribute cần phải tương đương :date hoặc trễ hơn.',
    'alpha'                => ':attribute chỉ có thể kết hợp từ chữ cái.',
    'alpha_dash'           => ':attribute chỉ có thể kết hợp từ chữ cái, số, gạch(-)hoặc gạch dưới(_).',
    'alpha_num'            => ':attribute chỉ có thể kết hợp từ chữ cái và số.',
    'array'                => ':attribute cần nhập số.',
    'before'               => ':attribute cần sớm hơn  :date.',
    'before_or_equal'      => ':attribute cần bẳng :date hoặc sớm hơn',
    'between'              => [
        'numeric' => 'Trường :attribute phải nằm trong khoảng :min - :max.',
        'file'    => 'Dung lượng tập tin trong trường :attribute phải từ :min - :max kB.',
        'string'  => 'Trường :attribute phải từ :min - :max ký tự.',
        'array'   => 'Trường :attribute phải có từ :min - :max phần tử.',
    ],
    'boolean'              => ':attribute cần phải là chính xác hoặc lỗi',
    'confirmed'            => ':attribute thông tin nhập lại không trùng khớp.',
    'date'                 => ':attribute không phải là ngày tháng hợp lệ',
    'date_equals'          => ':attribute cần phải tương đương với :date。',
    'date_format'          => ':attribute định dạng cần phải là :format。',
    'different'            => ':attribute và :other cần phải khác nhau.',
    'digits'               => ':attribute cần là :digits chữ số.',
    'digits_between'       => ':attribute cần từ :min và :max chữ số',
    'dimensions'           => ':attribute kích thước không chính xác',
    'distinct'             => ':attribute đã bị trùng lập.',
    'email'                => ':attribute cần phải là địa chỉ Email hợp lệ',
    'exists'               => ':attribute không tồn tại.',
    'file'                 => ':attribute cần phải là văn bản',
    'filled'               => ':attribute không thể bỏ trống.',
    'gt'                   => [
        'numeric' => 'Giá trị trường :attribute phải lớn hơn :value.',
        'file'    => 'Dung lượng trường :attribute phải lớn hơn :value kilobytes.',
        'string'  => 'Độ dài trường :attribute phải nhiều hơn :value kí tự.',
        'array'   => 'Mảng :attribute phải có nhiều hơn :value phần tử.',
    ],
    'gte'                  => [
        'numeric' => 'Giá trị trường :attribute phải lớn hơn hoặc bằng :value.',
        'file'    => 'Dung lượng trường :attribute phải lớn hơn hoặc bằng :value kilobytes.',
        'string'  => 'Độ dài trường :attribute phải lớn hơn hoặc bằng :value kí tự.',
        'array'   => 'Mảng :attribute phải có ít nhất :value phần tử.',
    ],
    'image'                => ':attribute cần phải là :values văn bản.',
    'in'                   => ':attribute không tồn tại.',
    'in_array'             => ':attribute cần phải là :values văn bản.',
    'integer'              => ':attribute cần phải là :values văn bản.',
    'ip'                   => ':attribute cần phải là :values văn bản.',
    'ipv4'                 => ':attribute cần phải là :values văn bản.',
    'ipv6'                 => ':attribute cần phải là :values văn bản.',
    'json'                 => ':attribute cần phải là :values văn bản.',
    'lt'                   => [
        'numeric' => 'Giá trị trường :attribute phải nhỏ hơn :value.',
        'file'    => 'Dung lượng trường :attribute phải nhỏ hơn :value kilobytes.',
        'string'  => 'Độ dài trường :attribute phải nhỏ hơn :value kí tự.',
        'array'   => 'Mảng :attribute phải có ít hơn :value phần tử.',
    ],
    'lte'                  => [
        'numeric' => 'Giá trị trường :attribute phải nhỏ hơn hoặc bằng :value.',
        'file'    => 'Dung lượng trường :attribute phải nhỏ hơn hoặc bằng :value kilobytes.',
        'string'  => 'Độ dài trường :attribute phải nhỏ hơn hoặc bằng :value kí tự.',
        'array'   => 'Mảng :attribute không được có nhiều hơn :value phần tử.',
    ],
    'max'                  => [
        'numeric' => 'Trường :attribute không được lớn hơn :max.',
        'file'    => 'Dung lượng tập tin trong trường :attribute không được lớn hơn :max kB.',
        'string'  => 'Trường :attribute không được lớn hơn :max ký tự.',
        'array'   => 'Trường :attribute không được lớn hơn :max phần tử.',
    ],
    'mimes'                => ':attribute cần phải là :values văn bản.',
    'mimetypes'            => ':attribute cần phải là :values văn bản.',
    'min'                  => [
        'numeric' => 'Trường :attribute phải tối thiểu là :min.',
        'file'    => 'Dung lượng tập tin trong trường :attribute phải tối thiểu :min kB.',
        'string'  => 'Trường :attribute phải có tối thiểu :min ký tự.',
        'array'   => 'Trường :attribute phải có tối thiểu :min phần tử.',
    ],
    'not_in'               => ':attribute không tồn tại.',
    'not_regex'            => ':attribute định dạng không chính xác.',
    'numeric'              => ':attribute cần phải là số.',
    'present'              => ':attribute cần tồn tại.',
    'regex'                => ':attribute định dạng không chính xác.',
    'required'             => ':attribute không thể bỏ trống.',
    'required_if'          => 'Khi :values là :attribute thì không thể bỏ trống.',
    'required_unless'      => 'Khi :values không phải là :attribute thì không thể bỏ trống.',
    'required_with'        => 'Khi :values đều không tồn tại thì :attribute không thể bỏ trống.',
    'required_with_all'    => 'Khi :values đều không tồn tại thì :attribute không thể bỏ trống.',
    'required_without'     => 'Khi :values đều không tồn tại thì :attribute không thể bỏ trống.',
    'required_without_all' => 'Khi :values đều không tồn tại thì :attribute không thể bỏ trống.',
    'same'                 => ':attribute và :other cần phải giống nhau.',
    'size'                 => [
        'numeric' => 'Trường :attribute phải bằng :size.',
        'file'    => 'Dung lượng tập tin trong trường :attribute phải bằng :size kB.',
        'string'  => 'Trường :attribute phải chứa :size ký tự.',
        'array'   => 'Trường :attribute phải chứa :size phần tử.',
    ],
    'starts_with'          => ':attribute cần bắt đầu bằng :values.',
    'string'               => ':attribute cần là 1 chuỗi chữ.',
    'timezone'             => ':attribute cần là thời gian hợp lệ.',
    'unique'               => ':attribute đã tồn tại.',
    'uploaded'             => ':attribute tải lên không thành công.',
    'url'                  => ':attribute địngdạnh không chính xác.',
    'uuid'                 => ':attribute cần là UUID có hiệu lực.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'name'                  => 'tên',
        'username'              => 'tên đăng nhập',
        'email'                 => 'email',
        'first_name'            => 'tên',
        'last_name'             => 'họ',
        'password'              => 'mật khẩu',
        'password_confirmation' => 'xác nhận mật khẩu',
        'city'                  => 'thành phố',
        'country'               => 'quốc gia',
        'address'               => 'địa chỉ',
        'phone'                 => 'số điện thoại',
        'mobile'                => 'di động',
        'age'                   => 'tuổi',
        'sex'                   => 'giới tính',
        'gender'                => 'giới tính',
        'year'                  => 'năm',
        'month'                 => 'tháng',
        'day'                   => 'ngày',
        'hour'                  => 'giờ',
        'minute'                => 'phút',
        'second'                => 'giây',
        'title'                 => 'tiêu đề',
        'content'               => 'nội dung',
        'body'                  => 'nội dung',
        'description'           => 'mô tả',
        'excerpt'               => 'trích dẫn',
        'date'                  => 'ngày',
        'time'                  => 'thời gian',
        'subject'               => 'tiêu đề',
        'message'               => 'lời nhắn',
        'available'             => 'có sẵn',
        'size'                  => 'kích thước',
    ],

];
