<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class SystemsController extends BackstageController
{
    public function index()
    {
        #Redis
        $redis    = false;
        $cacheKey = 'REDIS_STATUS';
        try {
            Cache::put($cacheKey, 123123123, now()->addSecond(10));
        } catch (\Exception $exception) {
            $redis = false;
        }

        if (Cache::has($cacheKey)) {
            $redis = true;
        }

        Cache::forget($cacheKey);

        $data           = [];
        $data['data'][] = [
            'name'   => 'redis',
            'status' => $redis,
        ];

        return $this->response->array($data);
    }
}
