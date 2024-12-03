<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SetDatabaseRole
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
        if (Auth::check()) {
            $user = Auth::user();
            $dbRole = strtolower($user->role); // Assuming role matches the PostgreSQL role name

            // Set PostgreSQL role for the current session
            DB::statement("SET ROLE \"$dbRole\"");
        }

        return $next($request);
    }
}
