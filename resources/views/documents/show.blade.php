@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Document Details</span>
                    <a href="{{ route('documents.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                </div>

                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th width="200">Title:</th>
                            <td>{{ $document->title }}</td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>{{ $document->description ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>File Name:</th>
                            <td>{{ $document->file_name }}</td>
                        </tr>
                        <tr>
                            <th>File Type:</th>
                            <td>{{ $document->file_type }}</td>
                        </tr>
                        <tr>
                            <th>File Size:</th>
                            <td>{{ number_format($document->file_size / 1024, 2) }} KB</td>
                        </tr>
                        <tr>
                            <th>Uploaded:</th>
                            <td>{{ $document->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    </table>

                    <div class="mt-3">
                        <a href="{{ route('documents.download', $document) }}" class="btn btn-success">Download File</a>
                        <a href="{{ route('documents.edit', $document) }}" class="btn btn-primary">Edit</a>
                        <form action="{{ route('documents.destroy', $document) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this document?')">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
