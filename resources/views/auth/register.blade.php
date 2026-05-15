<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MyTrans - Daftar</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&family=Material+Symbols+Outlined"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
</head>

<body>
    <!-- LEFT -->
    <div class="left-panel">
        <div class="bg-image"></div>
        <div class="overlay"></div>
        <div class="top-brand">
            <img src="{{ asset('img/logo.png') }}" alt="MyTrans Logo" class="brand-logo">
            <div>
                <div class="brand-name">MyTrans Travels</div>
                <div class="brand-tagline">Perjalanan Nyaman Anda</div>
            </div>
        </div>
        <div class="bottom-text">
            <div class="accent-line"></div>
            <h2>Bergabung &amp;<br>Mulai Perjalanan</h2>
            <p>Daftar sekarang dan nikmati kemudahan<br>pemesanan tiket perjalanan Anda.</p>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="right-panel">
        <div class="form-container">
            <div class="form-header">
                <div class="welcome">Buat Akun Baru</div>
                <h1>Daftar Akun</h1>
                <p>Silakan isi formulir di bawah untuk membuat akun baru.</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin-left:16px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.submit') }}" method="POST" id="registerForm">
                @csrf

                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        placeholder="Masukkan nama lengkap Anda" class="@error('name') error @enderror">
                    @error('name')<span class="error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="nama@email.com"
                        class="@error('email') error @enderror">
                    @error('email')<span class="error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="phone_number">Nomor Telepon</label>
                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                        placeholder="08xxxxxxxxxx" class="@error('phone_number') error @enderror">
                    @error('phone_number')<span class="error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password" placeholder="Minimal 8 karakter"
                            class="@error('password') error @enderror">
                        <span class="toggle-password material-symbols-outlined"
                            onclick="togglePassword('password', this)">visibility</span>
                    </div>
                    @error('password')<span class="error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            placeholder="Ulangi password Anda" class="@error('password_confirmation') error @enderror">
                        <span class="toggle-password material-symbols-outlined"
                            onclick="togglePassword('password_confirmation', this)">visibility</span>
                    </div>
                    @error('password_confirmation')<span class="error">{{ $message }}</span>@enderror
                </div>

                <button type="submit" class="btn-submit">
                    Daftar Sekarang
                    <span class="material-symbols-outlined" style="font-size:18px;">arrow_forward</span>
                </button>
            </form>

            <div class="bottom-links">
                Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a>
            </div>
            <div class="back-link">
                <a href="{{ route('welcome') }}">← Kembali ke Beranda</a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(id, icon) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.textContent = input.type === 'password' ? 'visibility' : 'visibility_off';
        }

        document.getElementById('registerForm').addEventListener('submit', function (e) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirmation').value;
            if (password.length < 8) { e.preventDefault(); alert('Password minimal 8 karakter!'); return; }
            if (password !== confirm) { e.preventDefault(); alert('Password dan konfirmasi tidak cocok!'); return; }
        });

        document.getElementById('phone_number').addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>

</html>