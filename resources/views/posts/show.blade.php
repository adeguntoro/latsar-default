@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">View Post</h5>
                    <div>
                        <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <a href="{{ route('posts.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <h2 class="mb-3">{{ $post->title }}</h2>
                            
                            @if($post->is_featured)
                                <span class="badge bg-info mb-3">Featured</span>
                            @endif

                            @if($post->excerpt)
                                <div class="alert alert-light border">
                                    <strong>Excerpt:</strong>
                                    <p class="mb-0">{{ $post->excerpt }}</p>
                                </div>
                            @endif

                            <div class="content mt-4">
                                <h5>Content</h5>
                                <div class="border-start border-3 border-primary ps-3">
                                    {!! nl2br(e($post->content)) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title border-bottom pb-2 mb-3">Post Information</h6>
                                    
                                    <div class="mb-3">
                                        <strong class="d-block text-muted small">Slug</strong>
                                        <span>{{ $post->slug }}</span>
                                    </div>

                                    <div class="mb-3">
                                        <strong class="d-block text-muted small">Short URL</strong>
                                        <div class="d-flex align-items-center gap-2">
                                            <code>{{ $post->short_url }}</code>
                                            <button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard('{{ url($post->short_url) }}')" title="Copy Short URL">
                                                <i class="bi bi-clipboard"></i> Copy
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <strong class="d-block text-muted small">Type</strong>
                                        <span class="badge bg-secondary">{{ $post->type ?? 'N/A' }}</span>
                                    </div>

                                    <div class="mb-3">
                                        <strong class="d-block text-muted small">Status</strong>
                                        <span class="badge bg-{{ $post->status == 'published' ? 'success' : ($post->status == 'draft' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($post->status ?? 'N/A') }}
                                        </span>
                                    </div>

                                    <div class="mb-3">
                                        <strong class="d-block text-muted small">Published At</strong>
                                        <span>{{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->format('F d, Y H:i') : 'Not published' }}</span>
                                    </div>

                                    <div class="mb-3">
                                        <strong class="d-block text-muted small">Created At</strong>
                                        <span>{{ $post->created_at->format('F d, Y H:i') }}</span>
                                    </div>

                                    <div class="mb-3">
                                        <strong class="d-block text-muted small">Last Updated</strong>
                                        <span>{{ $post->updated_at->format('F d, Y H:i') }}</span>
                                    </div>

                                    <hr>

                                    <h6 class="border-bottom pb-2 mb-3">Statistics</h6>
                                    
                                    <div class="mb-2">
                                        <strong class="d-block text-muted small">Views</strong>
                                        <span class="badge bg-primary">{{ $post->views_count ?? 0 }}</span>
                                    </div>

                                    <div class="mb-3">
                                        <strong class="d-block text-muted small">Downloads</strong>
                                        <span class="badge bg-success">{{ $post->downloads_count ?? 0 }}</span>
                                    </div>

                                    @if($post->file_name)
                                        <hr>
                                        <h6 class="border-bottom pb-2 mb-3">Attached File</h6>
                                        
                                        <div class="alert alert-info py-2 px-3 small mb-2">
                                            <strong class="d-block">{{ $post->file_name }}</strong>
                                            <div class="mt-1">
                                                <span class="d-block">Type: {{ $post->file_type }}</span>
                                                <span class="d-block">Size: {{ number_format($post->file_size / 1024, 2) }} KB</span>
                                            </div>
                                        </div>

                                        @if(!empty($post->file_path))
                                            <a href="{{ Storage::url($post->file_path) }}" 
                                               class="btn btn-sm btn-success w-100" 
                                               download>
                                                <i class="bi bi-download"></i> Download File
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mt-4">
                        <div>
                            <a href="{{ route('posts.index') }}" class="btn btn-secondary w-100 w-md-auto">Back to List</a>
                        </div>
                        <div class="d-flex flex-column flex-md-row gap-2">
                            <a href="{{ !empty($post->file_path) ? Storage::url($post->file_path) : '#' }}" 
                               class="btn btn-success w-100 w-md-auto" 
                               {{ !empty($post->file_path) ? 'download' : '' }}>
                                Download File
                            </a>
                            <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-warning w-100 w-md-auto">Edit Post</a>
                            <form action="{{ route('posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100 w-md-auto">Delete Post</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast for copy notification -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="copyToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                URL copied to clipboard!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function copyToClipboard(url) {
        navigator.clipboard.writeText(url).then(() => {
            const toast = new bootstrap.Toast(document.getElementById('copyToast'), { delay: 2000 });
            toast.show();
        }).catch(err => {
            console.error('Failed to copy: ', err);
        });
    }
</script>
@endsection
