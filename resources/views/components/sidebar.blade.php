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
            <a href="{{ route('dashboard.beranda-admin') }}">Dashboard</a>
            @if(Auth::user()->role === 'superadmin')
            <a href="{{ route('dashboard.superadmin.kelola-pengguna') }}">Kelola pengguna</a>
            @endif
            <a href="{{ route('dashboard.superadmin.kelola-paket-wisata') }}">Kelola paket wisata</a>
            <a href="{{ route('dashboard.superadmin.kelola-request') }}">Kelola request wisata</a>
            <a href="{{ route('dashboard.superadmin.kelola-destinasi') }}">Kelola Lokasi Wisata</a>
            <a href="{{ route('dashboard.superadmin.kelola-kendaraan') }}">Kelola Kendaraan</a>
            <a href="{{ route('dashboard.superadmin.kelola-trayek') }}">Kelola Trayek</a>
            <a href="{{ route('dashboard.superadmin.kelola-data-booking') }}">Data Booking</a>
            <a href="{{ route('dashboard.superadmin.kelola-laporan-transaksi') }}">Laporan</a>
        </nav>
    </div>

    <div class="sidebar-bottom">
        <a href="{{ route('dashboard.superadmin.profile') }}" class="menu-profile">
            👤 Profil Saya
        </a>

        <!-- <form action="{{ route('logout') }}" method="POST">
            @csrf -->
            <button class="menu-logout" type="button" onclick="bukaModalLogout()">
                ⛔ Keluar
            </button>
        <!-- </form> -->
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