@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Request File - Rahasia</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('request-file.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                        <!-- Nama Peminta -->
                        <div class="form-floating mb-3">
                            <input type="text" disabled class="form-control" id="nama_peminta" name="nama_peminta" 
                                   value="{{ auth()->user()->name }}" required>
                            <label for="nama_peminta">Diajukan oleh</label>
                            @error('nama_peminta')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Post Selection -->
                        <div class="form-floating mb-3">
                            <select class="form-select" id="post_id" name="post_id" required>
                                <option value="" selected disabled>Pilih file yang akan direquest</option>
                                @foreach($data as $item)
                                    <option value="{{ $item->id }}" {{ old('post_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->title }}
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
                                   value="{{ old('nama_peminta') }}" placeholder="Nama lengkap" required>
                            <label for="nama_peminta">Nama Peminta</label>
                            @error('nama_peminta')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Nomor Telepon -->
                        <div class="form-floating mb-3">
                            <input type="tel" class="form-control" id="nomor_telepon" name="nomor_telepon" 
                                   value="{{ old('nomor_telepon') }}" placeholder="Nomor telepon" required>
                            <label for="nomor_telepon">Nomor Telepon</label>
                            @error('nomor_telepon')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Alamat Peminta -->
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="alamat_peminta" name="alamat_peminta" 
                                      placeholder="Alamat lengkap" style="height: 100px" required>{{ old('alamat_peminta') }}</textarea>
                            <label for="alamat_peminta">Alamat Peminta Data</label>
                            @error('alamat_peminta')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Alasan Permintaan -->
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="alasan_permintaan" name="alasan_permintaan" 
                                      placeholder="Alasan permintaan" style="height: 100px" required>{{ old('alasan_permintaan') }}</textarea>
                            <label for="alasan_permintaan">Alasan Permintaan</label>
                            @error('alasan_permintaan')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- File Upload -->
                        <div class="mb-3">
                            <label for="file" class="form-label">Upload Surat Pendukung (PDF)</label>
                            <input type="file" class="form-control" id="file" name="file" required accept=".pdf">
                            @error('file')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Data</button>
                        <a href="{{ route('posts.index') }}" class="btn btn-secondary">Batal</a>
                    </form> 
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')  
    <script>
        // Optional: Add JavaScript for enhanced UX
        document.addEventListener('DOMContentLoaded', function() {
            // You can add custom JS here if needed
        });
    </script>
{{--

</parameter>
</task_progress>
- [x] Update route names in web.php to use request-file instead of posts.request-file
- [x] Update navigation link in app.blade.php
- [x] Update redirect in RequestFileController
- [x] Update form action in request-file.blade.php
- [ ] Verify all changes
</task_progress>
</write_to_file>

--}}