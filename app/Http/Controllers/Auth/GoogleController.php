<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

   
public function handleGoogleCallback()
{
    try {
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Check if user exists
        $user = User::where('email', $googleUser->email)->first();

        if (!$user) {
            return redirect()->route('login')->with('status', 'Account not found. Please register first.');
        }

        // Log the user in
        Auth::login($user);

        switch ($user->role_id) {
            case 1:
                // Admin dashboard
                return redirect()->route('dash-board');
            case 2:
                // Staff dashboard
                return redirect()->route('dash-board');
            case 3:
                // Customer dashboard
                
                
               return redirect()->intended('/'); // Adjust redirect as needed
            default:
                // Fallback to home or another default route
                return redirect()->route('/');
            }

    } catch (\Exception $e) {
        return redirect()->route('login')->with('status', 'An error occurred during login. Please try again.');
    }
}


}
