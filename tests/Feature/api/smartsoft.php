<?php

namespace Tests\Feature\api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class smartsoft extends TestCase
{
    public $client;

    # webservice link
    public $wsdl = "http://213.227.140.30/GamblingService/GamblingWebService.asmx?WSDL";

    # 商戶key
    public $ClientExternalKey = "1001";

    # 商戶號
    public $PortalName = "TestPortal";

    # 驗簽key
    public $HashValue = "4f306f1b13bb49759aaced44a44d20d7";

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->client = new \SoapClient($this->wsdl);

        //$result = $this->Transfer("giorgi", "ToGambling", "1", "5b983430-381f-4ee8-af67-c4b478de38d7", "98.127.16.89", "Roulette");


        //$result = $this->GetBalance();

    
        $result = $this->RegisterToken('Bura', 'shuenn', '98.127.16.89', 'Web');

        dd($result);
    }


    # 注册token
    private function RegisterToken($GameType, $UserName, $IpAddress, $DeviceType, $IsClientVerified = true, $CurrencyCode = 'CNY')
    {
        $HashData = implode([$this->HashValue, $this->ClientExternalKey, $this->PortalName, $GameType, $UserName, $IpAddress, $CurrencyCode], ':');

        $HashValue = hash("md5", $HashData);

        $params = new \stdClass();
        $params->request = new \stdClass();
        $params->request->HashValue = $HashValue;
        $params->request->ClientExternalKey =  $this->ClientExternalKey;
        $params->request->PortalName = $this->PortalName;
        $params->request->GameType = $GameType;
        $params->request->UserName = $UserName;
        $params->request->IpAddress = $IpAddress;
        $params->request->DeviceType = $DeviceType;
        $params->request->IsClientVerified = $IsClientVerified;
        $params->request->CurrencyCode = $CurrencyCode;

        $result = $this->client->RegisterToken($params);

        return $result;
    } 

    # 取得馀额
    private function GetBalance($AccountType = "BoardGame", $CurrencyCode = 'CNY'){

        $HashData = implode([$this->HashValue, $this->ClientExternalKey, $this->PortalName, $AccountType, $CurrencyCode], ':');

        $HashValue = hash("md5", $HashData);

        $params = new \stdClass();
        $params->request = new \stdClass();
        $params->request->HashValue =  $HashValue;
        $params->request->ClientExternalKey =  $this->ClientExternalKey;
        $params->request->PortalName = $this->PortalName;
        $params->request->AccountType = $AccountType;
        $params->request->CurrencyCode = $CurrencyCode;

        $result = $this->client->GetBalance($params);

        return $result;
    }

    # 用户转钱
    private function Transfer($UserName, $Direction, $Amount, $TransactionId, $IpAddress, $AccountType = "BoardGame", $CurrencyCode = 'CNY'){

        $HashData = implode([
            $this->HashValue, 
            $this->ClientExternalKey, 
            $this->PortalName, 
            $UserName, 
            $Direction,
            $Amount,
            $TransactionId,
            $IpAddress,
            $AccountType, 
            $CurrencyCode], ':');

        $HashValue = hash("md5", $HashData);

        $params = new \stdClass();
        $params->request = new \stdClass();
        $params->request->HashValue =  $HashValue;
        $params->request->ClientExternalKey =  $this->ClientExternalKey;
        $params->request->PortalName = $this->PortalName;
        $params->request->UserName = $UserName;
        $params->request->Direction = $Direction;
        $params->request->Amount = $Amount;
        $params->request->TransactionId = $TransactionId;
        $params->request->IpAddress = $IpAddress;
        $params->request->AccountType = $AccountType;
        $params->request->CurrencyCode = $CurrencyCode;

        $result = $this->client->Transfer($params);

        return $result;
    }

}
