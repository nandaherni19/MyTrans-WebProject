<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MyTrans - Verifikasi OTP</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&family=Material+Symbols+Outlined"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/auth/verify.css') }}">
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
                    <input type="email" name="email" id="email" value="{{ old('email', $email ?? request('email')) }}"
                        placeholder="nama@email.com" class="@error('email') error @enderror" required>
                    @error('email')<span class="error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="otp">Kode OTP</label>
                    <input type="text" name="otp" id="otp" maxlength="6" placeholder="······"
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
                <a href="{{ route('verify.otp.resend', ['email' => old('email', $email ?? request('email'))]) }}">Kirim
                    ulang</a>
            </div>
            <div class="back-link">
                <a href="{{ route('welcome') }}">← Kembali ke Beranda</a>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('otp').addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        document.getElementById('otpForm').addEventListener('submit', function (e) {
            if (document.getElementById('otp').value.length !== 6) {
                e.preventDefault(); alert('Kode OTP harus 6 digit!');
            }
        });
    </script>
</body>

</html>