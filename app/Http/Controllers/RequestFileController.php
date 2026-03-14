<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\RequestFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RequestFileController extends Controller
{
    /**
     * Constructor to apply role-based access control
     * Only users with roles: Superadmin, kasubag, komisioner can access request file functionality
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || !auth()->user()->hasAnyRole(['Superadmin', 'kasubag', 'komisioner'])) {
                abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //$requestFiles = RequestFile::with(['post', 'user'])
        // don't change

        $posts = Post::where('type', 'rahasia')->get();

        if ($posts->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No data found'
            ], 404);
        }

        // return response()->json([
        //     'status' => 'success',
        //     'data' => $posts
        // ]);
    
        return view('dashboard.requestFile.index');//, compact('posts')); //, compact('requestFiles'));
    }

    /**
     * Show the form to request a confidential file.
     */
    public function create()
    {
        $data = Post::where('type', 'rahasia')->get();
        return view('dashboard.requestFile.request-file', compact('data'));
    }

    /**
     * Store a newly created request file.
     */
    public function store(Request $request)
    {
        // return response()->json([
        //     'status' => 'success',
        //     'message' => $request->all(),
        // ]);

        ///*
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'nama_peminta' => 'required|string|max:255',
            'nomor_telepon' => 'required|string|max:20',
            'alamat_peminta' => 'required|string',
            'alasan_permintaan' => 'required|string',
            'file' => 'required|file|mimes:pdf|max:5000', // Max 5MB
        ]);

        // Storage::disk('public')->makeDirectory('xyz', 0755, true);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $filename = time() . '_' . Str::random(10) . '.' . $extension;
        $filePath = $file->storeAs('request_files', $filename, 'public');

        // Save to database
        RequestFile::create([
            'post_id' => $validated['post_id'],
            'nama_peminta' => $validated['nama_peminta'],
            'nomor_telepon' => $validated['nomor_telepon'],
            'alamat_peminta' => $validated['alamat_peminta'],
            'alasan_permintaan' => $validated['alasan_permintaan'],
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'user_served' => auth()->id(),
        ]);
        
        return redirect()->route('request-file.index')->with('success', 'Permintaan file berhasil dikirim.');
        
    }

    /**
     * Display the specified resource.
     */
    public function show(RequestFile $requestFile)
    {
        $requestFile->load(['post', 'user']);
        
        return view('dashboard.requestFile.request-file-show', compact('requestFile'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RequestFile $requestFile)
    {
        $requestFile->load(['post']);
        $data = Post::where('type', 'rahasia')->get();
        
        return view('dashboard.requestFile.request-file-edit', compact('requestFile', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RequestFile $requestFile)
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'nama_peminta' => 'required|string|max:255',
            'nomor_telepon' => 'required|string|max:20',
            'alamat_peminta' => 'required|string',
            'alasan_permintaan' => 'required|string',
            'file' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        // Handle file upload if new file is provided
        $filePath = $requestFile->file_path;
        $fileName = $requestFile->file_name;
        $fileType = $requestFile->file_type;
        $fileSize = $requestFile->file_size;

        $folderPath = 'request_files';
        $disk = 'public';


        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            // Delete old file if exists
            if (!Storage::disk($disk)->exists($folderPath)) {
                Storage::disk($disk)->makeDirectory($folderPath, 0755, true);
            }
            // Storage::disk('public')->makeDirectory('xyz', 0755, true);
            
            if (!empty($requestFile->file_path)) {
                Storage::disk('public')->delete($requestFile->file_path);
            }

            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '_' . Str::random(10) . '.' . $extension;
            $path = $file->storeAs('request_files', $filename, 'public');
            
            if ($path) {
                $filePath = $path;
                $fileName = $file->getClientOriginalName();
                $fileType = $file->getMimeType();
                $fileSize = $file->getSize();
            } else {
                return back()->withErrors(['file' => 'Failed to upload file. Please try again.'])->withInput();
            }
        }

        // Update request file record
        $requestFile->update([
            'post_id' => $validated['post_id'],
            'nama_peminta' => $validated['nama_peminta'],
            'nomor_telepon' => $validated['nomor_telepon'],
            'alamat_peminta' => $validated['alamat_peminta'],
            'alasan_permintaan' => $validated['alasan_permintaan'],
            'file_path' => $filePath,
            // 'file_name' => $fileName,
            // 'file_type' => $fileType,
            // 'file_size' => $fileSize,
        ]);

        return redirect()->route('request-file.index')
            ->with('success', 'Permintaan file berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(RequestFile $requestFile)
    {
        // Delete associated file if exists and path is not empty
        if (!empty($requestFile->file_path)) {
            Storage::disk('public')->delete($requestFile->file_path);
        }

        $requestFile->delete(); // Soft delete

        return redirect()->route('request-file.index')
            ->with('success', 'Permintaan file berhasil dihapus.');
    }
}