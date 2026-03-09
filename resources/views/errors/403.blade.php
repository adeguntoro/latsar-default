@extends('layouts.app')

@section('content')
<div class="container text-center mt-5">
    <div class="card shadow p-5">
        <h1 class="text-danger">403</h1>
        <h4 class="mb-3">Access Denied</h4>
        <p class="text-muted">
            You do not have permission to access this page.
            Silahkan datang ke kantor untuk meminta akses data yang dibutuhkan.
        </p>

        <a href="{{ url('/') }}" class="btn btn-primary mt-3">
            Go Back homepage
        </a>
    </div>
</div>
@endsection
