<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AccountController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:15|unique:users', // Phone validation
            'password' => 'required|string|min:8',
            'role' => 'required|in:staff,admin',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'], // Save phone to database
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'], // Assuming `role` column exists in the users table
        ]);

        return back()->with('message', 'Account created successfully!');
    }

    public function getGroupedUsers()
    {
        $users = [
            'admin' => User::where('role', 'admin')->get(),
            'staff' => User::where('role', 'staff')->get(),
            'customer' => User::where('role', 'customer')->get(),
        ];

        return response()->json($users);
    }

    public function update(Request $request, $id)
    {
        // Validate incoming data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id, // Ignore current user's email
            'phone' => 'required|string|max:15|unique:users,phone,' . $id, // Ignore current user's phone
            'role' => 'required|in:staff,admin',
        ]);

        // Find the user to update
        $user = User::findOrFail($id);

        // Update the user data
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
        ]);

        return back()->with('message', 'Account updated successfully!');
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }


}
