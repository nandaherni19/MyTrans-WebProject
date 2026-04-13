<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MyTrans - Verifikasi OTP</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <script src="{{ asset('js/script.js') }}" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>

<body>
    <div class="wrapper">
        <div class="container">
            <div class="content">
                <h2>Verifikasi Akun</h2>
                <p class="subtitle">Silakan isi kode OTP yang telah dikirim di Email anda.</p>

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

                <form action="{{ route('verify.otp.submit') }}" method="POST" id="verifyOtpForm">
                    @csrf

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input 
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email', $email ?? request('email')) }}"
                            placeholder="Masukkan email anda"
                            class="@error('email') error @enderror"
                            required
                        >
                        @error('email')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="otp">Kode OTP</label>
                        <input
                            type="text"
                            name="otp"
                            id="otp"
                            maxlength="6"
                            placeholder="Masukkan Kode OTP"
                            required
                            class="@error('otp') error @enderror"
                        >
                        @error('otp')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn-register">Verifikasi Akun</button>

                    <div class="resend-link" style="margin-top: 15px; text-align: center;">
                        <p>Tidak menerima kode?
                            <a href="{{ route('verify.otp.resend', ['email' => old('email', $email ?? request('email'))]) }}">
                                Kirim lagi
                            </a>
                        </p>
                    </div>

                    <div class="navigation">
                        <a href="{{ route('welcome') }}" class="nav-link">Kembali ke Beranda</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('verifyOtpForm').addEventListener('submit', function(e) {
            const otp = document.getElementById('otp').value;

            if (otp.length !== 6) {
                e.preventDefault();
                alert('Kode OTP harus 6 digit!');
                return false;
            }
        });
    </script>
</body>
</html>