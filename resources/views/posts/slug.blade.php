@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $post->title }}</h5>
                    <div>
                        <span class="badge bg-{{ $post->type == 'public' ? 'success' : ($post->type == 'internal' ? 'primary' : 'danger') }}">
                            {{ ucfirst($post->type) }}
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <small class="text-muted">Slug: <code>{{ $post->slug }}</code></small>
                                <span class="mx-2">|</span>
                                <small class="text-muted">Short URL: <code>{{ $post->short_url }}</code></small>
                                <button class="btn btn-sm btn-outline-primary ms-1" onclick="copyToClipboard('{{ url($post->short_url) }}')" title="Copy Short URL">
                                    <i class="bi bi-clipboard"></i> Copy
                                </button>
                            </div>
                            <div>
                                <small class="text-muted">Views: {{ $post->views_count ?? 0 }}</small>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="content">
                            {!! nl2br(e($post->content)) !!}
                        </div>
                    </div>

                    @if($post->file_name)
                        <div class="alert alert-info">
                            <h6 class="alert-heading">Attached File</h6>
                            <p class="mb-2">
                                <strong>{{ $post->file_name }}</strong><br>
                                <small>Type: {{ $post->file_type }} | Size: {{ number_format($post->file_size / 1024, 2) }} KB</small>
                            </p>
                        </div>
                    @endif

                    <hr>

                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                        <div>
                            <small class="text-muted">
                                Created: {{ $post->created_at->format('F d, Y H:i') }}
                            </small>
                        </div>
                        <div class="d-flex flex-column flex-md-row gap-2 w-100 w-md-auto">
                            <a href="{{ !empty($post->file_path) ? Storage::url($post->file_path) : '#' }}" 
                               class="btn btn-success btn-sm w-100 w-md-auto" 
                               {{ !empty($post->file_path) ? 'download' : '' }}>
                                Download File
                            </a>
                            <a href="{{ route('posts.index') }}" class="btn btn-secondary btn-sm w-100 w-md-auto">Back to List</a>
                        </div>
                    </div>
                        {{--
                        <div>
                            <a href="{{ !empty($post->file_path) ? Storage::url($post->file_path) : '#' }}" 
                               class="btn btn-success btn-sm {{ empty($post->file_path) ? 'disabled' : '' }}" 
                               {{ !empty($post->file_path) ? 'download' : '' }}>
                                Download File
                            </a>
                            <a href="{{ route('posts.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                        </div>
                        --}}

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

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
