<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
{
    if (!auth()->check() || auth()->user()->role !== $role) {
        abort(403); // Or redirect as per your logic
    }

    return $next($request);
}

}
