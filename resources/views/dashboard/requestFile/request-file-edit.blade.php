@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Request File</h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('request-file.update', $requestFile->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Post Selection -->

                        <div class="form-floating mb-3">
                            <select class="form-select" id="post_id" name="post_id" required>
                                <option value="" disabled>Pilih file yang akan direquest</option>
                                @foreach($data as $item)
                                    <option value="{{ $item->id }}" {{ old('post_id', $requestFile->post_id) == $item->id ? 'selected' : '' }}>
                                        {{ $item->title }} ({{ $item->file_name ?: 'No file' }})
                                    </option>
                                @endforeach
                            </select>
                            <label for="post_id">File yang Diminta</label>
                            @error('post_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Nama Peminta -->
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="nama_peminta" name="nama_peminta" 
                                   value="{{ old('nama_peminta', $requestFile->nama_peminta) }}" placeholder="Nama lengkap" required>
                            <label for="nama_peminta">Nama Peminta</label>
                            @error('nama_peminta')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Nomor Telepon -->
                        <div class="form-floating mb-3">
                            <input type="tel" class="form-control" id="nomor_telepon" name="nomor_telepon" 
                                   value="{{ old('nomor_telepon', $requestFile->nomor_telepon) }}" placeholder="Nomor telepon" required>
                            <label for="nomor_telepon">Nomor Telepon</label>
                            @error('nomor_telepon')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Alamat Peminta -->
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="alamat_peminta" name="alamat_peminta" 
                                      placeholder="Alamat lengkap" style="height: 100px" required>{{ old('alamat_peminta', $requestFile->alamat_peminta) }}</textarea>
                            <label for="alamat_peminta">Alamat Peminta Data</label>
                            @error('alamat_peminta')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Alasan Permintaan -->
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="alasan_permintaan" name="alasan_permintaan" 
                                      placeholder="Alasan permintaan" style="height: 100px" required>{{ old('alasan_permintaan', $requestFile->alasan_permintaan) }}</textarea>
                            <label for="alasan_permintaan">Alasan Permintaan</label>
                            @error('alasan_permintaan')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Current File Information -->
                        @if($requestFile->file_name)
                        <div class="mb-3">
                            <label class="form-label">Current File</label>
                            <div class="alert alert-info">
                                <i class="bi bi-file-earmark-pdf"></i> {{ $requestFile->file_name }}
                                ({{ number_format($requestFile->file_size / 1024, 2) }} KB)
                                <a href="{{ asset('storage/' . $requestFile->file_path) }}" class="btn btn-sm btn-outline-primary ms-2" target="_blank">
                                    View
                                </a>
                            </div>
                        </div>
                        @endif

                        <!-- New File Upload (Optional) -->
                        <div class="mb-3">
                            <label for="file" class="form-label">Upload Surat Pendukung (PDF) - Optional</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".pdf">
                            <small class="text-muted">Leave empty to keep current file. Max 2MB.</small>
                            @error('file')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Update Data</button>
                        <a href="{{ route('request-file.index') }}" class="btn btn-secondary">Cancel</a>
                    </form> 
                </div>
            </div>
        </div>
    </div>
</div>
@endsection