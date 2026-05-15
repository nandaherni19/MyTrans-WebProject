<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyTrans - Lupa Kata Sandi</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&family=Material+Symbols+Outlined"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/auth/forgot.css') }}">
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
            <h2>Lupa Password?<br>Tenang, Kami Bantu</h2>
            <p>Masukkan email Anda dan kami akan mengirimkan link untuk reset password.</p>
        </div>
    </div>

    <div class="right-panel">
        <div class="form-container">
            <div class="icon-area">
                <span class="material-symbols-outlined">lock_reset</span>
            </div>
            <div class="form-header">
                <div class="welcome">Reset Password</div>
                <h1>Lupa Kata Sandi</h1>
                <p>Masukkan email terdaftar Anda untuk menerima link reset password.</p>
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

            <form action="{{ route('password.email') }}" method="POST" id="forgotForm">
                @csrf
                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="nama@email.com"
                        class="@error('email') error @enderror">
                    @error('email')<span class="error">{{ $message }}</span>@enderror
                </div>
                <button type="submit" class="btn-submit">
                    Kirim Link Reset
                    <span class="material-symbols-outlined" style="font-size:18px;">send</span>
                </button>
            </form>

            <div class="back-link">
                <a href="{{ route('login') }}">← Kembali ke Login</a>
            </div>
        </div>
    </div>
</body>

</html>