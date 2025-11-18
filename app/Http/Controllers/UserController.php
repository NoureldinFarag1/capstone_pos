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
    public function index(Request $request)
    {
        // Available roles for filtering (normalized)
        $availableRoles = ['Admin','Moderator','Cashier'];
        $selectedRole = $request->query('role');
        $normalizedSelected = $selectedRole ? ucfirst(strtolower($selectedRole)) : null;

        // Base query with eager loading
        $query = User::with('roles');

        if ($normalizedSelected && in_array($normalizedSelected, $availableRoles, true)) {
            $query->whereHas('roles', function($q) use ($normalizedSelected) {
                $q->where(DB::raw('LOWER(name)'), strtolower($normalizedSelected));
            });
        }

        $users = $query->get();

        // Case-insensitive role counts to handle inconsistent role naming (e.g., 'admin' vs 'Admin')
        $roleCounts = User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_type', User::class)
            ->select(DB::raw('LOWER(roles.name) as name_lower'), DB::raw('COUNT(*) as count'))
            ->groupBy('name_lower')
            ->pluck('count', 'name_lower')
            ->toArray();

        $adminCount = $roleCounts['admin'] ?? 0;
        $moderatorCount = $roleCounts['moderator'] ?? 0;
        $cashierCount = $roleCounts['cashier'] ?? 0;

    return view('users.index', compact('users', 'adminCount', 'moderatorCount', 'cashierCount', 'availableRoles', 'normalizedSelected'));
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
            // Allow both capitalized and lowercase variants for robustness
            'role' => 'required|in:admin,Admin,moderator,Moderator,cashier,Cashier',
        ]);
        $username = strtolower(str_replace(' ', '_', $request->name));
        // Normalize role naming to capitalized form used in counts/display
        $roleInput = $request->role;
        $normalizedRole = ucfirst(strtolower($roleInput));
        $email = "{$username}@" . strtolower($normalizedRole) . ".lhub";

        $user = User::create([
            'name' => $request->name,
            'email' => $email,
            'password' => Hash::make($request->password),
        ]);

    $user->assignRole($normalizedRole);

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
            'role' => 'required|in:admin,Admin,moderator,Moderator,cashier,Cashier',
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
        $newRoleInput = $request->role;
        $normalizedRole = ucfirst(strtolower($newRoleInput));
        if ($oldRole !== $normalizedRole) {
            $username = strtolower(str_replace(' ', '_', $request->name));
            $user->email = "{$username}@" . strtolower($normalizedRole) . ".lhub";
        }

        $user->save();

    $user->syncRoles($normalizedRole); // Sync new role
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
