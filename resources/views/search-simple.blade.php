<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Files</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .search-header {
            text-align: center;
            padding: 60px 0 40px;
        }
        
        .search-header h1 {
            color: #1a73e8;
            font-size: 48px;
            margin-bottom: 30px;
        }
        
        .search-box {
            position: relative;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .search-box input[type="text"] {
            width: 100%;
            padding: 15px 50px 15px 20px;
            border: 1px solid #dfe1e5;
            border-radius: 24px;
            font-size: 16px;
            outline: none;
            transition: box-shadow 0.2s;
        }
        
        .search-box input[type="text"]:focus {
            border-color: transparent;
            box-shadow: 0 1px 6px rgba(32, 33, 36, 0.28);
        }
        
        .search-box button {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px 10px;
            color: #1a73e8;
            font-size: 18px;
        }
        
        .results-info {
            color: #70757a;
            font-size: 14px;
            margin: 20px 0;
        }
        
        .result-item {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.2s;
        }
        
        .result-item:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }
        
        .result-title {
            font-size: 20px;
            margin-bottom: 5px;
        }
        
        .result-title a {
            color: #1a0dab;
            text-decoration: none;
        }
        
        .result-title a:hover {
            text-decoration: underline;
        }
        
        .result-url {
            color: #006621;
            font-size: 14px;
            margin-bottom: 8px;
        }
        
        .result-snippet {
            color: #545454;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 10px;
        }
        
        .result-meta {
            display: flex;
            gap: 20px;
            font-size: 12px;
            color: #70757a;
        }
        
        .result-meta span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .download-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 16px;
            background-color: #1a73e8;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            transition: background-color 0.2s;
        }
        
        .download-btn:hover {
            background-color: #1557b0;
        }
        
        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #70757a;
        }
        
        .no-results h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="search-header">
            <h1>🔍 File Search</h1>
            
            <form action="{{ route('search') }}" method="GET" class="search-box">
                <input 
                    type="text" 
                    name="q" 
                    value="{{ $query ?? '' }}" 
                    placeholder="Search for files..." 
                    autofocus
                >
                <button type="submit">🔍</button>
            </form>
        </div>
        
        @if(isset($query) && $query != '')
            <div class="results-info">
                Found {{ count($posts) }} result(s) for "<strong>{{ $query }}</strong>"
            </div>
            
            @if(count($posts) > 0)
                @foreach($posts as $post)
                    <div class="result-item">
                        <div class="result-title">
                            <a href="{{ route('posts.slug', $post->slug) }}">
                                {{ $post->title }}
                            </a>
                        </div>
                        
                        <div class="result-url">
                            {{ url($post->slug) }}
                        </div>
                        
                        <div class="result-snippet">
                            {{ Str::limit($post->content, 200) }}
                        </div>
                        
                        <div class="result-meta">
                            <span>📄 {{ $post->file_name }}</span>
                            <span>💾 {{ number_format($post->file_size / 1024, 2) }} KB</span>
                            <span>👁️ {{ number_format($post->views_count) }} views</span>
                            <span>⬇️ {{ number_format($post->downloads_count) }} downloads</span>
                        </div>
                        
                        <a href="{{ route('posts.download', $post) }}" class="download-btn">
                            Download File
                        </a>
                    </div>
                @endforeach
            @else
                <div class="no-results">
                    <h2>No results found</h2>
                    <p>Try different keywords or check your spelling</p>
                </div>
            @endif
        @endif
    </div>
</body>
</html>