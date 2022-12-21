<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use DB;

class RoleChecker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (!$user->hasAnyRole($roles)) {
            return response()->json(['message' => 'You are not authorized to access this resource'], 403);
        }

        $user->is_active = true;
        return $next($request);
    }
}
