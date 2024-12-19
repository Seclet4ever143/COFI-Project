<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class SetDBConnection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    { {
            // Default connection
            $connection = config('database.default');
            $userId = null; // Default to null if no user is authenticated

            if (Auth::check()) {
                $user = Auth::user();
                $userId = $user->id; // Retrieve the authenticated user's ID

                // Dynamically set connection based on role
                $connection = match ($user->role_id) {
                    1 => 'admin',
                    2 => 'staff',
                    3 => 'customer',
                    default => config('database.default'),
                };
            }

            // Purge and reconnect
            DB::purge($connection);
            config(['database.default' => $connection]);
            DB::reconnect($connection);

            // Set the session variable for the user ID in PostgreSQL
            if ($userId) {
                DB::statement("SET myapp.user_id = {$userId}");
            }

            return $next($request);
        }
    }
}