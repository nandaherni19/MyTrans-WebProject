<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MyTrans')</title>

    <link rel="stylesheet" href="{{ asset('css/user/navbar.css') }}">
    @stack('styles')
</head>
<body>

    <header class="navbar">
        <div class="nav-logo">
            <img src="{{ asset('img/logo.png') }}" alt="Logo MyTrans">
        </div>

        <nav class="nav-menu">
            <a href="{{ route('welcome') }}"
               class="{{ request()->routeIs('welcome') ? 'active' : '' }}">
                Beranda
            </a>
            <span>|</span>

            <a href="{{ route('guest.katalogpaketwisata') }}"
               class="{{ request()->routeIs('guest.katalogpaketwisata', 'guest.detailpaket') ? 'active' : '' }}">
                Paket Wisata
            </a>
            <span>|</span>

            <a href="{{ route('welcome') }}#tentangkami">Tentang Kami</a>
            <span>|</span>

            <a href="{{ route('welcome') }}#kontak">Kontak</a>
        </nav>

        <div class="nav-action">
            <a href="{{ route('login') }}" class="btn-login">Masuk</a>
            <a href="{{ route('register') }}" class="btn-register">Daftar</a>
        </div>

    </header>

    <main>
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>