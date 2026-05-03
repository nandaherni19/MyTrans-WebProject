<!-- tombol  -->
<button class="menu-toggle" onclick="toggleSidebar()">☰</button>

<aside class="sidebar">
    <div>
        <div class="sidebar-header">
            <div class="brand">
                <img src="{{ asset('img/logo.png') }}" class="brand-logo">
                <div class="brand-text">
                    <h2>MY Trans Nusa</h2>
                    <p>{{ ucfirst(Auth::user()->role) }}</p>
                </div>
            </div>
        </div>

        <nav class="sidebar-menu">
            <a href="{{ route('dashboard.beranda-admin') }}"
                class="{{ request()->routeIs('dashboard.beranda-admin') ? 'active' : '' }}">
                Dashboard
                </a>
            @if(Auth::user()->role === 'superadmin')
            <a href="{{ route('dashboard.superadmin.kelola-pengguna') }}"
                class="{{ request()->routeIs('dashboard.superadmin.kelola-pengguna') ? 'active' : '' }}">
                Kelola pengguna
            </a>
            @endif
            <a href="{{ route('dashboard.superadmin.kelola-paket-wisata') }}"
                class="{{ request()->routeIs('dashboard.superadmin.kelola-paket-wisata') ? 'active' : '' }}">
                Kelola paket wisata
            </a>
            <a href="{{ route('dashboard.superadmin.kelola-destinasi') }}"
                class="{{ request()->routeIs('dashboard.superadmin.kelola-destinasi') ? 'active' : '' }}">
                Kelola Lokasi Wisata
            </a>
            <a href="{{ route('dashboard.superadmin.kelola-kendaraan') }}"
                class="{{ request()->routeIs('dashboard.superadmin.kelola-kendaraan') ? 'active' : '' }}">
                Kelola Kendaraan
            </a>
            <a href="{{ route('dashboard.superadmin.kelola-data-booking') }}"
                class="{{ request()->routeIs('dashboard.superadmin.kelola-data-booking') ? 'active' : '' }}">
                Data Booking
            </a>
            <a href="{{ route('dashboard.superadmin.kelola-laporan-transaksi') }}"
                class="{{ request()->routeIs('dashboard.superadmin.kelola-laporan-transaksi') ? 'active' : '' }}">
                Laporan
            </a>
        </nav>
    </div>

    <div class="sidebar-bottom">
        <a href="{{ route('dashboard.superadmin.profile') }}"
            class="menu-profile {{ request()->routeIs('dashboard.superadmin.profile') ? 'active-bottom' : '' }}">
            👤 Profil Saya
            </a>

            <button class="menu-logout" type="button" onclick="bukaModalLogout()">
                ⛔ Keluar
            </button>
        </div>
</aside>

<!-- overlay gelap -->
<div class="sidebar-overlay" onclick="toggleSidebar()"></div>

<script>
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('active');
    document.querySelector('.sidebar-overlay').classList.toggle('active');
}
function bukaModalLogout() {
    document.getElementById('modal-logout').classList.add('active');
}

function tutupModalLogout() {
    document.getElementById('modal-logout').classList.remove('active');
}
</script>