@extends('layouts.user')

@section('title', 'Edit Password Saya')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user/profile.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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

    <section class="profile-hero-card">
    <div class="profile-hero-left">
        <div class="avatar-wrapper">
            <div class="avatar-circle">
                @if(!empty($user->photo))
                    <img src="{{ asset($user->photo) }}" alt="" class="avatar-image">
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
        <a href="{{ route('dashboard.user.profile-edit') }}" class="tab-btn">Ubah Informasi Pribadi</a>
        <a href="{{ route('dashboard.user.profile-edit-password') }}" class="tab-btn active">Ubah Password</a>
    </section>

    <section class="profile-info-card">
        <div class="info-title">
            <h3>Ubah Password</h3>
            <p>Perbarui keamanan akun Anda</p>
        </div>

        <form action="{{ route('dashboard.user.profile-password-update') }}" method="POST">
            @csrf

            <div class="password-form-group">
                <label>Password Saat Ini</label>
                <div class="password-input-wrapper">
                    <span class="password-left-icon">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" id="currentPassword" name="current_password">
                    <button type="button" class="toggle-password" onclick="togglePassword('currentPassword', this)">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
                @error('current_password')
                    <small class="error-text">{{ $message }}</small>
                @enderror
            </div>

            <div class="password-form-group">
                <label>Password Baru</label>
                <div class="password-input-wrapper">
                    <span class="password-left-icon">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" id="newPassword" name="new_password" value="{{ old('new_password') }}">
                    <button type="button" class="toggle-password" onclick="togglePassword('newPassword', this)">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
                @error('new_password')
                    <small class="error-text">{{ $message }}</small>
                @enderror
            </div>

            <div class="password-form-group">
                <label>Konfirmasi Password Baru</label>
                <div class="password-input-wrapper">
                    <span class="password-left-icon">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" id="confirmPassword" name="new_password_confirmation" value="{{ old('new_password_confirmation') }}">
                    <button type="button" class="toggle-password" onclick="togglePassword('confirmPassword', this)">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
                @error('new_password_confirmation')
                    <small class="error-text">{{ $message }}</small>
                @enderror
            </div>

            <div class="edit-action-wrapper">
                <a href="{{ route('dashboard.user.profile') }}" class="btn-cancel">Batal</a>
                <button type="submit" class="btn-save">
                    <i class="fa-solid fa-save"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </section>

    <section class="security-tip-card">
        <div class="security-icon">
            <i class="fa-solid fa-shield"></i>
        </div>
        <div class="security-text">
            <h4>Tips Keamanan</h4>
            <p>
                Untuk keamanan akun Anda, pastikan menggunakan password yang kuat dan tidak membagikan informasi login Anda kepada siapapun.
            </p>
        </div>
    </section>
</main>
@endsection

@push('scripts')
<script>
    function togglePassword(id, btn) {
        const input = document.getElementById(id);
        const icon = btn.querySelector("i");

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = 'password';
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
</script>
@endpush