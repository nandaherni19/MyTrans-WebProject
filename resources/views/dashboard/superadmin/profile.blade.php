@extends('layouts.admin')
@section('title', 'Profil Pengguna')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/profile.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
@endpush

@section('content')
    <section class="page-header">
        <h1>Profil Saya</h1>
        <p>Kelola informasi profil Anda dengan mudah</p>
    </section>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <section class="profile-hero-card">
        <div class="profile-hero-left">
            <div class="avatar-wrapper">
                <div class="avatar-circle">
                    @if(!empty($user->photo))
                    <img src="{{ asset($user->photo) }}" alt="Foto Profil" class="avatar-image">
                    @else
                    <span class="avatar-icon">
            <i class="fa-solid fa-user"></i>
        </span>
    @endif
</div>
            </div>
        </div>

        <div class="profile-hero-right">
            <h2>{{ $user->nama }}</h2>
        </div>
    </section>

    <section class="profile-tabs">
        <a href="{{ route('dashboard.superadmin.profile') }}" class="tab-btn active">Informasi Pribadi</a>
        <a href="{{ route('dashboard.superadmin.profile-password') }}" class="tab-btn">Password</a>
    </section>

    <section class="profile-info-card">
        <div class="info-title">
            <h3>Profil</h3>
            <p>Identitas Anda</p>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <label>Nama Lengkap</label>
                <div class="info-box">{{ $user->nama }}</div>
            </div>

            <div class="info-item">
                <label>Email</label>
                <div class="info-box">{{ $user->email }}</div>
            </div>

            <div class="info-item">
                <label>Nomor Telepon</label>
                <div class="info-box">{{ $user->no_hp }}</div>
            </div>
        </div>
    </section>

    <div class="profile-action">
        <a href="{{ route('dashboard.superadmin.profile-edit') }}" class="btn-edit">Edit Profil</a>
    </div>
</main>
@endsection

@push('scripts')
<script>
    console.log('Halaman beranda loaded');
</script>
@endpush