<?php

use Illuminate\Support\Facades\DB;

if (!function_exists('error_response')) {
    function error_response($statusCode, $message = null, $code = 0)
    {
        throw new \Symfony\Component\HttpKernel\Exception\HttpException($statusCode, $message, null, [], $code);
    }
}

if (!function_exists('transform_list')) {
    function transform_list($list, $keyField = 'key', $valueFiled = 'value')
    {
        $result = [];
        $index  = 0;
        foreach ($list as $key => $value) {
            $result[$index][$keyField]   = $key;
            $result[$index][$valueFiled] = $value;
            $index++;
        }
        return $result;
    }
}

# 反查对应语系文字
if (!function_exists('transfer_lang_value')) {
    function transfer_lang_value($lang_file, $array)
    {

        foreach ($array as $key => $value) {
            $array[$key] = __($lang_file . '.' . $value);
        }
        return $array;
    }
}

# 获取接口返回字段对应的显示值
if (!function_exists('transfer_show_value')) {
    function transfer_show_value($key, $array)
    {
        return isset($array[$key]) ? $array[$key] : '';
    }
}

/**
 * 转化数组字段对应的显示值
 */
if (!function_exists('transfer_array_show_value')) {
    function transfer_array_show_value($values, $array)
    {
        if (!is_array($values)) {
            return [];
        }

        return array_map(function ($value) use ($array) {
            return transfer_show_value($value, $array);
        }, $values);
    }
}

/**
 * curl
 */
if (!function_exists('call_api')) {
    function call_api(
        $url,
        $data = [],
        $headers = [],
        $method = 'post',
        $dataType = 'form_params',
        $timeout = 3000
    )
    {
        $http = new \GuzzleHttp\Client();

        $response = $http->$method($url, [
            'timeout'     => $timeout,
            'http_errors' => false,
            $dataType     => $data,
            'headers'     => $headers,
            'verify'      => false,
        ]);

        return $response;
    }
}

/**
 * 解析返回数据格式
 */
if (!function_exists('get_response_body')) {
    function get_response_body($response, $type = '', $platformCode = '')
    {
        try {
            $body = (string)$response->getBody();
            switch ($type) {
                case 'json':
                    $result = json_decode($body, true);
                    break;
                case 'xml':
                    $result = simplexml_load_string($body, 'SimpleXMLElement', LIBXML_NOCDATA);
                    $result = json_decode(json_encode($result), true);
                    break;
                default:
                    $result = $body;
                    break;
            }
        } catch (\Exception $e) {
            $body = (string)$response->getBody();
            \Illuminate\Support\Facades\Log::info($platformCode . ' response content:' . $body);
            error_response(422, 'parse error.');
        }

        return $result;
    }
}

/**
 * 获取图片地址
 */
if (!function_exists('get_image_url')) {
    function get_image_url($path)
    {
        if (app()->isLocal()) {
            $path = config('app.url') . '/' . $path;
        } else {
            $path = '/static/' . $path;
        }
        return $path;
    }
}

/**
 * 转换时间格式
 */
if (!function_exists('convert_time')) {
    function convert_time($time)
    {
        return $time instanceof \Carbon\Carbon ? $time->toDateTimeString() : '';
    }
}

/**
 * 转换时间格式
 */
if (!function_exists('convert_date')) {
    function convert_date($time) {
        return $time instanceof \Carbon\Carbon  ? $time->toDateString() : '';
    }
}

/**
 * 批量插入包装数据
 */
if (!function_exists('wrap_batch_insert_value')) {
    function wrap_batch_insert_value(array $values)
    {
        $parameters = collect($values)->map(function ($record) {
            $record = collect($record)->map(function ($detail) {
                $detail = is_array($detail) ? (string)json_encode($detail) : (string)$detail;
                return addslashes($detail);
            })->toArray();
            $record = '"' . implode('", "', array_values($record)) . '"';
            return '(' . $record . ')';
        })->implode(', ');

        return $parameters;
    }
}

/**
 * 批量插入
 */
