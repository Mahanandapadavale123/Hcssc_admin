<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;

class RoleCoE
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'CoE') {
            abort(403, 'Access denied. TP User only.');
        }

        return $next($request);
    }
}
