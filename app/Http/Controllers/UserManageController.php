<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Profile;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Middleware\RoleMiddleware;


class UserManageController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:Superadmin')->only(['index', 'store', 'show', 'update', 'destroy']);
    }

    public function index()
    {
        // Check if user has superadmin role (case-insensitive)
        $hasRole = auth()->user()->getRoleNames()->contains(function ($role) {
            return strtolower($role) === 'superadmin';
        });
        abort_unless($hasRole, 403);
        
        $users = User::all();
        $roles = Role::all();
        $permissions = Permission::all();
        
        return view('dashboard.Superadmin.manage-user', compact('users', 'roles', 'permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check if user has superadmin role (case-insensitive)
        $hasRole = auth()->user()->getRoleNames()->contains(function ($role) {
            return strtolower($role) === 'superadmin';
        });
        abort_unless($hasRole, 403);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign role
        $user->assignRole($request->role);

        // Assign permissions
        if ($request->has('permissions')) {
            $user->syncPermissions($request->permissions);
        }

        // Get role from the route
        $role = request()->route('role');
        
        return redirect()->route('users.index', ['role' => $role])
                        ->with('success', "User '{$user->name}' created successfully with role '{$request->role}'.");
    }


    /**
     * Display the specified resource.
     */
    public function show(string $role, $user)
    {
        // Check if user has superadmin role (case-insensitive)
        $hasRole = auth()->user()->getRoleNames()->contains(function ($role) {
            return strtolower($role) === 'superadmin';
        });
        abort_unless($hasRole, 403);

        $userData = User::with('roles', 'permissions')->findOrFail($user);
        $roles = Role::all();
        $permissions = Permission::all();

        return view('dashboard.Superadmin.cline-user', compact('userData', 'roles', 'permissions', 'role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $role, $user)
    {
        // Check if user has superadmin role (case-insensitive)
        $hasRole = auth()->user()->getRoleNames()->contains(function ($role) {
            return strtolower($role) === 'superadmin';
        });
        abort_unless($hasRole, 403);

        $userToUpdate = User::findOrFail($user);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userToUpdate->id,
            'role' => 'required|exists:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        // Update user
        $userToUpdate->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'min:8|confirmed',
            ]);
            $userToUpdate->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // Update role - handle empty role (remove all roles)
        if (empty($request->role)) {
            $userToUpdate->syncRoles([]);
        } else {
            $userToUpdate->syncRoles($request->role);
        }

        // Update permissions
        if ($request->has('permissions')) {
            $userToUpdate->syncPermissions($request->permissions);
        } else {
            $userToUpdate->syncPermissions([]);
        }

        return redirect()->route('users.index', ['role' => $role])
                        ->with('success', "User '{$userToUpdate->name}' updated successfully.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $role, $user)
    {
        // Check if user has superadmin role (case-insensitive)
        $hasRole = auth()->user()->getRoleNames()->contains(function ($role) {
            return strtolower($role) === 'superadmin';
        });
        abort_unless($hasRole, 403);
        
        // Find the user by ID
        $userToDelete = User::findOrFail($user);
        $userName = $userToDelete->name;
        
        $userToDelete->delete();
        
        return redirect()->route('users.index', ['role' => $role])
                        ->with('success', "User '{$userName}' deleted successfully.");
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        // Check if new password was used before
        if ($user->isPasswordUsedBefore($request->new_password)) {
            return back()->withErrors(['new_password' => 'You cannot use a password that you have used before. Please choose a different password.']);
        }

        // Record current password in history before changing
        $user->recordPasswordHistory($user->password);

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password changed successfully.');
    }

    // Old method - replaced with more flexible approach
    // public function viewProfile($user)
    // {
    //     $user = User::findOrFail($user);
    //     $profile = $user->profile;
    //     
    //     return view('dashboard.Superadmin.profile', compact('user', 'profile'));
    // }

    /**
     * Display a user's profile (Superadmin can view any, users can view own)
     */
    public function viewProfile($user)
    {
        $authUser = auth()->user();
        $user = User::findOrFail($user);

        // Check: Superadmin OR viewing own profile
        $isSuperadmin = $authUser->getRoleNames()->contains(function ($role) {
            return strtolower($role) === 'superadmin';
        });
        abort_unless($isSuperadmin || $authUser->id === $user->id, 403);

        $profile = $user->profile;

        return view('dashboard.Superadmin.view-profile', compact('user', 'profile'));
    }

    /**
     * Display the authenticated user's own profile
     */
    public function myProfile()
    {
        $user = auth()->user();
        $profile = $user->profile;

        return view('dashboard.Superadmin.view-profile', compact('user', 'profile'));
    }

    /**
     * Show the form to edit the authenticated user's profile
     */
    public function editProfile()
    {
        $user = auth()->user();
        $profile = $user->profile;

        return view('dashboard.Superadmin.edit-profile', compact('user', 'profile'));
    }

    /**
     * Update the authenticated user's profile
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        // Get or create profile
        $profile = $user->profile;
        if (!$profile) {
            $profile = new Profile(['user_id' => $user->id]);
        }

        $request->validate([
            'bio' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'avatar_file' => 'nullable|image|mimes:jpg,jpeg|max:2048',
        ]);

        // Handle avatar file upload
        if ($request->hasFile('avatar_file')) {
            // Delete old avatar if exists
            if ($profile->avatar && Storage::exists($profile->avatar)) {
                Storage::delete($profile->avatar);
            }
            
            // Store new avatar
            $avatarPath = $request->file('avatar_file')->store('avatars', 'public');
            
            $profile->avatar = asset('storage/' . $avatarPath);
        }

        $profile->fill($request->only(['bio', 'phone', 'address']));
        $profile->save();

        return redirect()->route('profile.view')->with('success', 'Profile updated successfully.');
    }
}
