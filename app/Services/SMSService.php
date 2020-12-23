<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SMSService
{
    public $config;
    public $providerCode;
    private $secret = 'API@CallHttp2FFT';
    private $logFileName = 'fly_one_talk';

    public function __construct()
    {
        if (app()->isLocal()) {
            $this->config = config('sms');
        } else {
            $this->config = config('sms_product');
        }
        if (!$this->validConfig()) {
            throw new \Exception('Init config failed!!!');
        }
        $this->providerCode = $this->config['provider_code'];
    }

    /**
     * 发信息
     * @param $phone
     * @param $content
     * @return mixed
     */
    public function sms($phone, $content, $currency)
    {
        $data = [
            'account'  => $this->config['account'],
            'passcode' => $this->config['passcode'],
            'phone'    => $phone,
            'sms'      => $content,
        ];

        return $this->callApi(__FUNCTION__, $data);
    }

    /**
     * 打电话
     * @param $phone
     * @return mixed
     */
    public function call($phone)
    {
        $data = [
            'outnumber'  => $phone,
            'ext'        => $this->config['ext'],
            'secret'     => $this->secret,
            'department' => $this->config['department'],
        ];

        return $this->callApi(__FUNCTION__, $data);
    }

    public function validConfig()
    {
        return !empty($this->config);
    }

    protected function callApi($function, $data)
    {
        $apiKey = $function . '_url';
        $url          = $this->config[$apiKey] . '?' . http_build_query($data);

        $this->writeLog('','',$url);

        $response     = call_api($url, [], [], 'get','form_params',10);
        $jsonResponse = (string)$response->getBody();

        $this->writeLog($jsonResponse, $function);

        return $this->parse($jsonResponse);
    }

    protected function parse($data)
    {
        return json_decode($data, 1);
    }

    protected function writeLog($log, $function = '', $requestUrl = '', $requestData = [])
    {
        $content = '';

        if (!empty($function)) {
            $content .= ' function: ' . $function;
        }
        if (!empty($requestUrl)) {
            $content .= ' request url: ' . $requestUrl;
        }
        if (!empty($requestData)) {
            $content .= ' request data: ' . $function;
        }
        $content .= ' response: ' . $log;

        Log::stack([$this->logFileName])->info($content);
    }
}