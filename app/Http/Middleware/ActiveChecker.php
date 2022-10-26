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
                return $next($request);
            }
        }

        return response()->json(["msg" => "Your account is not active"], 401);
    }
}
