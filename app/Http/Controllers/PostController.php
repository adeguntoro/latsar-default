<?php

/**
 * PostController - Handles CRUD operations for posts
 * 
 * CHANGELOG:
 * - 2026-02-19: Changed SESSION_DRIVER from 'database' to 'file' in .env to fix
 *   "PHP Request Startup: file created in the system's temporary directory" error.
 *   The database session driver was causing issues with PHP temp directory configuration.
 * 
 * @author Cline
 * @date 2026-02-19
 */

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Constructor to apply role-based access control
     * ORIGINAL: Only users with roles: Superadmin, kasubag, komisioner, staff can access any method
     * This blocked all guests from viewing any posts, including publik ones.
     * 
     * UPDATED: CRUD methods require authentication + specific roles, but public methods
     * (showBySlug, download) are accessible to guests - access control based on post type
     * is handled in SearchController and the view layer.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $publicMethods = ['showBySlug', 'download'];
            
            // Allow public access to showBySlug and download (type-based filtering in SearchController)
            if (in_array($request->route()->getActionMethod(), $publicMethods)) {
                return $next($request);
            }
            
            // Require authentication and specific roles for CRUD operations
            if (!auth()->check() || !auth()->user()->hasAnyRole(['Superadmin', 'kasubag', 'komisioner', 'staff'])) {
                abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        // OLD: Get all posts without ordering
        // $posts = Post::all();
        
        // NEW: Get all posts sorted by newest first (created_at DESC)
        // DataTables handles pagination, sorting, and searching on frontend
        $posts = Post::latest()->get();
        
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'content' => 'required',
            'department' => 'required',
            'type' => 'required|in:publik,internal,rahasia',
            'file' => 'nullable|file',
        ]);

        // Check if user has permission to create confidential (rahasia) posts
        if ($validated['type'] === 'rahasia' && !$request->user()->can('create confidential files')) {
            abort(403, 'Anda tidak memiliki izin untuk membuat postingan rahasia.');
        }

        $data = $validated;
        $data['department'] = $request->input('department');
        
        // OLD: Generate random slug code
        // $data['slug'] = $this->generateUniqueCode(5);
        
        // NEW: Generate slug based on title (slugified) + unique code for uniqueness
        $data['slug'] = Str::slug($data['title']) . '-' . $this->generateUniqueCode(8);
        
        // Generate unique short_url code (8 characters)
        $data['short_url'] = $this->generateUniqueCode(8);

        // Handle file upload
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            
            // Generate filename with extension
            if ($extension) {
                $filename = time() . '_' . $data['short_url'] . '.' . $extension;
            } else {
                $filename = time() . '_' . $data['short_url'];
            }
            
            $path = $file->storeAs('posts', $filename, 'public');
            
            if ($path) {
                $data['file_path'] = $path;
                $data['file_name'] = $file->getClientOriginalName();
                $data['file_type'] = $file->getClientMimeType();
                $data['file_size'] = $file->getSize();
            }
        }

        Post::create($data);

        return redirect()->route('posts.index');
    }

    public function show(Post $post)
    {
        // Track view count with cookie (3 hours expiration)
        /*
        $cookieName = 'post_viewed_' . $post->id;
        
        if (!Cookie::has($cookieName)) {
            // Increment view count
            $post->increment('views_count');
            
            // Set cookie for 3 hours (180 minutes)
            Cookie::queue($cookieName, true, 180);
        }
        */
        
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required',
            'content' => 'required',
            'type' => 'required|in:publik,internal,rahasia',
            'file' => 'nullable|file',
        ]);

        // Check if user has permission to edit confidential (rahasia) posts
        if ($validated['type'] === 'rahasia' && !$request->user()->can('create confidential files')) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit postingan rahasia.');
        }

        $data = $validated;
        $data['department'] = $request->input('department');

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file
            if ($post->file_path) {
                Storage::disk('public')->delete($post->file_path);
            }

            $file = $request->file('file');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $filename = $originalName . '_' . $post->short_url . '.' . $extension;
            $path = $file->storeAs('posts', $filename, 'public');
            $data['file_path'] = $path;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_type'] = $file->getMimeType();
            $data['file_size'] = $file->getSize();
        }

        $post->update($data);
        $post->searchable(); // Re-index for search

        return redirect()->route('posts.index');
    }

    public function destroy(Post $post)
    {
        // Delete file
        if ($post->file_path && !empty($post->file_path)) {
            Storage::disk('public')->delete($post->file_path);
        }

        $post->unsearchable(); // Remove from search index
        $post->delete();

        return redirect()->route('posts.index');
    }

    public function download(Post $post)
    {
        // Apply access control based on post type and user role
        if (auth()->check()) {
            $user = auth()->user();
            $userRoles = $user->roles->pluck('name')->toArray();
            
            // Check if user has kasubag, komisioner, or Superadmin role (can see all types)
            $isPrivileged = in_array('kasubag', $userRoles) || in_array('komisioner', $userRoles) || in_array('Superadmin', $userRoles);
            
            if (!$isPrivileged && $post->type === 'rahasia') {
                abort(403, 'Anda tidak memiliki izin untuk mengunduh postingan rahasia.');
            }
        } else {
            // Guests can only download publik posts
            if ($post->type !== 'publik') {
                abort(403, 'Postingan ini bersifat internal/rahasia. Silakan login untuk mengakses.');
            }
        }
        
        if ($post->file_path) {
            // Increment download count
            $post->increment('downloads_count');
            
            return Storage::disk('public')->download($post->file_path, $post->file_name);
        }
        
        abort(404);
    }

    public function showBySlug($slug)
    {
        // Try to find by slug first, then by short_url
        $post = Post::where('slug', $slug)->orWhere('short_url', $slug)->firstOrFail();
        
        // Redirect short_url to slug
        if ($post->short_url === $slug) {
            return redirect(url($post->slug), 301);
        }
        
        // Apply access control based on post type and user role
        if (auth()->check()) {
            $user = auth()->user();
            $userRoles = $user->roles->pluck('name')->toArray();
            
            // Check if user has kasubag, komisioner, or Superadmin role (can see all types)
            $isPrivileged = in_array('kasubag', $userRoles) || in_array('komisioner', $userRoles) || in_array('Superadmin', $userRoles);
            
            if (!$isPrivileged && $post->type === 'rahasia') {
                abort(403, 'Anda tidak memiliki izin untuk mengakses postingan rahasia.');
            }
        } else {
            // Guests can only see publik posts
            if ($post->type !== 'publik') {
                abort(403, 'Postingan ini bersifat internal/rahasia. Silakan login untuk mengakses.');
            }
        }
        
        // Track view count with cookie (3 hours expiration)
        $cookieName = 'post_viewed_' . $post->id;
        
        if (!Cookie::has($cookieName)) {
            // Increment view count
            $post->increment('views_count');
            
            // Set cookie for 3 hours (180 minutes)
            Cookie::queue($cookieName, true, 180);
        }
        
        return view('posts.slug', compact('post'));
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        $department = $request->input('department');
        $posts = collect([]);
        
        if ($query) {
            // Old search without department filter
            // $posts = Post::search($query)->paginate(10);
            
            // Search using Laravel Scout/TNTSearch with department filter
            if ($department) {
                $posts = Post::search($query)
                    ->query(fn ($builder) => $builder->where('department', $department))
                    ->paginate(10);
            } else {
                $posts = Post::search($query)->paginate(10);
            }
        }
        return view('search', compact('posts', 'query'));
    }

    /**
     * Generate unique code for slug/short_url (safe for concurrent users)
     * Uses timestamp component + random to minimize collision chance
     */
    private function generateUniqueCode(int $length): string
    {
        // Mix of timestamp (base36) + random characters for uniqueness
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        
        // Add microsecond component for extra uniqueness
        $micro = substr(str_replace('.', '', microtime(true)), -4);
        $code .= strtoupper(base_convert($micro, 10, 36));
        
        // Fill rest with random
        while (strlen($code) < $length) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        return substr($code, 0, $length);
    }
}