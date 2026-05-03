<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MyTrans')</title>

    <link rel="stylesheet" href="{{ asset('css/user/navbar.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    @stack('styles')
</head>
<body>

    <header class="navbar" id="navbar">
        <div class="nav-logo">
            <img src="{{ asset('img/logo.png') }}" alt="MyTrans Logo">
            <span class="nav-logo-text">MyTrans Nusa</span>
        </div>

        <nav class="nav-menu">
            <a href="{{ route('welcome') }}" 
            class="{{ request()->routeIs('welcome') ? 'active' : '' }}">
            Beranda
            </a>

            <a href="{{ route('guest.katalogpaketwisata') }}" 
            class="{{ request()->routeIs('guest.katalogpaketwisata', 'guest.detailpaket') ? 'active' : '' }}">
            Paket Wisata
            </a>

            <a href="{{ route('welcome') }}#tentang">Tentang Kami</a>
            <a href="{{ route('welcome') }}#kontak">Kontak</a>
        </nav>

        <div class="nav-right">
            <a href="{{ route('login') }}" class="btn-nav-login">Masuk</a>
            <a href="{{ route('register') }}" class="btn-nav-register">Daftar</a>
        </div>
    </header>
    <main>
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>