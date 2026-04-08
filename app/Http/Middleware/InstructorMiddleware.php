<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstructorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['instructor', 'admin'])) {
            abort(403, 'Unauthorized access. Instructor only area.');
        }

        return $next($request);
    }
}
