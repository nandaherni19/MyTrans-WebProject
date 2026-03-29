<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil Super Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin-profile.css') }}">
</head>
<body>

<div class="admin-layout">
    <aside class="sidebar">
        <div>
            <div class="sidebar-header">
                <div class="brand">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="brand-logo">
                    <div class="brand-text">
                        <h2>MY Trans Nusa</h2>
                        <p>Super Admin</p>
                    </div>
                </div>
            </div>

            <nav class="sidebar-menu">
                <a href="#">Dashboard</a>
                <a href="#">Kelola Paket Wisata dan Destinasi</a>
                <a href="{{ url('/superadmin/kelola-pengguna') }}">Kelola Pengguna</a>
                <a href="#">Kelola Kendaraan</a>
                <a href="#">Kelola Trayek</a>
                <a href="#">Data Booking</a>
                <a href="#">Laporan Transaksi</a>
            </nav>
        </div>

        <div class="sidebar-bottom">
            <a href="{{ url('/superadmin/profile') }}" class="menu-profile">👤 Profil Saya</a>
            <a href="#" class="menu-logout">⛔ Keluar</a>
        </div>
    </aside>

    <main class="main-content">
        <div class="content-header">
            <h1>Profil Saya</h1>
            <a href="{{ url('/superadmin/profile') }}" class="btn-back">← Kembali</a>
        </div>

        <form action="{{ url('/superadmin/profile/update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <section class="profile-card">
                <div class="avatar-wrapper">
                    <div class="avatar-circle">
                        @if(!empty($superadminProfile['photo']))
                            <img src="{{ asset($superadminProfile['photo']) }}" alt="Foto Profil Super Admin" class="avatar-image">
                        @else
                            <span class="avatar-icon">👤</span>
                        @endif
                    </div>

                    <label for="photoInput" class="camera-icon">📷</label>
                    <input type="file" id="photoInput" name="photo" accept="image/*" hidden onchange="this.form.submit()">
                </div>

                <div class="profile-name">
                    <h2>{{ $superadminProfile['name'] }}</h2>
                </div>
            </section>

            <section class="tab-section">
                <a href="{{ url('/superadmin/profile/edit') }}" class="tab-link active">Ubah Informasi Pribadi</a>
                <a href="{{ url('/superadmin/profile/edit/password') }}" class="tab-link">Ubah Password</a>
            </section>

            <section class="info-card">
                <div class="info-header">
                    <h3>Edit Profil</h3>
                </div>

                <div class="info-grid">
                    <div class="info-box">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" class="form-input" value="{{ old('name', $superadminProfile['name']) }}">
                        @error('name')
                            <small class="error-text">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="info-box">
                        <label>Email</label>
                        <input type="email" name="email" class="form-input" value="{{ old('email', $superadminProfile['email']) }}">
                        @error('email')
                            <small class="error-text">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="info-box">
                        <label>Nomor Telepon</label>
                        <input type="text" name="phone" class="form-input" value="{{ old('phone', $superadminProfile['phone']) }}">
                        @error('phone')
                            <small class="error-text">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="edit-button-wrapper admin-edit-actions">
                    <a href="{{ url('/superadmin/profile') }}" class="btn-cancel">Batal</a>
                    <button type="submit" class="btn-save">💾 Simpan Perubahan</button>
                </div>
            </section>
        </form>
    </main>
</div>

</body>
</html>