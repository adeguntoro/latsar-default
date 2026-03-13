@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Request File Details</h5>
                    <div>
                        <a href="{{ route('request-file.edit', $requestFile->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <a href="{{ route('request-file.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Request Information</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th>File Requested</th>
                                    <td>{{ $requestFile->post->title ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Nama Peminta</th>
                                    <td>{{ $requestFile->nama_peminta }}</td>
                                </tr>
                                <tr>
                                    <th>Nomor Telepon</th>
                                    <td>{{ $requestFile->nomor_telepon }}</td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td>{{ $requestFile->alamat_peminta }}</td>
                                </tr>
                                <tr>
                                    <th>Alasan Permintaan</th>
                                    <td>{{ $requestFile->alasan_permintaan }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge bg-primary">Active</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $requestFile->created_at->format('d F Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h6>File Information</h6>
                            @if($requestFile->file_name)
                            <table class="table table-bordered">
                                <tr>
                                    <th>File Name</th>
                                    <td>{{ $requestFile->file_name }}</td>
                                </tr>
                                <tr>
                                    <th>File Type</th>
                                    <td>{{ $requestFile->file_type }}</td>
                                </tr>
                                <tr>
                                    <th>File Size</th>
                                    <td>{{ number_format($requestFile->file_size / 1024, 2) }} KB</td>
                                </tr>
                                <tr>
                                    <th>Uploaded By</th>
                                    <td>{{ $requestFile->user->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Download</th>
                                    <td>
                                        <a href="{{ asset('storage/' . $requestFile->file_path) }}" 
                                           class="btn btn-sm btn-primary" 
                                           target="_blank">
                                            <i class="bi bi-download"></i> Download File
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            @else
                                <p class="text-muted">No file uploaded</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection