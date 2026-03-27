<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MyTrans - Register</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="{{ asset('js/script.js') }}" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>
<body>
    <div class="wrapper">
        <div class="logo-top">
        <img src="{{ asset('img/logo.png') }}" alt="MyTrans Logo" class="logo">
        <h1>MyTrans Travels</h1>
        <p>Buat Akun Baru</p>
    </div>
    <div class="container">
        <div class="content">
            <h2>Daftar Akun</h2>
            <p class="subtitle">Silakan isi formulir di bawah ini untuk membuat akun baru.</p>
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
            action="{{ route('register') }}" method="POST" id="registerForm">
                @csrf
                <!-- Form fields will go here -->
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap anda" 
                    class="@error('name')error
                    @enderror">
                    @error('name')
                    <span class="error">{{ $message }}</span>
                    @enderror
                </div>
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
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Konfirmasi password anda" 
                        class="@error('password_confirmation')error
                        @enderror">
                        <span class="toggle-password material-symbols-outlined" onclick="togglePassword('password_confirmation', this)">visibility</span>
                    </div>
                    @error('password_confirmation')
                    <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="phone_number">Nomor Telepon</label>
                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" placeholder="Masukkan nomor telepon anda" 
                    class="@error('phone_number')error
                    @enderror">
                    @error('phone_number')
                    <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn-register">Daftar</button>
                <div class="divider">Atau</div>
                <!-- Tombol untuk daftar dengan Google (placeholder, implementasi OAuth diperlukan) -->
                <button type="button" class="btn-google">
                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google">
                    Daftar dengan google
                </button>

                <div class="login-link">
                    <p>Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a></p>
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
        document.getElementById('registerForm').addEventListener('submit', function(e) {
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

        // Real-time validation for phone number
        document.getElementById('phone_number').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>