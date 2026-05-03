<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MyTrans - Daftar</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&family=Material+Symbols+Outlined" rel="stylesheet" />
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --navy: #0f1e3d; --blue: #1e4db7; --blue-light: #2e6be6;
            --accent: #f5a623; --white: #ffffff;
            --gray-100: #f4f6fb; --gray-200: #e8ecf4;
            --gray-400: #9aa3b8; --gray-600: #5a6380; --text: #1a1f36;
        }
        body { font-family: 'DM Sans', sans-serif; min-height: 100vh; display: flex; background: var(--gray-100); }

        /* LEFT PANEL */
        .left-panel { width: 40%; position: relative; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between; }
        .left-panel .bg-image { position: absolute; inset: 0; background: url('https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=900&q=80') center/cover no-repeat; z-index: 0; }
        .left-panel .overlay { position: absolute; inset: 0; background: linear-gradient(160deg, rgba(10,20,60,0.88) 0%, rgba(15,40,100,0.70) 50%, rgba(5,15,40,0.92) 100%); z-index: 1; }
        .top-brand { position: relative; z-index: 2; padding: 36px 40px; display: flex; align-items: center; gap: 14px; }
        .brand-logo { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,0.3); }
        .brand-name { font-family: 'Playfair Display', serif; font-size: 20px; color: var(--white); }
        .brand-tagline { font-size: 11px; color: rgba(255,255,255,0.55); letter-spacing: 2px; text-transform: uppercase; margin-top: 2px; }
        .bottom-text { position: relative; z-index: 2; padding: 40px; }
        .bottom-text h2 { font-family: 'Playfair Display', serif; font-size: 26px; color: var(--white); line-height: 1.35; margin-bottom: 10px; }
        .bottom-text p { font-size: 13px; color: rgba(255,255,255,0.6); line-height: 1.6; }
        .accent-line { width: 40px; height: 3px; background: var(--accent); border-radius: 2px; margin-bottom: 16px; }

        /* RIGHT PANEL */
        .right-panel { width: 60%; display: flex; align-items: center; justify-content: center; padding: 40px; background: var(--white); overflow-y: auto; }
        .form-container { width: 100%; max-width: 440px; animation: fadeUp 0.5s ease both; }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .form-header { margin-bottom: 28px; }
        .form-header .welcome { font-size: 13px; font-weight: 500; color: var(--blue); letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 8px; }
        .form-header h1 { font-family: 'Playfair Display', serif; font-size: 28px; color: var(--text); margin-bottom: 6px; }
        .form-header p { font-size: 14px; color: var(--gray-600); }

        .alert { padding: 12px 16px; border-radius: 10px; margin-bottom: 18px; font-size: 13px; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-danger { background: #fde8e8; color: #9b1c1c; border-left: 3px solid #f05252; }

        /* 2 column grid for some fields */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 16px; }

        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 13px; font-weight: 500; color: var(--text); margin-bottom: 6px; }
        .form-group input { width: 100%; padding: 11px 16px; border: 1.5px solid var(--gray-200); border-radius: 10px; background: var(--gray-100); font-size: 14px; font-family: 'DM Sans', sans-serif; color: var(--text); transition: all 0.2s; }
        .form-group input:focus { outline: none; border-color: var(--blue-light); background: var(--white); box-shadow: 0 0 0 3px rgba(46,107,230,0.1); }
        .form-group input::placeholder { color: var(--gray-400); }
        .form-group input.error { border-color: #f05252; }
        span.error { font-size: 12px; color: #f05252; margin-top: 4px; display: block; }

        .password-wrapper { position: relative; }
        .password-wrapper input { padding-right: 44px; }
        .toggle-password { position: absolute; right: 13px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 19px; color: var(--gray-400); transition: color 0.2s; user-select: none; }
        .toggle-password:hover { color: var(--blue); }

        .btn-submit { width: 100%; padding: 13px; background: linear-gradient(135deg, var(--blue) 0%, var(--blue-light) 100%); color: var(--white); border: none; border-radius: 10px; font-size: 15px; font-weight: 600; font-family: 'DM Sans', sans-serif; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(30,77,183,0.35); }
        .btn-submit:active { transform: translateY(0); }

        .bottom-links { text-align: center; margin-top: 18px; font-size: 13px; color: var(--gray-600); }
        .bottom-links a { color: var(--blue); font-weight: 600; text-decoration: none; }
        .bottom-links a:hover { text-decoration: underline; }
        .back-link { text-align: center; margin-top: 10px; }
        .back-link a { font-size: 13px; color: var(--gray-400); text-decoration: none; transition: color 0.2s; }
        .back-link a:hover { color: var(--blue); }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            .left-panel { width: 100%; min-height: 180px; }
            .right-panel { width: 100%; padding: 28px 20px; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
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
                        placeholder="Masukkan nama lengkap Anda"
                        class="@error('name') error @enderror">
                    @error('name')<span class="error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                        placeholder="nama@email.com"
                        class="@error('email') error @enderror">
                    @error('email')<span class="error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="phone_number">Nomor Telepon</label>
                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                        placeholder="08xxxxxxxxxx"
                        class="@error('phone_number') error @enderror">
                    @error('phone_number')<span class="error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password"
                            placeholder="Minimal 8 karakter"
                            class="@error('password') error @enderror">
                        <span class="toggle-password material-symbols-outlined" onclick="togglePassword('password', this)">visibility</span>
                    </div>
                    @error('password')<span class="error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            placeholder="Ulangi password Anda"
                            class="@error('password_confirmation') error @enderror">
                        <span class="toggle-password material-symbols-outlined" onclick="togglePassword('password_confirmation', this)">visibility</span>
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

        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirmation').value;
            if (password.length < 8) { e.preventDefault(); alert('Password minimal 8 karakter!'); return; }
            if (password !== confirm) { e.preventDefault(); alert('Password dan konfirmasi tidak cocok!'); return; }
        });

        document.getElementById('phone_number').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>