@extends('layouts.app')

@section('css')
<style>
    .search-container {
        max-width: 900px;
        margin: 0 auto;
    }
    
    .search-header {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .search-header h1 {
        font-size: 1.8rem;
        color: #1a73e8;
        margin-bottom: 10px;
        font-weight: 600;
    }
    
    .search-header h1 a {
        text-decoration: none;
        color: #1a73e8;
    }
    
    .search-header h1 a:hover {
        text-decoration: underline;
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
    
    .results-info {
        color: #70757a;
        font-size: 14px;
        margin-bottom: 20px;
    }
    
    .result-card {
        background: white;
        border: 1px solid #e8eaed;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        transition: box-shadow 0.2s;
    }
    
    .result-card:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .result-title {
        font-size: 1.25rem;
        margin-bottom: 8px;
    }
    
    .result-title a {
        color: #1a0dab;
        text-decoration: none;
        font-weight: 500;
    }
    
    .result-title a:hover {
        text-decoration: underline;
    }
    
    .result-url {
        color: #006621;
        font-size: 14px;
        margin-bottom: 10px;
        word-break: break-all;
    }
    
    .result-snippet {
        color: #545454;
        line-height: 1.6;
        margin-bottom: 15px;
    }
    
    .result-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        font-size: 13px;
        color: #70757a;
        margin-bottom: 15px;
    }
    
    .result-meta .badge {
        font-weight: normal;
        padding: 5px 10px;
    }
    
    .no-results {
        text-align: center;
        padding: 60px 20px;
    }
    
    .no-results h3 {
        color: #5f6368;
        margin-bottom: 15px;
    }
    
    .no-results p {
        color: #70757a;
    }
    
    .pagination {
        margin-top: 30px;
        justify-content: center;
    }
    
    .page-link {
        color: #1a73e8;
    }
    
    .page-item.active .page-link {
        background-color: #1a73e8;
        border-color: #1a73e8;
    }
    
    /* Hide pagination info text "Showing X to Y of Z results" */
    .d-flex.justify-content-center nav .text-muted,
    .d-flex.justify-content-center > .text-muted,
    nav .pagination-info {
        display: none !important;
    }
</style>
@endsection

@php
    use Illuminate\Support\Facades\URL;
@endphp

@section('content')
<div class="container search-container">
    <div class="search-header">
        <h1><a href="{{ route('search') }}">TemuKPU</a></h1>
    </div>
    
    <form action="{{ route('search.results') }}" method="GET" class="search-box">
        <div class="position-relative">
            <input 
                type="text" 
                name="q" 
                class="form-control" 
                value="{{ $query ?? '' }}" 
                placeholder="Search for files, documents, or content..." 
                autofocus
                required
            >
            <button type="submit" class="btn btn-primary btn-search">
                <i class="bi bi-search"></i> Search
            </button>
        </div>
    </form>
    
    <!-- Department Filter -->
    <div class="department-filter mb-4">
        <form action="{{ route('search.results') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-center">
            <input type="hidden" name="q" value="{{ $query ?? '' }}">
            <span class="me-2 text-muted">Department:</span>
            @php
                $departments = config('temukpu.departments');
                $selectedDept = request('department');
            @endphp
            <a href="{{ route('search.results', ['q' => $query, 'sort' => request('sort')]) }}" 
               class="btn btn-sm {{ !$selectedDept ? 'btn-primary' : 'btn-outline-secondary' }}">
                Semua
            </a>
            @foreach($departments as $dept)
                <a href="{{ route('search.results', ['q' => $query, 'department' => strtolower($dept), 'sort' => request('sort')]) }}" 
                   class="btn btn-sm {{ strtolower($selectedDept) == strtolower($dept) ? 'btn-primary' : 'btn-outline-secondary' }}">
                    {{ ucfirst($dept) }}
                </a>
            @endforeach
        </form>
    </div>
    
    <!-- Sort by Date Filter -->
    <div class="sort-filter mb-4">
        <form action="{{ route('search.results') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-center">
            <input type="hidden" name="q" value="{{ $query ?? '' }}">
            @if(request('department'))
                <input type="hidden" name="department" value="{{ request('department') }}">
            @endif
            <span class="me-2 text-muted">Sort by:</span>
            @php
                $selectedSort = request('sort', 'newest');
            @endphp
            <a href="{{ route('search.results', ['q' => $query, 'department' => request('department'), 'sort' => 'newest']) }}" 
               class="btn btn-sm {{ $selectedSort == 'newest' ? 'btn-primary' : 'btn-outline-secondary' }}">
                🆕 Newest
            </a>
            <a href="{{ route('search.results', ['q' => $query, 'department' => request('department'), 'sort' => 'oldest']) }}" 
               class="btn btn-sm {{ $selectedSort == 'oldest' ? 'btn-primary' : 'btn-outline-secondary' }}">
                🕐 Oldest
            </a>
        </form>
    </div>
    {{-- Old hardcoded departments list
    <div class="department-filter mb-4">
        <form action="{{ route('search.results') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-center">
            <input type="hidden" name="q" value="{{ $query ?? '' }}">
            <span class="me-2 text-muted">Filter:</span>
            @php
                $departments = ['kul', 'rendatin', 'teknis hukum', 'sdm parmas', 'komisioner'];
                $selectedDept = request('department');
            @endphp
            <a href="{{ route('search.results', ['q' => $query]) }}" 
               class="btn btn-sm {{ !$selectedDept ? 'btn-primary' : 'btn-outline-secondary' }}">
                Semua
            </a>
            @foreach($departments as $dept)
                <a href="{{ route('search.results', ['q' => $query, 'department' => $dept]) }}" 
                   class="btn btn-sm {{ $selectedDept == $dept ? 'btn-primary' : 'btn-outline-secondary' }}">
                    {{ ucfirst($dept) }}
                </a>
            @endforeach
        </form>
    </div>
    --}}
    {{-- Old results-info without filter
    <div class="results-info">
        @if($posts->total() > 0)
            About {{ number_format($posts->total()) }} result(s) for "<strong>{{ $query }}</strong>" 
            ({{ number_format($posts->currentPage() * $posts->perPage() - $posts->perPage() + 1) }}-{{ number_format(min($posts->currentPage() * $posts->perPage(), $posts->total())) }})
        @else
            No results found for "<strong>{{ $query }}</strong>"
        @endif
    </div>
    --}}
    
    <div class="results-info">
        @if($posts->total() > 0)
            About {{ number_format($posts->total()) }} result(s) for "<strong>{{ $query }}</strong>" 
            ({{ number_format($posts->currentPage() * $posts->perPage() - $posts->perPage() + 1) }}-{{ number_format(min($posts->currentPage() * $posts->perPage(), $posts->total())) }})
        @else
            No results found for "<strong>{{ $query }}</strong>"
        @endif
    </div>
    
    @if($posts->count() > 0)
        @foreach($posts as $post)
            <div class="result-card">
                <div class="result-title">
                    <a href="{{ route('posts.slug', $post->slug) }}">
                        {{ $post->title }}
                    </a>
                </div>
                
                <div class="result-url">
                    {{ url($post->slug) }}
                </div>
                
                <div class="result-snippet">
                    {{ Str::limit($post->content, 250) }}
                </div>
                
                <div class="result-meta">
                    @auth
                    @if(auth()->user()->roles->count() > 0)
                    <span class="badge bg-light text-dark">
                        📄 {{ $post->file_name }}
                    </span>
                    @endif
                    @endauth
                    
                    <span class="badge bg-light text-dark">
                        💾 {{ number_format($post->file_size / 1024, 2) }} KB
                    </span>
                    <span class="badge bg-light text-dark">
                        👁️ {{ number_format($post->views_count) }} views
                    </span>
                    <span class="badge bg-light text-dark">
                        ⬇️ {{ number_format($post->downloads_count) }} downloads
                    </span>
                    @if($post->type)
                        <span class="badge bg-primary">
                            {{ ucfirst($post->type) }}
                        </span>
                    @endif
                    <span class="badge bg-primary">
                        {{ ucfirst($post->department) }}
                    </span>
                </div>
                @auth
                    @if(auth()->user()->roles->count() > 0)
                    <a href="{{ URL::signedRoute('posts.download', ['post' => $post], now()->addHours(2)) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-download"></i> Download File
                    </a>
                    @endif
                @endauth

                <a href="{{ route('posts.slug', $post->slug) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-eye"></i> View Details
                </a>
            </div>
        @endforeach
        
        <!-- Pagination -->
        {{-- Old pagination without department filter
        <div class="d-flex justify-content-center">
            {{ $posts->appends(['q' => $query])->links() }}
        </div>
        --}}
        <br>
        <div class="d-flex justify-content-center">
            {{ $posts->appends(['q' => $query, 'department' => request('department'), 'sort' => request('sort', 'newest')])->links() }}
        </div>
    @else
        <div class="no-results">
            <h3>No results found</h3>
            <p>Try different keywords or check your spelling</p>
            <p class="mt-3">
                <strong>Search tips:</strong><br>
                • Try more general keywords<br>
                • Check your spelling<br>
                • Try different words with similar meaning
            </p>
            <a href="{{ route('search') }}" class="btn btn-primary mt-3">
                <i class="bi bi-arrow-left"></i> Back to Search
            </a>
        </div>
    @endif
</div>
@endsection
