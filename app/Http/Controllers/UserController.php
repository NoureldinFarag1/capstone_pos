<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    use AuthorizesRequests;
    // List all users
    public function index()
    {
        // OPTIMIZED: Get users with roles using eager loading (already good)
        $users = User::with('roles')->get();

        // OPTIMIZED: Replace multiple role count queries with a single query
        $roleCounts = User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_type', User::class)
            ->select('roles.name', DB::raw('COUNT(*) as count'))
            ->groupBy('roles.name')
            ->pluck('count', 'name')
            ->toArray();

        $adminCount = $roleCounts['Admin'] ?? 0;
        $moderatorCount = $roleCounts['Moderator'] ?? 0;
        $cashierCount = $roleCounts['Cashier'] ?? 0;

        return view('users.index', compact('users', 'adminCount', 'moderatorCount', 'cashierCount'));
    }

    // Create user view
    public function create()
    {
        $this->authorize('admin');
        return view('users.create');
    }

    // Store a new user
    public function store(Request $request)
    {
        $this->authorize('admin');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,manager,cashier',
        ]);
        $username = strtolower(str_replace(' ', '_', $request->name));

        $role = $request->role;
        $email = "{$username}@{$role}.lhub";

        $user = User::create([
            'name' => $request->name,
            'email' => $email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    // Edit user view
    public function edit($id)
    {
        $this->authorize('admin');
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    // Update user details
    public function update(Request $request, $id)
    {
        $this->authorize('admin');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,moderator,cashier',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        $oldRole = $user->getRoleNames()->first();

        // Update the user's name
        $user->name = $request->name;

        // If the role has changed, update the email
        if ($oldRole !== $request->role) {
            // Replace spaces with underscores in the name
            $username = strtolower(str_replace(' ', '_', $request->name));

            // Generate the new email based on the role
            $role = $request->role;
            $user->email = "{$username}@{$role}.com";
        }

        $user->save();

        $user->syncRoles($request->role); // Sync new role
        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    // Delete a user
    public function destroy($id)
    {
        $this->authorize('admin');
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}
