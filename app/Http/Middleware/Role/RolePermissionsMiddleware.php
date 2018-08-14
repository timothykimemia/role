<?php

namespace App\Http\Middleware\Role;

use Closure;

class RolePermissionsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param $role
     * @param null $permission
     * @return mixed
     */
    public function handle($request, Closure $next, $role, $permission = null)
    {
        if (!$request->user()->hasRole($role)):
            return back()->with('warning', "You don't have Access.");
        endif;

        if ($permission !== null && !$request->user()->can($permission)):
            return back()->with('warning', "You don't have Permission on this Action.");
        endif;

        return $next($request);
    }
}
