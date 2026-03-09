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
    public function index()
    {
        $posts = Post::all();
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'type' => 'required',
            'file' => 'nullable|file',
        ]);

        $data = $request->only(['title', 'content', 'type', 'department']);

        // Old approach - not safe for concurrent users
        // do {
        //     $data['slug'] = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 5));
        // } while (Post::where('slug', $data['slug'])->exists());
        // do {
        //     $data['short_url'] = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8));
        // } while (Post::where('short_url', $data['short_url'])->exists());

        // Generate unique slug and short_url (safe for concurrent users)
        // Uses timestamp + random to minimize collision, with retry on duplicate
        //$data['slug'] = $this->generateUniqueCode(5); Str::
        //$data['slug'] = Str::of($data->title)->slug('-');
        $slug = Str::slug($request->title);

        // make it unique by appending short unique id
        $uniqueSlug = $slug . '-' . Str::random(6);
        $data['slug'] = $uniqueSlug;   
        
        $data['short_url'] = $this->generateUniqueCode(8);

        // Handle file upload
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $filename = $originalName . '_' . $data['short_url'] . '.' . $extension;
            $path = $file->storeAs('posts', $filename, 'public');
            
            if ($path === false) {
                return back()->withErrors(['file' => 'Failed to upload file. Please try again.'])->withInput();
            }
            
            $data['file_path'] = $path;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_type'] = $file->getClientMimeType();
            $data['file_size'] = $file->getSize();
        } else {
            // Explicitly set to null when no file is uploaded
            $data['file_path'] = null;
            $data['file_name'] = null;
            $data['file_type'] = null;
            $data['file_size'] = null;
        }

        // Old approach without retry
        // $post = Post::create($data);
        // $post->searchable();

        // Create post with retry on duplicate key (safe for concurrent users)
        $maxRetries = 5;
        $post = null;
        
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $post = DB::transaction(function () use ($data) {
                    return Post::create($data);
                });
                break; // Success, exit loop
            } catch (\Illuminate\Database\QueryException $e) {
                // Check if it's a duplicate key error (MySQL: 1062, PostgreSQL: 23505)
                if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'Duplicate entry')) {
                    // Regenerate codes and retry
                    $data['slug'] = $this->generateUniqueCode(5);
                    $data['short_url'] = $this->generateUniqueCode(8);
                    // Update filename with new short_url
                    if (isset($data['file_path'])) {
                        $originalName = pathinfo($data['file_name'], PATHINFO_FILENAME);
                        $extension = pathinfo($data['file_name'], PATHINFO_EXTENSION);
                        $newFilename = $originalName . '_' . $data['short_url'] . '.' . $extension;
                        // Rename file
                        Storage::disk('public')->move($data['file_path'], 'posts/' . $newFilename);
                        $data['file_path'] = 'posts/' . $newFilename;
                    }
                    if ($attempt == $maxRetries) {
                        throw $e; // Max retries reached
                    }
                } else {
                    throw $e; // Other database error
                }
            }
        }

        $post->searchable(); // Index for search

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
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'type' => 'required',
            'file' => 'nullable|file',
        ]);

        $data = $request->only(['title', 'content', 'type', 'department']);

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
            $data['file_type'] = $file->getClientMimeType();
            $data['file_size'] = $file->getSize();
        }

        $post->update($data);
        $post->searchable(); // Re-index for search

        return redirect()->route('posts.index');
    }

    public function destroy(Post $post)
    {
        // Delete file
        if ($post->file_path) {
            Storage::disk('public')->delete($post->file_path);
        }

        $post->unsearchable(); // Remove from search index
        $post->delete();

        return redirect()->route('posts.index');
    }

    public function download(Post $post)
    {
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
