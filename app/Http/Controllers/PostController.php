<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostController extends Controller
{

    public function __construct()
    {
        // Apply auth middleware to all methods except showBySlug and download
     
    }
    public function index()
    {
        // $posts = Post::latest()->get();
        
        // return view('posts.index', compact('posts'));

        // $query = Post::query();

        // Hide rahasia posts from non-authorized users
        // if (!auth()->check() || !auth()->user()->hasRole(['kasubag', 'komisioner'])) {
        //     $query->where('type', '!=', 'rahasia');
        // }

        // $posts = $query->all();
        // return view('posts.index', compact('posts'));

        
        if (!auth()->check() || !auth()->user()->hasRole(['kasubag', 'komisioner'])) {
            $posts = Post::where('type', '!=', 'rahasia')->get();
        } else {
            $posts = Post::all();
        }
        
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
            'file' => 'nullable|file|max:10240', // Added max file size (10MB)
        ]);
    
        $post = new Post($validated);

            $slug = Str::slug($validated['title']) . '-' . Str::random(8);
            $shortUrl = Str::random(8);
        
            $filePath = null;
            $fileName = null;
        
            // Handle file upload
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $extension = $file->getClientOriginalExtension();

                $fileName = $file->getClientOriginalName();
                $fileType = $file->getMimeType();
                $fileSize = $file->getSize();
                
                // Generate filename with random string
                $filename = time() . '_' . Str::random(8) . '.' . $extension;
                
                // Create posts folder if not exists
                $destinationPath = storage_path('app/public/posts');
                if (!is_dir($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                
                // Move file
                $file->move($destinationPath, $filename);
                
                // Save path and original filename
                $filePath = 'posts/' . $filename;
                $fileName = $file->getClientOriginalName();
            }
        
            Post::create([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'department' => $validated['department'],
                'type' => $validated['type'],
                'slug' => $slug,
                'short_url' => $shortUrl,
                'file_path' => $filePath,
                'file_name' => $fileName,
                'file_type' => $fileType,
                'file_size' => $fileSize,
                'status' => 'published',
            ]);

            //for indexing you can try :
            // $post = Post::create([...]);
            // $post->indexSearchable(); // or ->index()

        return redirect()->route('posts.index')->with('success', 'Post created successfully!');
    }

    public function show(Post $post)
    {
        // $post = Post::all();

        // return view('posts.show', compact('post'));

        if (!auth()->check()) {
            abort(403, 'Please login to access this post');
        }
    
        // If post type is rahasia, only kasubag and komisioner can access
        if ($post->type === 'rahasia' && !auth()->user()->hasRole(['kasubag', 'komisioner'])) {
            abort(403, 'You do not have permission to access this post');
        }
    
        // internal and publik - all authenticated users can access
    
        $post->increment('views_count');
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

        // Handle file upload if new file provided
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file');
            
            $fileName = $file->getClientOriginalName();
            $fileType = $file->getMimeType();
            $fileSize = $file->getSize();
            $extension = $file->getClientOriginalExtension();
            
            // Generate new filename with random string (don't delete old file)
            $filename = time() . '_' . Str::random(8) . '.' . $extension;
            
            // Create posts folder if not exists
            $destinationPath = storage_path('app/public/posts');
            if (!is_dir($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            // Move new file (don't delete old file)
            $file->move($destinationPath, $filename);
            
            // Set new file path
            $filePath = 'posts/' . $filename;
            
            // Update file fields
            $validated['file_path'] = $filePath;
            $validated['file_name'] = $fileName;
            $validated['file_type'] = $fileType;
            $validated['file_size'] = $fileSize;
        }

        // Update post
        $post->update($validated);
        $post->searchable(); // Re-index for search

        return redirect()->route('posts.index')->with('success', 'Post updated successfully!');
    }

    public function destroy(Post $post)
    {
        // Delete file
        if ($post->file_path && !empty($post->file_path)) {
            Storage::disk('public')->delete($post->file_path);
        }

        $post->unsearchable(); // Remove from search index
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully!');
    }

    public function download(Post $post)
    {
        if ($post->file_path && Storage::disk('public')->exists($post->file_path)) {
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
        
        // Check access based on post type
        if ($post->type === 'rahasia') {
            // Only kasubag and komisioner can access
            if (!auth()->check() || !auth()->user()->hasRole(['kasubag', 'komisioner'])) {
                abort(403, 'You do not have permission to access this post');
            }
        } elseif ($post->type === 'internal') {
            // Only authenticated users can access
            if (!auth()->check()) {
                abort(403, 'Please login to access this post');
            }
        }
        // type === 'publik' - everyone can access
        
        // Check if download is requested
        if ($request->query('download')) {
            // Verify user can download based on post type
            if ($post->type === 'rahasia' && (!auth()->check() || !auth()->user()->hasRole(['kasubag', 'komisioner']))) {
                abort(403, 'You cannot download this file');
            }
        
            if ($post->type === 'internal' && !auth()->check()) {
                abort(403, 'Please login to download this file');
            }
        
            if ($post->file_path && Storage::disk('public')->exists($post->file_path)) {
                $post->increment('downloads_count');
                return Storage::disk('public')->download($post->file_path, $post->file_name);
            }
        
            abort(404, 'File not found');
        }
        
        // Track view count with cookie (3 hours expiration)
        $cookieName = 'post_viewed_' . $post->id;
        
        if (!Cookie::has($cookieName)) {
            $post->increment('views_count');
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
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        
        $micro = substr(str_replace('.', '', microtime(true)), -4);
        $code .= strtoupper(base_convert($micro, 10, 36));
        
        while (strlen($code) < $length) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        return substr($code, 0, $length);
    }
}