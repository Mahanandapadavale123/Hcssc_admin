<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleGroupMiddleware
{
    public function handle($request, Closure $next, $group)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $roles = $user->roles()->where('group_type', $group)->exists();

        if (!$roles) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
