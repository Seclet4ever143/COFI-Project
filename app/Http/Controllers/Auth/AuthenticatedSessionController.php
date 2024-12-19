<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();
     


        $user = DB::table('users')
            ->where('email', $request->email)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        $role_id = $user->role_id; // Retrieve user's role_id
        // Change database connection based on user's role
        switch ($role_id) {
            case 1:
                DB::purge('admin'); // Disconnect existing connection to avoid caching
                // Update the default database connection to 'manager'
                config(['database.default' => 'admin']);
                break;
            case 2:
                DB::purge('staff');
                // Update the default database connection to 'employee'
                config(['database.default' => 'staff']);

                break;
            case 3:
                DB::purge('customer');
                // Update the default database connection to 'admin'
                config(['database.default' => 'customer']);
                break;
            default:
                // Fallback to the default connection defined in .env
                config(['database.default' => env('DB_CONNECTION', 'pgsql')]);
                DB::purge(env('DB_CONNECTION', 'pgsql'));
                break;
        }

        // Attempt login
        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Get authenticated user
            $user = Auth::user();

            switch ($user->role_id) {
                case 1:
                    return redirect()->route('dash-board');
                    
                case 2:
                    return redirect()->route('dash-board');
                    
                case 3:
                    return redirect()->route('dash-board');
                    
            }   //dd(config('database.default'));

        }

        return redirect()->intended(route('/', absolute: false));
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
