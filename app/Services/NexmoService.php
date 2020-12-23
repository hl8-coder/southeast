<?php


namespace App\Services;


use Illuminate\Support\Facades\Log;

class NexmoService
{

    public $config;

    private $logFileName = 'nexmo';

    public function __construct()
    {
        $this->config = config('nexmo');
        if (!$this->validConfig()) {
            throw new \Exception('Init config failed!!!');
        }
    }

    public function validConfig()
    {
        return !empty($this->config);
    }

    public function sms($phone, $code, $currency)
    {
        if (app()->isLocal()) {
            $options = [
                'base_rest_url' => 'https://rest.nexmo.com',
                'base_api_url'  => 'https://api.nexmo.com',
            ];
        } else {
            $options = [
                'base_rest_url' => 'http://rest.nexmo.com',
                'base_api_url'  => 'http://api.nexmo.com',
            ];
        }


        $basic  = new \Nexmo\Client\Credentials\Basic($this->config['api_key'], $this->config['api_secret']);
        $client = new \Nexmo\Client($basic, $options);

        $from = $currency == 'VND' ? '84869412011' : 'Nexmo';

        $data = [
            'to'   => $phone,
            'from' => $from,
            'text' => $code
        ];

        try {
            $message = $client->message()->send($data);
            $this->writeLog('','', http_build_query($data));
            $response = $message->getResponseData();
            $this->writeLog(http_build_query($response['messages'][0]), 'SMS');
        } catch (\Exception $e) {
            $this->writeLog($e->getMessage(), 'SMS');
        }
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