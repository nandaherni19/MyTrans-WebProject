@extends('layouts.user')

@section('title', 'Edit Profil')

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

    <form action="{{ route('dashboard.user.profile-update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <section class="profile-hero-card">
    <div class="profile-hero-left">
        <div class="avatar-wrapper">
            <div class="avatar-circle">
                <img src="{{ !empty($user->photo) ? asset($user->photo) : '' }}"
                     alt=""
                     class="avatar-image"
                     id="previewImage"
                     style="{{ !empty($user->photo) ? 'display:block;' : 'display:none;' }}">

                <span class="avatar-icon" id="defaultAvatarIcon" style="{{ !empty($user->photo) ? 'display:none;' : 'display:flex;' }}">
                    <i class="fa-solid fa-user"></i>
                </span>
            </div>

            <label for="photoInput" class="camera-icon">
                <i class="fa-solid fa-camera"></i>
            </label>

            <input type="file" id="photoInput" name="photo" accept="image/*" hidden>
        </div>
    </div>

    <div class="profile-hero-right">
        <h2>{{ $user->nama }}</h2>
    </div>
</section>

        <section class="profile-tabs">
            <a href="{{ route('dashboard.user.profile-edit') }}" class="tab-btn active">Ubah Informasi Pribadi</a>
            <a href="{{ route('dashboard.user.profile-edit-password') }}" class="tab-btn">Ubah Password</a>
        </section>

        <section class="profile-info-card">
            <div class="info-title">
                <h3>Edit Profil</h3>
                <p>Lengkapi identitas Anda</p>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name', $user->nama) }}">
                    @error('name')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="info-item">
                    <label>Email</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}">
                    @error('email')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="info-item">
                    <label>Nomor Telepon</label>
                    <input type="text" name="no_hp" class="form-input" value="{{ old('no_hp', $user->no_hp) }}">
                    @error('no_hp')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="edit-action-wrapper">
                <a href="{{ route('dashboard.user.profile') }}" class="btn-cancel">Batal</a>
               <button type="submit" class="btn-save">
    <i class="fa-solid fa-save"></i>
    <span>Simpan Perubahan</span>
</button>
        </section>
    </form>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const photoInput = document.getElementById('photoInput');
    const previewImage = document.getElementById('previewImage');
    const defaultAvatarIcon = document.getElementById('defaultAvatarIcon');

    photoInput.addEventListener('change', function (event) {
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();

            reader.onload = function (e) {
                previewImage.src = e.target.result;
                previewImage.style.display = 'block';
                defaultAvatarIcon.style.display = 'none';
            };

            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush