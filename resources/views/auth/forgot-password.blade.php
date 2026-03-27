<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyTrans - Lupa Kata Sandi</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
<div class="wrapper">
    <div class="container">
        <div class="content">
            <h2>Lupa Kata Sandi</h2>
            <p class="subtitle">Masukkan email untuk reset Kata Sandi</p>
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

            <form action="{{ route('password.email') }}" method="POST" id="forgotPasswordForm">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="Masukkan email anda" 
                    class="@error('email')error
                    @enderror">
                    @error('email')
                    <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn-register">Kirim Link Reset</button>
            </form>

            <div class="navigation">
                <a href="{{ route('login') }}" class="nav-link">Kembali ke Login</a>

            </div>
        </div>
    </div>
</div>

<script>
        // Client-side validation
        document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            
            if (!email.trim() === '') {
                e.preventDefault();
                alert('Email tidak boleh kosong!');
                return false;
            }

            if (!email.includes('@') || !email.includes('.')) {
                e.preventDefault();
                alert('Format email tidak valid!');
                return false;
            }
        });
    </script>
</body>
</html>