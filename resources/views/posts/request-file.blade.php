@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Request file</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf


                        <div class="form-floating mb-3">
                            <select class="form-select searchable" id="floatingSelectGrid">
                                <option selected>Open this select menu</option>
                                @foreach($data as $item)
                                <option value="{{ $item->id }}">{{ $item->title }}</option>
                                @endforeach
                            </select>
                        </select>
                        <label for="floatingSelectGrid">Silahkan Pilih filenya</label>
                    </div>
                    
                    <div class="row g-2">
                        <div class="col-md">
                            <div class="form-floating">
                            <input type="email" class="form-control" id="floatingInputGrid" placeholder="" value="{{ old('nama_peminta') }}" name="nama_peminta" required>
                            <label for="floatingInputGrid">Nama Peminta</label>
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="form-floating">
                                <input type="number" class="form-control" id="floatingInputGrid" placeholder="" value="{{ old('nomor_telepon') }}" name="nomor_telepon" required>
                                <label for="floatingInputGrid">Nomor Telepon</label>
                            </div>

                        </div>
                    </div>

                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih file surat pendukung</label>
                            <input type="file" class="form-control" id="file" name="file" required accept=".pdf"> 
                           
                        </div>


                        <div class="form-floating mb-3">
                            <textarea class="form-control" placeholder="Leave a comment here" id="floatingTextarea2" style="height: 100px" value="{{ old('alamat_peminta') }}" name="alamat_peminta" required></textarea>
                            <label for="floatingTextarea2">Alamat Peminta Data</label>
                        </div>
                        
                        <div class="form-floating mb-3">
                            <textarea class="form-control" placeholder="Leave a comment here" id="floatingTextarea2" style="height: 100px" value="{{ old('alasan_permintaan') }}" name="alasan_permintaan" required></textarea>
                            <label for="floatingTextarea2">Alasan permintaan</label>
                        </div>

                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form> 
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')  
    <script>
        // Tambahkan JavaScript jika diperlukan
    </script>   