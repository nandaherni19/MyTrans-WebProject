<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MyTrans - Verifikasi OTP</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&family=Material+Symbols+Outlined" rel="stylesheet" />
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --blue: #1e4db7; --blue-light: #2e6be6; --accent: #f5a623;
            --white: #ffffff; --gray-100: #f4f6fb; --gray-200: #e8ecf4;
            --gray-400: #9aa3b8; --gray-600: #5a6380; --text: #1a1f36;
        }
        body { font-family: 'DM Sans', sans-serif; min-height: 100vh; display: flex; background: var(--gray-100); }
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

        .right-panel { width: 60%; display: flex; align-items: center; justify-content: center; padding: 40px; background: var(--white); }
        .form-container { width: 100%; max-width: 400px; animation: fadeUp 0.5s ease both; }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .icon-area { display: flex; align-items: center; justify-content: center; width: 64px; height: 64px; background: #eff4ff; border-radius: 16px; margin-bottom: 24px; }
        .icon-area span { font-size: 32px; color: var(--blue); }

        .form-header { margin-bottom: 28px; }
        .form-header .welcome { font-size: 13px; font-weight: 500; color: var(--blue); letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 8px; }
        .form-header h1 { font-family: 'Playfair Display', serif; font-size: 28px; color: var(--text); margin-bottom: 6px; }
        .form-header p { font-size: 14px; color: var(--gray-600); }

        .alert { padding: 12px 16px; border-radius: 10px; margin-bottom: 18px; font-size: 13px; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-danger { background: #fde8e8; color: #9b1c1c; border-left: 3px solid #f05252; }

        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 500; color: var(--text); margin-bottom: 6px; }
        .form-group input { width: 100%; padding: 12px 16px; border: 1.5px solid var(--gray-200); border-radius: 10px; background: var(--gray-100); font-size: 14px; font-family: 'DM Sans', sans-serif; color: var(--text); transition: all 0.2s; }
        .form-group input:focus { outline: none; border-color: var(--blue-light); background: var(--white); box-shadow: 0 0 0 3px rgba(46,107,230,0.1); }
        .form-group input::placeholder { color: var(--gray-400); }
        .form-group input.error { border-color: #f05252; }
        span.error { font-size: 12px; color: #f05252; margin-top: 4px; display: block; }

        /* OTP input styling */
        #otp { font-size: 20px; font-weight: 600; letter-spacing: 8px; text-align: center; }

        .btn-submit { width: 100%; padding: 13px; background: linear-gradient(135deg, var(--blue) 0%, var(--blue-light) 100%); color: var(--white); border: none; border-radius: 10px; font-size: 15px; font-weight: 600; font-family: 'DM Sans', sans-serif; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(30,77,183,0.35); }

        .resend-box { text-align: center; margin-top: 16px; font-size: 13px; color: var(--gray-600); }
        .resend-box a { color: var(--blue); font-weight: 600; text-decoration: none; }
        .resend-box a:hover { text-decoration: underline; }

        .back-link { text-align: center; margin-top: 10px; }
        .back-link a { font-size: 13px; color: var(--gray-400); text-decoration: none; }
        .back-link a:hover { color: var(--blue); }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            .left-panel { width: 100%; min-height: 180px; }
            .right-panel { width: 100%; padding: 28px 20px; }
        }
    </style>
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
            <h2>Verifikasi<br>Akun Anda</h2>
            <p>Kode OTP telah dikirim ke email Anda. Segera verifikasi sebelum kadaluarsa.</p>
        </div>
    </div>

    <div class="right-panel">
        <div class="form-container">
            <div class="icon-area">
                <span class="material-symbols-outlined">mark_email_read</span>
            </div>
            <div class="form-header">
                <div class="welcome">Verifikasi Email</div>
                <h1>Masukkan Kode OTP</h1>
                <p>Kode 6 digit telah dikirim ke email Anda. Berlaku selama 10 menit.</p>
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

            <form action="{{ route('verify.otp.submit') }}" method="POST" id="otpForm">
                @csrf

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email"
                        value="{{ old('email', $email ?? request('email')) }}"
                        placeholder="nama@email.com"
                        class="@error('email') error @enderror" required>
                    @error('email')<span class="error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="otp">Kode OTP</label>
                    <input type="text" name="otp" id="otp"
                        maxlength="6" placeholder="······"
                        class="@error('otp') error @enderror" required>
                    @error('otp')<span class="error">{{ $message }}</span>@enderror
                </div>

                <button type="submit" class="btn-submit">
                    Verifikasi Akun
                    <span class="material-symbols-outlined" style="font-size:18px;">verified</span>
                </button>
            </form>

            <div class="resend-box">
                Tidak menerima kode?
                <a href="{{ route('verify.otp.resend', ['email' => old('email', $email ?? request('email'))]) }}">Kirim ulang</a>
            </div>
            <div class="back-link">
                <a href="{{ route('welcome') }}">← Kembali ke Beranda</a>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('otp').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        document.getElementById('otpForm').addEventListener('submit', function(e) {
            if (document.getElementById('otp').value.length !== 6) {
                e.preventDefault(); alert('Kode OTP harus 6 digit!');
            }
        });
    </script>
</body>
</html>