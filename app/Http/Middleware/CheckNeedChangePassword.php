<?php

namespace App\Http\Middleware;

use App\Models\Currency;
use App\Models\User;
use Closure;
use Illuminate\Support\Facades\App;

class CheckNeedChangePassword
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
        $user = $request->user();
        if($user && $user->is_need_change_password){
            error_response(422, __('user.NEED_CHANGE_PASSWORD'));
        }

        return $next($request);
    }
}