if (!function_exists('batch_insert')) {
    function batch_insert($table, array $values, $isIgnore = false)
    {
        $columns = implode(', ', array_keys(reset($values)));

        $parameters = wrap_batch_insert_value($values);

        if ($isIgnore) {
            return DB::statement("insert ignore into {$table} ({$columns}) values {$parameters}");
        } else {
            return DB::statement("insert into {$table} ({$columns}) values {$parameters}");
        }
    }
}

if (!function_exists('to_camel_case')) {
    function to_camel_case($str)
    {
        $array  = explode('_', $str);
        $result = $array[0];
        $len    = count($array);
        if ($len > 1) {
            for ($i = 1; $i < $len; $i++) {
                $result .= ucfirst($array[$i]);
            }
        }
        return $result;
    }
}

/**
 * 去除数组中为null的值
 */
if (!function_exists('remove_null')) {
    function remove_null($data)
    {
        return collect($data)->filter(function ($value) {
            return !is_null($value);
        })->toArray();
    }
}

/**
 * 格式化数字【直接去除未保留位小数】
 */
if (!function_exists('format_number')) {
    function format_number($val, $precision = 4)
    {
        return sprintf("%." . $precision . "f", substr(sprintf("%." . ($precision + 1) . "f", $val), 0, -1));
    }
}

/**
 * 千分位格式化数组
 */
if (!function_exists('thousands_number')) {
    function thousands_number($val, $precision = 2)
    {
        return number_format($val, $precision);
    }
}

/**
 * 去除千分位
 */
if (!function_exists('remove_thousands_number')) {
    function remove_thousands_number($val)
    {
        return str_replace(",", "", $val);
    }
}

if (!function_exists('get_replace_key')) {
    function get_replace_key($data, $search, $replace)
    {
        $result = [];
        foreach ($data as $value) {
            $result[][$replace] = $value[$search];
        }

        return $result;
    }
}

/**
 * 隐藏数字
 */
if (!function_exists('hidden_number')) {
    function hidden_number($str, $length, $direction = 'right')
    {
        $keepLength = strlen($str) - $length;
        if ($keepLength < 0) {
            return $str;
        }
        if ('right' == $direction) {
            return substr_replace($str, str_repeat('*', $keepLength), 0, $keepLength);
        } else {
            return substr_replace($str, str_repeat('*', $keepLength), $length);
        }
    }
}

/**
 * 隐藏会员名
 */
if (!function_exists('hidden_name')) {
    function hidden_name($str, $start = 3, $end = 2)
    {
        switch (strlen($str))
        {
            case 5:
                $start = 1;
                $end = 1;
                break;
            case 6:
                $start = 2;
                $end = 1;
                break;
            case 7:
                $start = 2;
                $end = 2;
                break;
            default:
                $start = 3;
                $end = 2;
                break;
        }
        $hideLen = strlen($str) - $start - $end;
        return substr_replace($str, str_repeat('*', $hideLen), $start, strlen($str) - $start - $end);
    }
}

/**
 * 获取毫秒级时间戳
 */
if (!function_exists('milli_time')) {
    function milli_time()
    {
        return (string)round(microtime(true) * 1000);
    }
}

/**
 * validate验证in字符串获取
 */
if (!function_exists('get_validate_in_string')) {
    function get_validate_in_string($data)
    {
        if (!is_array($data)) {
            $data = [$data];
        }

        return implode(',', array_keys($data));
    }
}
/**
 * 将一位数组打乱随机分成几个新数组，key 不保留旧的，不够平均的最后几个数组将会平均每个少一个单元
 */
if (!function_exists('array_slice_ave')) {
    function array_slice_ave(array $array, int $part) {
        $countArray    = count($array);
        $partLen       = floor( $countArray / $part );
        $arrayItemRest = $countArray % $part;

        $partition = array();
        $mark = 0;

        shuffle($array);

        for ($newArrayKey = 0; $newArrayKey < $part; $newArrayKey++) {

            $newArrayLen = ($newArrayKey < $arrayItemRest) ? $partLen + 1 : $partLen;

            $partition[$newArrayKey] = array_slice($array, $mark, $newArrayLen);

            $mark += $newArrayLen;
        }
        return $partition;
    }
}
