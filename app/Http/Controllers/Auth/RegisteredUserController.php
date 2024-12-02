<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:11|unique:users', // Make it optional
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone, // Save phone
            'password' => Hash::make($request->password),
            'role' => 'customer', // Automatically assign "customer" role
        ]);
    
        Auth::login($user);
    
        return redirect()->intended('dashboard');
    }
}