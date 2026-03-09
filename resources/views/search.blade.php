@extends('layouts.app')

@section('css')
<style>
    .search-container {
        max-width: 900px;
        margin: 0 auto;
        padding-top: 100px;
    }
    
    .search-header {
        text-align: center;
        margin-bottom: 40px;
    }
    
    .search-header h1 {
        font-size: 2.5rem;
        color: #1a73e8;
        margin-bottom: 20px;
        font-weight: 600;
    }
    
    .search-box {
        position: relative;
        margin-bottom: 30px;
    }
    
    .search-box .form-control {
        height: 50px;
        padding-left: 20px;
        padding-right: 120px;
        border-radius: 25px;
        border: 2px solid #dfe1e5;
        font-size: 16px;
    }
    
    .search-box .form-control:focus {
        border-color: #1a73e8;
        box-shadow: 0 1px 6px rgba(26, 115, 232, 0.3);
    }
    
    .search-box .btn-search {
        position: absolute;
        right: 5px;
        top: 5px;
        height: 40px;
        padding: 0 25px;
        border-radius: 20px;
        background-color: #1a73e8;
        border: none;
    }
    
    .search-box .btn-search:hover {
        background-color: #1557b0;
    }
</style>
@endsection

@section('content')
<div class="container search-container">
    <div class="search-header">
        <img src="{{ asset('1627539868logo-kpu.png') }}" alt="KPU Logo" style="height: 80px; margin-bottom: 15px;">
        <h1>TemuKPU</h1>
        <p class="text-muted">Akuntabel dimulai dari TRANSPARANSI</p>
    </div>
    
    <form action="{{ route('search.results') }}" method="GET" class="search-box">
        <div class="position-relative">
            <input 
                type="text" 
                name="q" 
                class="form-control" 
                placeholder="Search for files, documents, or content..." 
                autofocus
                required
            >
            <button type="submit" class="btn btn-primary btn-search">
                <i class="bi bi-search"></i> Search
            </button>
        </div>
    </form>
    
    <div class="text-center text-muted mt-5">
        <i class="bi bi-search" style="font-size: 4rem;"></i>
        <p class="mt-3">Enter a search term to find documents</p>
    </div>
</div>
@endsection
