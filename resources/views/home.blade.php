{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MuTransNusa Travels - Beranda</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .welcome-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            max-width: 600px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        
        p {
            color: #666;
            margin-bottom: 30px;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            transition: transform 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="welcome-card">
        <h1>Selamat Datang di MuTransNusa Travels</h1>
        @if(auth()->check())
            <p>Halo, {{ auth()->user()->name }}! Selamat datang di platform booking paket wisata kami.</p>
        @else
            <p>Temukan pengalaman wisata terbaik bersama MuTransNusa Travels</p>
            <a href="{{ route('register') }}" class="btn">Mulai Registrasi</a>
        @endif
    </div>
</body>
</html>