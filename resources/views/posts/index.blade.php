@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Posts</h5>
                    <a href="{{ route('posts.create') }}" class="btn btn-primary btn-sm">Create New Post</a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tablepost">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Slug</th>
                                    <th>Type</th>
                                    <th>Views</th>
                                    <th>Actions</th>
                                    <th>Tanggal Input</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($posts as $post)
                                    <tr>
                                        
                                        <td>{{ $post->title }}</td>
                                        <td>
                                            <code>{{ $post->slug }}</code>
                                            <button class="btn btn-sm btn-secondary ms-2" onclick="copyToClipboard('{{ url($post->short_url) }}')" title="Copy Short URL">
                                                Copy
                                            </button>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $post->type == 'public' ? 'success' : ($post->type == 'internal' ? 'primary' : 'danger') }}">
                                                {{ ucfirst($post->type) }}
                                            </span>
                                        </td>
                                        <td>{{ $post->views_count ?? 0 }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('posts.show', $post->id) }}" type="button" class="btn btn-sm btn-info">View</a>
                                                <a href="{{ route('posts.edit', $post->id) }}" type="button" class="btn btn-sm btn-warning">Edit</a>
                                                <form action="{{ route('posts.destroy', $post->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit button" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </div>


                                        </td>
                                        <td>{{ $post->created_at->format('d F Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
    // Copy to clipboard function with toast
    function copyToClipboard(url) {
        navigator.clipboard.writeText(url).then(() => {
            const toast = new bootstrap.Toast(document.getElementById('copyToast'), { delay: 2000 });
            toast.show();
        }).catch(err => {
            console.error('Failed to copy: ', err);
        });
    }
    
    // wrap in DOMContentLoaded to ensure app.js finished loading
    document.addEventListener('DOMContentLoaded', () => {
        window.initDataTable('#tablepost', { 
            pageLength: 5, 
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            order: [[0,'desc']]  // Sort by Created At (first column) descending (newest first)
        });
    });
</script>
@endsection

