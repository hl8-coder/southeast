<?php


namespace App\Services;


use Illuminate\Support\Facades\Log;

class FraudForceService
{
    public $config;

    private $token;

    private $prefix = 'Basic';

    private $logFileName = 'fraud_force';

    private $http;

    public function __construct()
    {
        $this->config = config('fraud_force');

        if (!$this->validConfig()) {
            throw new \Exception('Init config failed!!!');
        }

        if (app()->isLocal()) {
            $this->http = $this->config['local'];
        } else {
            $this->http = $this->config['product'];
        }

        $this->createdToken();

    }

    /**
     * 生成Token
     */
    public function createdToken()
    {
        $username    = $this->config['subscriberID'] . '/' . $this->config['subscriberAccount'];
        $password    = $this->config['subscriberPassCode'];
        $this->token = $this->prefix . ' ' . base64_encode($username . ':' . $password);
    }

    /**
     * 设置header
     */
    public function setHeader()
    {
        return [
            'Content-Type'  => 'application/json',
            'Authorization' => $this->token,
        ];
    }

    /**
     * 检查设置
     * @return bool
     */
    public function validConfig()
    {
        return !empty($this->config);
    }

    /**
     * 检测第三方数据，是否在黑名单里面
     * @param $data
     * @return mixed
     */
    public function login($data)
    {
        $apiInfo = $this->config['url']['login'];
        $url     = $this->http.$apiInfo['url'];
        $method  = $apiInfo['method'];
        $data    = json_encode($data);
        return $this->callApi($url, $data, $method);
    }

    public function add($data)
    {
        $apiInfo = $this->config['url']['add'];
        $url     = $this->http.$apiInfo['url'];
        $method  = $apiInfo['method'];
        return $this->callApi($url, json_encode($data), $method);
    }

    public function update($evidenceId, $data)
    {
        $apiInfo = $this->config['url']['update'];
        $url     = $apiInfo['url'] . $evidenceId;
        $method  = $apiInfo['method'];
        return $this->callApi($url, json_encode($data), $method);
    }

    public function get($data)
    {
        $apiInfo = $this->config['url']['get'];
        $url     = $this->http.$apiInfo['url'];
        $method  = $apiInfo['method'];
        return $this->callApi($url, json_encode($data), $method);
    }

    public function retractingEvidence($evidenceId, $data)
    {
        $apiInfo = $this->config['url']['retracting_evidence'];
        $url     = $this->http.$apiInfo['url'];
        $url     = str_replace('{evidenceId}', $evidenceId, $url);
        $method  = $apiInfo['method'];
        return $this->callApi($url, json_encode($data), $method);
    }

    public function retractingAllEvidence($data)
    {
        $apiInfo = $this->config['url']['retracting_all_evidence'];
        $url     = $this->http.$apiInfo['url'];
        $method  = $apiInfo['method'];
        return $this->callApi($url, json_encode($data), $method);
    }

    public function callApi($url, $data, $method)
    {
        $response = call_api($url, $data, $this->setHeader(), $method, 'body', 10);

        $jsonResponse = (string)$response->getBody();

        $this->writeLog($jsonResponse, $url);

        $result = $this->parse($jsonResponse);

        $this->writeLog($jsonResponse);

        return $result;
    }

    protected function writeLog($log, $requestUrl='')
    {
        $content = '';
        if (!empty($requestUrl)) {
            $content .= ' request url: ' . $requestUrl;
        }
        $content .= ' response: ' . $log;

        Log::stack([$this->logFileName])->info($content);
    }

    protected function parse($data)
    {
        return json_decode($data, 1);
    }
}
