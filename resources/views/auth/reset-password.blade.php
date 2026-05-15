<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MyTrans - Reset Kata Sandi</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&family=Material+Symbols+Outlined"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/auth/reset.css') }}">
</head>

<body>
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
            <h2>Buat Password<br>Baru Anda</h2>
            <p>Pastikan password baru Anda kuat dan mudah diingat.</p>
        </div>
    </div>

    <div class="right-panel">
        <div class="form-container">
            <div class="icon-area">
                <span class="material-symbols-outlined">key</span>
            </div>
            <div class="form-header">
                <div class="welcome">Password Baru</div>
                <h1>Reset Kata Sandi</h1>
                <p>Masukkan password baru Anda di bawah ini.</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin-left:16px;">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" id="resetForm">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ request('email') }}"
                        placeholder="nama@email.com" class="@error('email') error @enderror">
                    @error('email')<span class="error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="password">Password Baru</label>
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
                            placeholder="Ulangi password baru" class="@error('password_confirmation') error @enderror">
                        <span class="toggle-password material-symbols-outlined"
                            onclick="togglePassword('password_confirmation', this)">visibility</span>
                    </div>
                    @error('password_confirmation')<span class="error">{{ $message }}</span>@enderror
                </div>

                <button type="submit" class="btn-submit">
                    Reset Password
                    <span class="material-symbols-outlined" style="font-size:18px;">check_circle</span>
                </button>
            </form>

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
        document.getElementById('resetForm').addEventListener('submit', function (e) {
            const p = document.getElementById('password').value;
            const c = document.getElementById('password_confirmation').value;
            if (p.length < 8) { e.preventDefault(); alert('Password minimal 8 karakter!'); return; }
            if (p !== c) { e.preventDefault(); alert('Password dan konfirmasi tidak cocok!'); return; }
        });
    </script>
</body>

</html>