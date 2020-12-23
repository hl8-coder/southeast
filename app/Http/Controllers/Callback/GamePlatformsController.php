<?php

namespace App\Http\Controllers\CallBack;

use App\Http\Controllers\CallbackController;
use Illuminate\Http\Request;
use App\Models\GamePlatform;
use App\Services\GamePlatformService;

class GamePlatformsController extends CallbackController
{
    protected $service;

    public function __construct(GamePlatformService $service)
    {
        $this->service = $service;
    }

    public function login(Request $request)
    {
        if (!$gamePlatform = GamePlatform::findByCode($request->route('code'))) {
            return $this->response->errorInternal();
        }

        $data = $request->getQueryString() ? $request->all() : $request->getContent();

        $result = $this->service->loginCallBack(null, $gamePlatform, $data);

        return $this->response->accepted(null, $result)->setStatusCode(200);
    }
}
