<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin-profile.css') }}">
</head>
<body>

<div class="admin-layout">
    <aside class="sidebar">
        <div>
            <div class="sidebar-header">
                <div class="brand">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="brand-logo">
                    <div class="brand-text">
                        <h2>MY Trans Nusa</h2>
                        <p>Super admin</p>
                    </div>
                </div>
            </div>

            <nav class="sidebar-menu">
                <a href="{{ route('dashboard.admin') }}">Dashboard</a>
                <a href="{{ route('dashboard.superadmin.kelola-pengguna') }}" class="active">Kelola pengguna</a>
                <a href="{{ route('dashboard.superadmin.kelola-paket-wisata') }}">Kelola paket wisata dan destinasi</a>
                <a href="{{ route('dashboard.superadmin.request-booking') }}">Request Booking</a>
                <a href="{{ route('dashboard.superadmin.kelola-kendaraan') }}">Kelola Kendaraan</a>
                <a href="{{ route('dashboard.superadmin.kelola-trayek') }}">Kelola Trayek</a>
                <a href="{{ route('dashboard.superadmin.data-booking') }}">Data Booking</a>
                <a href="{{ route('dashboard.superadmin.laporan-transaksi') }}">Laporan Transaksi</a>
            </nav>
        </div>

        <div class="sidebar-bottom">
            <a href="{{ route('dashboard.superadmin.profile') }}" class="menu-profile"><i class="fa-solid fa-user"></i> Profil Saya</a>
            <form action="{{ route('logout') }}" method="POST" style="margin-top:20px;">
            @csrf
            <button type="submit" style="background:none;border:none;color:#ff2800;cursor:pointer;">
                <i class="fa-solid fa-circle-minus"></i> Logout
            </button>
            </form>
        </div>
    </aside>
    
    <main class="main-content">
        <div class="content-header">
            <h1>Profil Saya</h1>
            <a href="{{ url('/admin/profile') }}" class="btn-back">← Kembali</a>
        </div>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <section class="profile-card">
            <div class="avatar-wrapper">
            <div class="avatar-circle">
    @if(!empty($adminProfile['photo']))
        <img src="{{ asset($adminProfile['photo']) }}" alt="Foto Profil Admin" class="avatar-image">
    @else
        <span class="avatar-icon">👤</span>
    @endif
</div>
            </div>

            <div class="profile-name">
                <h2>{{ $adminProfile['name'] }}</h2>
            </div>
        </section>

        <section class="tab-section">
            <a href="{{ url('/admin/profile') }}" class="tab-link active">Informasi Pribadi</a>
            <a href="{{ url('/admin/profile/password') }}" class="tab-link">Password</a>
        </section>

        <section class="info-card">
            <div class="info-header">
                <h3>Profil</h3>
            </div>

            <div class="info-grid">
                <div class="info-box">
                    <label>Nama Lengkap</label>
                    <div class="input-like">{{ $adminProfile['name'] }}</div>
                </div>

                <div class="info-box">
                    <label>Email</label>
                    <div class="input-like">{{ $adminProfile['email'] }}</div>
                </div>

                <div class="info-box">
                    <label>Nomor Telepon</label>
                    <div class="input-like">{{ $adminProfile['phone'] }}</div>
                </div>
            </div>
        </section>

        <div class="edit-button-wrapper">
            <a href="{{ url('/admin/profile/edit') }}" class="btn-edit">Edit Profil</a>
        </div>
    </main>
</div>


</body>
</html>