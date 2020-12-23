<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class ApiLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ('GET' != $request->method()) {
            $data['url'] = $request->url();
            $data['method'] = $request->method();
            $data['request_data'] = $request->all();
            Log::stack(['api_log'])->info('请求信息 : ' . json_encode($data));
        }

        $response = $next($request);
        if ('GET' != $request->method()) {
            Log::stack(['api_log'])->info('返回信息 : ' . $response->getContent());
        }

        return $response;
    }
}
