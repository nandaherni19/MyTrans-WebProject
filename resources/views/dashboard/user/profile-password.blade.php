@extends('layouts.user')

@section('title', 'Edit Password Saya')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user/profile.css') }}">

@endpush

@section('navbar_action')
    <a href="{{ route('dashboard.user.profile') }}" class="btn-kembali">← Kembali</a>
@endsection


@section('content')
<main class="profile-page">

    <section class="page-header">
        <h1>Profil Saya</h1>
        <p>Kelola informasi profil Anda dengan mudah</p>
    </section>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <!-- HERO -->
    <section class="profile-hero-card">
        <div class="profile-hero-left">
            <div class="avatar-wrapper">
                <div class="avatar-circle">
    @if(!empty($user->photo))
        <img src="{{ asset($user->photo) }}" alt="Foto Profil" class="avatar-image">
    @else
        <span class="avatar-icon">👤</span>
    @endif
</div>
            </div>
        </div>

        <div class="profile-hero-right">
            <h2>{{ $user->nama }}</h2>
        </div>
    </section>

    <!-- TAB -->
    <section class="profile-tabs">
        <a href="{{ route('dashboard.user.profile') }}" class="tab-btn">Informasi Pribadi</a>
        <a href="{{ route('dashboard.user.profile-password') }}" class="tab-btn active">Password</a>
    </section>

    <!-- PASSWORD CARD -->
    <section class="profile-info-card">
        <div class="info-title">
            <h3>Password</h3>
            <p>Keamanan akun Anda</p>
        </div>

<div class="info-grid">
    <div class="info-item password-full">
        <label>Password Saat Ini</label>
        <div class="password-input-wrapper">
            <input type="password" name="current_password" value="........" readonly>
        </div>
    </div>
</div>
    </section>

    <!-- SECURITY TIP -->
    <section class="security-tip-card">
        <div class="security-icon">🛡️</div>
        <div class="security-text">
            <h4>Tips Keamanan</h4>
            <p>
                Untuk keamanan akun Anda, pastikan menggunakan password yang kuat dan tidak
                membagikan informasi login Anda kepada siapapun.
            </p>
        </div>
    </section>

    <div class="profile-action">
        <a href="{{ route('dashboard.user.profile-edit') }}" class="btn-edit">Edit Profil</a>
    </div>
</main>

@endsection

@push('scripts')
<script>
    console.log('Halaman beranda loaded');
</script>
@endpush

