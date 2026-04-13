<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MyTrans - Login</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <script src="{{ asset('js/script.js') }}" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>
<body>
    <div class="wrapper">
        <div class="logo-top">
        <img src="{{ asset('img/logo.png') }}" alt="MyTrans Logo" class="logo">
        <h1>MyTrans Travels</h1>
        <p>Masuk ke Akun Anda</p>
    </div>
    <div class="container">
        <div class="content">
            <h2>Masuk Akun</h2>
            <p class="subtitle">Silakan isi formulir di bawah ini untuk masuk ke akun Anda.</p>
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form 
            action="{{ route('login.submit') }}" method="POST" id="loginForm">
                @csrf
                <!-- Form fields will go here -->
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="Masukkan email anda" 
                    class="@error('email')error
                    @enderror">
                    @error('email')
                    <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password" placeholder="Masukkan password anda" 
                        class="@error('password')error
                        @enderror">
                        <span class="toggle-password material-symbols-outlined" onclick="togglePassword('password', this)">visibility</span>
                    </div>
                    @error('password')
                    <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="navigation forgot-link">
                    <a href="{{ route('password.request') }}" class="nav-link">Lupa Kata Sandi?</a>
                </div>

                <button type="submit" class="btn-register">Login</button>

                <div class="login-link">
                    <p>Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a></p>
                </div>
                <div class="navigation">
                    <a href="{{ route('welcome') }}" class="nav-link">Kembali ke Beranda</a>
                </div>
            </form>
        </div>
    </div>
    </div>

        <script>
        // Client-side validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password minimal 6 karakter!');
                return false;
            }
        });
    </script>
</body>
</html>