<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 40px;
        }
        h1 {
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .meta {
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 30px;
        }
        .content {
            text-align: justify;
            font-size: 12px;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
            text-align: center;
            color: #95a5a6;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    
    <div class="meta">
        <strong>Author:</strong> {{ $author }} <br>
        <strong>Date:</strong> {{ $date }}
    </div>
    
    <div class="content">
        {!! nl2br(e($content)) !!}
    </div>
    
    <div class="footer">
        <p>Generated with Love in Indonesia</p>
    </div>
</body>
</html>
