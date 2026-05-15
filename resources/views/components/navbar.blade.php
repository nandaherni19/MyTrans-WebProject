<header class="navbar">
    <div class="nav-logo">
        <img src="{{ asset('img/logo.png') }}">
        <span class="nav-logo-text">MyTrans</span>
    </div>

    <div class="burger" id="burger">
        ☰
    </div>

    <nav class="nav-menu" id="navMenu">

        @auth

            <a href="{{ route('dashboard.user') }}"
                class="{{ request()->routeIs('dashboard.user') ? 'active' : '' }}">
                Beranda
            </a>

            <a href="{{ route('dashboard.user.katalogpaketwisata') }}"
                class="{{ request()->routeIs('dashboard.user.katalogpaketwisata*') ? 'active' : '' }}">
                Paket Wisata
            </a>

            <a href="{{ route('dashboard.user.riwayatbooking') }}"
                class="{{ request()->routeIs('dashboard.user.riwayatbooking*') ? 'active' : '' }}">
                Riwayat
            </a>

            <a href="{{ route('dashboard.user.profile') }}"
                class="{{ request()->routeIs('dashboard.user.profile*') ? 'active' : '' }}">
                Profil
            </a>

        @else

            <a href="{{ route('welcome') }}"
                class="{{ request()->routeIs('welcome') ? 'active' : '' }}">
                Beranda
            </a>

            <a href="{{ route('guest.katalogpaketwisata') }}"
                class="{{ request()->routeIs('guest.katalogpaketwisata*') ? 'active' : '' }}">
                Paket Wisata
            </a>

            <a href="{{ route('welcome') }}#tentang">
                Tentang
            </a>

            <a href="{{ route('welcome') }}#kontak">
                Kontak
            </a>

        @endauth

        <div class="nav-right">
            @auth

                <button onclick="bukaModalLogout()" class="btn-nav-login">
                    Keluar
                </button>

            @else

                <a href="{{ route('login') }}" class="btn-nav-login">
                    Masuk
                </a>

                <a href="{{ route('register') }}" class="btn-nav-register">
                    Daftar
                </a>

            @endauth
        </div>

    </nav>
</header>

<script>
    const burger = document.getElementById('burger');
    const navMenu = document.getElementById('navMenu');

    burger.addEventListener('click', () => {
        navMenu.classList.toggle('active');
    });

    function bukaModalLogout() {
        document.getElementById('modal-logout').classList.add('active');
    }

    function tutupModalLogout() {
        document.getElementById('modal-logout').classList.remove('active');
    }
</script>