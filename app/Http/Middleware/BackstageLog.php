<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class BackstageLog
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
        $admin = $request->user();
        if ('GET' != $request->method()) {
            $data['admin_name'] = $admin ? $admin->name : '';
            $data['url'] = $request->url();
            $data['method'] = $request->method();
            $data['request_data'] = $request->all();
            Log::stack(['backstage_log'])->info('请求信息 : ' . json_encode($data));
        }

        $response = $next($request);
        if ('GET' != $request->method()) {
            Log::stack(['backstage_log'])->info('返回信息 : ' . $response->getContent());
        }

        return $response;
    }
}
