<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:admin,moderator,cashier',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        $user->assignRole($validated['role']);
        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function showUsers()
    {
        $users = User::with('roles')->get(); // Fetch users with their roles
        return view('admin.users.index', compact('users')); // Pass data to the view
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all(); // Get all available roles
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->syncRoles($request->input('roles')); // Sync roles to avoid duplicates
        return redirect()->route('users.index')->with('success', 'Roles updated successfully');
    }

}
