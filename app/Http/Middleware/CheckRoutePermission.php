<?php

namespace App\Http\Middleware;

use App\Models\Action;
use App\Models\Config;
use Closure;

class CheckRoutePermission
{
    # 忽略权限
    protected $except = [
        'backstage.drop_list.index',
        'backstage.admins.menu',
        'backstage.admins.change_pwd',
    ];

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $action = app('api.router')->current()->getAction()['as'];
        $admin  = $request->user();

        if ($this->checkCodeNeedAuth($action) && !$admin->is_super_admin) {
            $actions = Action::query()->where('action', $action)->get();
            if ($action) {
                $adminRoleIds = [];
                $actions->each(function ($action) use (&$adminRoleIds) {
                    $adminRoleIds = array_merge($adminRoleIds, $action->adminRoles->pluck('id')->toArray());
                });
                if (!$admin->roles()->whereIn('admin_role_id', $adminRoleIds)->exists()) {
                    $message = Config::findValue('auth_forbidden_notice', 'Permission Deny!');
                    error_response(403, $message);
                }
            } else {
                $message = Config::findValue('auth_forbidden_notice', 'Permission Deny!');
                error_response(403, $message);
            }
        }

        return $next($request);
    }

    private function checkCodeNeedAuth($action)
    {
        $isNeed = true;
        foreach ($this->except as $value) {
            if ($isNeed) {
                if (strrpos($value, '*') === false) {
                    $isNeed = !($action == $value);
                } else {
                    $isNeed = !(strrpos($action, str_replace('*', '', $value)) !== false);
                }
            }
        }
        return $isNeed;
    }
}
