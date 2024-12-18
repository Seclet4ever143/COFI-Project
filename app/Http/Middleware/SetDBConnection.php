<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SetDBConnection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       

     
        $user = Auth::user();
        //DB::statement("SET myapp.user_id = " . (int) $user->user_id);
        //DB::statement("SET myapp.user_id = " . (int) $user->user_id);
        //dd($user->role_id);
        // Determine the database connection based on user's role
        switch ($user->role_id) {
            case 1:
                DB::purge('admin'); // Purge the existing connection
                config(['database.default' => 'admin']);
                DB::reconnect('admin'); // Reconnect using the admin configuration
                // $pdo = DB::connection('admin')->getName();
                // dd($pdo);
                //DB::statement("SET myapp.user_id = " . (int) $user->user_id);
                session(['connection' => 'admin']);
                break;
            case 2:
                config(['database.default' => 'staff']);
                DB::purge('staff');
                DB::reconnect('staff');
                //DB::statement("SET myapp.user_id = " . (int) $user->user_id);
                session(['connection' => 'staff']);
                break;
            case 3:
                config(['database.default' => 'customer']);
                DB::purge('customer');
                DB::reconnect('customer');
               // DB::statement("SET myapp.user_id = " . (int) $user->user_id);
                session(['connection' => 'customer']);
                break;
            default:
                // Fallback to the primary connection
                config(['database.default' => env('DB_CONNECTION', 'pgsql')]);
                DB::purge(env('DB_CONNECTION', 'pgsql'));
                DB::reconnect(env('DB_CONNECTION', 'pgsql'));
                break;
        }
        DB::statement("SET myapp.user_id = " . (int) 1);
        return $next($request);
    }
}