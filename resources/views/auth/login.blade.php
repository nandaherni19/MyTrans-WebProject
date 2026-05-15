<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MyTrans - Login</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&family=Material+Symbols+Outlined"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
</head>

<body>
    <!-- LEFT PANEL -->
    <div class="left-panel">
        <div class="bg-image"></div>
        <div class="overlay"></div>

        <div class="top-brand">
            <img src="{{ asset('img/logo.png') }}" alt="MyTrans Logo" class="brand-logo">
            <div>
                <div class="brand-name">MyTrans</div>
                <div class="brand-tagline">Perjalanan Nyaman Anda</div>
            </div>
        </div>

        <div class="bottom-text">
            <div class="accent-line"></div>
            <h2>Perjalanan Dimulai<br>Dari Sini</h2>
            <p>Pesan tiket perjalanan Anda dengan mudah,<br>cepat, dan terpercaya.</p>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="right-panel">
        <div class="form-container">
            <div class="form-header">
                <div class="welcome">Selamat Datang</div>
                <h1>Masuk Akun</h1>
                <p>Silakan masukkan detail akun Anda untuk melanjutkan.</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin-left: 16px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST" id="loginForm">
                @csrf
                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="nama@email.com"
                        class="@error('email') error @enderror">
                    @error('email')<span class="error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password" placeholder="Masukkan password Anda"
                            class="@error('password') error @enderror">
                        <span class="toggle-password material-symbols-outlined"
                            onclick="togglePassword('password', this)">visibility</span>
                    </div>
                    @error('password')<span class="error">{{ $message }}</span>@enderror
                </div>

                <div class="form-row">
                    <div></div>
                    <a href="{{ route('password.request') }}" class="forgot-link">Lupa Password?</a>
                </div>

                <button type="submit" class="btn-login">
                    Masuk
                    <span class="material-symbols-outlined arrow">arrow_forward</span>
                </button>
            </form>

            <div class="divider">atau</div>

            <div class="register-link">
                Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
            </div>

            <div class="back-link">
                <a href="{{ route('welcome') }}">← Kembali ke Beranda</a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(id, icon) {
            const input = document.getElementById(id);
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        }

        document.getElementById('loginForm').addEventListener('submit', function (e) {
            const password = document.getElementById('password').value;
            if (password.length < 8) {
                e.preventDefault();
                alert('Password minimal 8 karakter!');
            }
        });
    </script>
</body>

</html>