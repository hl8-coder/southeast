<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Dingo\Api\Routing\Helpers;

class CheckDevice
{
    use Helpers;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $deviceList = array_keys(User::$devices);
        $device = $request->header('device', User::DEVICE_PC);
        if (!in_array($device, $deviceList)){
            return $this->response->error(__('middleware/api/checkdevice.wrong_device'), 422);
        }

        return $next($request);
    }
}
