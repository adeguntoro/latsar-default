<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($role = null)
    {
        // Role-based access control
        if ($role) {
            abort_unless(auth()->user()->hasRole($role), 403);
        }

        // Get statistics for posts by status
        $postsByStatus = Post::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Get statistics for posts by department
        $postsByDepartment = Post::selectRaw('department, COUNT(*) as count')
            ->whereNotNull('department')
            ->groupBy('department')
            ->pluck('count', 'department')
            ->toArray();

        // Get statistics for posts by type [rahasia, terbuka, internal]
        $postsByType = Post::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        // Get statistics for users by role
        $usersByRole = User::selectRaw('roles.name as role, COUNT(users.id) as count')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->groupBy('roles.name')
            ->pluck('count', 'role')
            ->toArray();

        // Get total counts
        $totalPosts = Post::count();
        $totalUsers = User::count();

        return view('dashboard.index', compact(
            'postsByStatus',
            'postsByDepartment',
            'postsByType', //base on type [rahasia, terbuka, internal]
            'usersByRole',
            'totalPosts',
            'totalUsers'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}