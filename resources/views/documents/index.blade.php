@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Documents</span>
                    <a href="{{ route('documents.create') }}" class="btn btn-primary btn-sm">Upload New Document</a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($documents->count() > 0)
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>File Name</th>
                                    <th>File Size</th>
                                    <th>Uploaded</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documents as $document)
                                    <tr>
                                        <td>{{ $document->id }}</td>
                                        <td>{{ $document->title }}</td>
                                        <td>{{ Str::limit($document->description, 50) }}</td>
                                        <td>{{ $document->file_name }}</td>
                                        <td>{{ number_format($document->file_size / 1024, 2) }} KB</td>
                                        <td>{{ $document->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('documents.download', $document) }}" class="btn btn-sm btn-success">Download</a>
                                            <a href="{{ route('documents.show', $document) }}" class="btn btn-sm btn-info">View</a>
                                            <form action="{{ route('documents.destroy', $document) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-center">No documents uploaded yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
