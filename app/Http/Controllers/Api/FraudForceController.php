<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\FraudForceRequest;
use App\Models\Config;
use App\Services\FraudForceService;

class FraudForceController extends ApiController
{
    /**
     * @OA\Post(
     *      path="/fraud_force/login",
     *      operationId="api.fraud_force.login",
     *      tags={"Api-第三方风控"},
     *      summary="第三方风控",
     *      @OA\Response(response=204, description="No Content",),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function login(FraudForceRequest $request)
    {
        $data                = remove_null($request->all());
        $operationId         = Config::findValue('operation_id');
        $user                = $this->user();
        $data['statedIp']    = $user->info->register_ip;
        $data['type']        = 'login_' . $operationId;
        $data['accountCode'] = $this->getAccountCode($operationId, $user);
        $fraudForce          = new FraudForceService();
        $fraudForce->login($data);
        return $this->response->noContent();
    }
    /**
     * @OA\Post(
     *      path="/fraud_force/register",
     *      operationId="api.fraud_force.register",
     *      tags={"Api-第三方风控"},
     *      summary="第三方风控",
     *      @OA\Response(response=204, description="No Content",),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function register(FraudForceRequest $request)
    {
        $data                = remove_null($request->all());
        $operationId         = Config::findValue('operation_id');
        $user                = $this->user();
        $data['statedIp']    = $user->info->register_ip;
        $data['type']        = 'registration_' . $operationId;
        $data['accountCode'] = $this->getAccountCode($operationId, $user);
        $fraudForce          = new FraudForceService();
        $fraudForce->login($data);
        return $this->response->noContent();
    }

    public function getAccountCode($operationId, $user)
    {
        $suffix = $user->is_agent ? 'aff' : '';
        return $operationId . $suffix . '_' . $user->name;
    }
}
