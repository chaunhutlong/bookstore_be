<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ActiveChecker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // if all user's role is not active, return 401
        $user = $request->user();
        foreach ($user->roles as $role) {
            if ($role->pivot->active == 1) {
                // set is_active to true
                $user->is_active = true;
                return $next($request);
            }
        }

        // set is_active to false
        $user->is_active = false;

        return response()->json(["msg" => "Your account is not active"], 401);
    }
}
