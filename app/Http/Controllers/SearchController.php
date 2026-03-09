<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\URL;

class SearchController extends Controller
{
    //
    public function index()
    {
        return view('search');
    }
    public function results(Request $request)
    {
        $query = $request->get('q');
        $department = $request->get('department');
        $sort = $request->get('sort', 'newest');
        
        // ORIGINAL QUERY (before fix):
        // $posts = Post::where('title', 'like', "%{$query}%")
        //     ->orWhere('content', 'like', "%{$query}%")
        //     ->orWhere('file_name', 'like', "%{$query}%")
        //     ->orWhere('department', 'like', "%{$query}%");
        // Problem: The type filter was added after orWhere clauses, causing it to be
        // combined with the last orWhere, making the access control ineffective.
        // Fixed by grouping all search conditions in a closure.
        
        // Build base query with search conditions grouped together
        $posts = Post::where(function($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhere('content', 'like', "%{$query}%")
              ->orWhere('file_name', 'like', "%{$query}%")
              ->orWhere('department', 'like', "%{$query}%");
        });
        
        // Apply access control based on post type and user role
        if (auth()->check()) {
            $user = auth()->user();
            $userRoles = $user->roles->pluck('name')->toArray();
            
            // Check if user has kasubag or komisioner role (can see all types)
            $isPrivileged = in_array('kasubag', $userRoles) || in_array('komisioner', $userRoles) || in_array('Superadmin', $userRoles);
            
            if (!$isPrivileged) {
                // Regular authenticated users: only publik and internal
                $posts->whereIn('type', ['publik', 'internal']);
            }
            // Privileged users (kasubag, komisioner, Superadmin) can see all types (no additional filter)

        } else {
            // Guests can only see publik types
            // $posts->where('type', 'publik');

            // Guests can see publik and internal types
            // $posts->whereIn('type', ['publik', 'internal']);
            $posts->whereIn('type', ['publik']);
        }
        
        // Apply department filter if selected
        if ($department) {
            $posts->where('department', 'like', "%{$department}%");
        }
        
        // Apply sorting by created_at
        if ($sort === 'oldest') {
            $posts->orderBy('created_at', 'asc');
        } else {
            $posts->orderBy('created_at', 'desc'); // newest by default
        }
        
        $posts = $posts->paginate(10);
        
        return view('result', compact('posts', 'query'));
    }
}
