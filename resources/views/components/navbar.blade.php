<header class="navbar">
    <div class="nav-logo">
        <img src="{{ asset('img/logo.png') }}" alt="Logo MyTrans">
    </div>

    <nav class="nav-menu">
        <a href="{{ route('dashboard.user') }}" class="{{ request()->routeIs('dashboard.user') ? 'active' : '' }}">
            Beranda
        </a>
        <span>|</span>

        <a href="{{ route('dashboard.user.katalogpaketwisata') }}" class="{{ request()->routeIs('dashboard.user.katalogpaketwisata') ? 'active' : '' }}">
            Paket Wisata
        </a>
        <span>|</span>

       <a href="{{ route('dashboard.user.booking') }}"
            class="{{ request()->routeIs('dashboard.user.booking') ? 'active' : '' }}">
            Booking
        </a>
        <span>|</span>

       <a href="{{ route('dashboard.user.riwayatbooking',) }}"
            class="{{ request()->routeIs('dashboard.user.riwayatbooking') ? 'active' : '' }}">
            Riwayat Booking
        </a>
        <span>|</span>

        <a href="{{ route('dashboard.user.profile') }}" class="{{ request()->routeIs('dashboard.user.profile', 'dashboard.user.profile-password', 'dashboard.user.profile-edit', 'dashboard.user.profile-edit-password') ? 'active' : '' }}">
            Profil
        </a>
    </nav>

     <div class="nav-action">
        @hasSection('navbar_action')
            @yield('navbar_action')
        @else
           


                <button type="button" onclick="bukaModalLogout()" class="btn-logout">Keluar</button>
            </form>
        @endif
    </div>
    

</header>

<script>
    function bukaModalLogout() {
    document.getElementById('modal-logout').classList.add('active');
}

function tutupModalLogout() {
    document.getElementById('modal-logout').classList.remove('active');
}
</script>