<?php

/**
 * PostController - Handles CRUD operations for posts
 * 
 * CHANGELOG:
 * - 2026-02-19: Changed SESSION_DRIVER from 'database' to 'file' in .env to fix
 *   "PHP Request Startup: file created in the system's temporary directory" error.
 *   The database session driver was causing issues with PHP temp directory configuration.
 * - 2026-03-13: Fixed download and showBySlug methods to use getRoleNames() instead of
 *   pluck('name') to avoid "role not found" errors when roles relationship isn't loaded.
 *   Added $user->load('roles') to force refresh roles and avoid cached permission issues.
 * - 2026-03-13: Fixed update method file upload - was using relative path 'posts' instead
 *   of storage_path('app/public/posts'), causing files to be saved to wrong location.
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

class PostControllerCopy extends Controller
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
    // public function __construct()
    // {
    //     $this->middleware(function ($request, $next) {
    //         $publicMethods = ['showBySlug', 'download'];
            
    //         // Allow public access to showBySlug and download (type-based filtering in SearchController)
    //         if (in_array($request->route()->getActionMethod(), $publicMethods)) {
    //             return $next($request);
    //         }
            
    //         // Require authentication and specific roles for CRUD operations
    //         if (!auth()->check() || !auth()->user()->hasAnyRole(['Superadmin', 'kasubag', 'komisioner', 'staff'])) {
    //             abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
    //         }
    //         return $next($request);
    //     });
    // }

    public function index()
    {
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
        
        // Generate slug based on title (slugified) + unique code for uniqueness
        $data['slug'] = Str::slug($data['title']) . '-' . $this->generateUniqueCode(8);
        
        // Generate unique short_url code (8 characters)
        $data['short_url'] = $this->generateUniqueCode(8);
    
        // Handle file upload
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            
            // ✅ GET FILE INFO FIRST (BEFORE STORING)
            $fileSize = $file->getSize();
            $fileMimeType = $file->getMimeType();
            $fileOriginalName = $file->getClientOriginalName();
            
            // Generate filename with extension
            if ($extension) {
                $filename = time() . '_' . $data['short_url'] . '.' . $extension;
            } else {
                $filename = time() . '_' . $data['short_url'];
            }
            
            // Set file path
            $filePath = 'posts/' . $filename;
            
            // Ensure posts directory exists
            if (!Storage::disk('public')->exists('posts')) {
                Storage::disk('public')->makeDirectory('posts');
            }
            
            // ✅ Use Storage::put() - Laravel-idiomatic approach (same as PostFactory)
            Storage::disk('public')->put($filePath, file_get_contents($file->getRealPath()));
            
            // Set file data
            $data['file_path'] = $filePath;
            $data['file_name'] = $fileOriginalName;
            $data['file_type'] = $fileMimeType;
            $data['file_size'] = $fileSize;
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
            $oldFile = $post->file_path;
            
            $file = $request->file('file');
            $fileMimeType = $file->getMimeType();
            $fileSize = $file->getSize();
            $fileOriginalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = $originalName . '_' . $post->short_url . '.' . $extension;

            // Set new file path
            $filePath = 'posts/' . $filename;
            
            // Ensure posts directory exists
            if (!Storage::disk('public')->exists('posts')) {
                Storage::disk('public')->makeDirectory('posts');
            }
            
            // Delete old file if exists
            if ($oldFile && Storage::disk('public')->exists($oldFile)) {
                Storage::disk('public')->delete($oldFile);
            }
            
            // Use Storage::put() for consistency with PostFactory
            Storage::disk('public')->put($filePath, file_get_contents($file->getRealPath()));

            // Use saved values
            $data['file_path'] = $filePath;
            $data['file_size'] = $fileSize;
            $data['file_type'] = $fileMimeType;
            $data['file_name'] = $fileOriginalName;
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
        // TRIAL VERSION: Remove all role permission checks
        // Allow ANY user (authenticated or guest) to download any file
        // No restrictions based on post type or user role
        
        if ($post->file_path && Storage::disk('public')->exists($post->file_path)) {
            // Increment download count
            $post->increment('downloads_count');
            
            return Storage::disk('public')->download($post->file_path, $post->file_name);
        }
        
        abort(404, 'File not found');
    }

    public function showBySlug($slug, Request $request)
    {
        // Try to find by slug first, then by short_url
        $post = Post::where('slug', $slug)->orWhere('short_url', $slug)->firstOrFail();
        
        // Redirect short_url to slug
        if ($post->short_url === $slug) {
            return redirect(url($post->slug), 301);
        }
        
        // Check if download is requested
        if ($request->query('download')) {
            // TRIAL VERSION: Remove all role permission checks
            // Allow ANY user (authenticated or guest) to download any file
            // No restrictions based on post type or user role
            
            if ($post->file_path && Storage::disk('public')->exists($post->file_path)) {
                // Increment download count
                $post->increment('downloads_count');
                
                return Storage::disk('public')->download($post->file_path, $post->file_name);
            }
            
            abort(404, 'File not found');
        }
        
        // OLD CODE: Role-based access control - commented out for trial
        /*
        // Check if user is authenticated
        if (!auth()->check()) {
            // Guests can only access publik posts
            if ($post->type !== 'publik') {
                abort(403, 'Akses ditolak. Postingan ini bersifat internal.');
            }
        } else {
            // Authenticated users need appropriate role based on post type
            $user = auth()->user();
            $user->load('roles'); // Force refresh roles to avoid cached permission issues
            
            switch ($post->type) {
                case 'publik':
                    // All authenticated users can access publik posts
                    break;
                case 'internal':
                    // Only users with roles: Superadmin, kasubag, komisioner, staff can access internal posts
                    if (!$user->hasAnyRole(['Superadmin', 'kasubag', 'komisioner', 'staff'])) {
                        abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses postingan internal.');
                    }
                    break;
                case 'rahasia':
                    // Only Superadmin can access rahasia posts
                    if (!$user->hasRole('Superadmin')) {
                        abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses postingan rahasia.');
                    }
                    break;
            }
        }
        */
        
        // TRIAL VERSION: Remove all role permission checks
        // Allow ANY user (authenticated or guest) to view ANY post
        // No restrictions based on post type or user role
        
        // Track view count with cookie (3 hours expiration)
        $cookieName = 'post_viewed_' . $post->id;
        
        if (!Cookie::has($cookieName)) {
            // Increment view count
            $post->increment('views_count');
            
            // Set cookie for 3 hours (180 minutes)
            Cookie::queue($cookieName, true, 180);
        }
        
        // return view('posts.slug', compact('post'));
        return response()->json([
            //'post' => $post,
            'url_download' => $post->file_path ? Storage::disk('public')->url($post->file_path) : null,
            //'message' => 'This is a trial version of the showBySlug method. All access control checks have been removed, allowing any user to view any post regardless of type or role. In the full version, access will be restricted based on post type and user permissions.'
        ]);
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