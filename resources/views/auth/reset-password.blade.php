<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MyTrans - Reset Kata Sandi</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="{{ asset('js/script.js') }}" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>
<body>
    <div class="wrapper">
    <div class="container">
        <div class="content">
            <h2>Reset Kata Sandi</h2>
            <p class="subtitle">Silakan isi formulir di bawah ini untuk mereset Kata Sandi Anda.</p>
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
            action="{{ route('password.update') }}" method="POST" id="resetPasswordForm">
                @csrf
                <!-- Form fields will go here -->
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ request('email') }}" placeholder="Masukkan email anda" 
                    class="@error('email')error
                    @enderror">
                    @error('email')
                    <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password">Kata Sandi Baru</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password" placeholder="Masukkan Kata Sandi Baru anda" 
                        class="@error('password')error
                        @enderror">
                        <span class="toggle-password material-symbols-outlined" onclick="togglePassword('password', this)">visibility</span>
                    </div>
                    @error('password')
                    <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Kata sandi</label>
                    <div class="password-wrapper">
                        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Konfirmasi Kata sandi anda" 
                        class="@error('password_confirmation')error
                        @enderror">
                        <span class="toggle-password material-symbols-outlined" onclick="togglePassword('password_confirmation', this)">visibility</span>
                    </div>
                    @error('password_confirmation')
                    <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn-register">Reset Kata Sandi</button>
                <div class="navigation">
                    <a href="{{ route('welcome') }}" class="nav-link">Kembali ke Beranda</a>
                </div>
            </form>
        </div>
    </div>
    </div>

        <script>
        // Client-side validation
        document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Password dan konfirmasi password tidak cocok!');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password minimal 6 karakter!');
                return false;
            }
        });
    </script>
</body>
</html>