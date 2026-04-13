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

    <form action="{{ route('dashboard.superadmin.profile-update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <section class="profile-hero-card">
    <div class="avatar-wrapper">
        <div class="avatar-circle">
            @if(!empty($user->photo))
                <img src="{{ asset($user->photo) }}"
                     alt=""
                     class="avatar-image"
                     id="previewImage"
                     onerror="this.style.display='none'; document.getElementById('defaultAvatarIcon').style.display='flex';">
            @else
                <img src=""
                     alt=""
                     class="avatar-image"
                     id="previewImage"
                     style="display:none;">
            @endif

            <span class="avatar-icon" id="defaultAvatarIcon" style="{{ !empty($user->photo) ? 'display:none;' : 'display:flex;' }}">
                <i class="fa-solid fa-user"></i>
            </span>
        </div>

        <label for="photoInput" class="camera-icon">
            <i class="fa-solid fa-camera"></i>
        </label>

        <input type="file" id="photoInput" name="photo" accept="image/*" hidden>
    </div>

    <div class="profile-hero-name">
        <h2>{{ $user->nama }}</h2>
    </div>
</section>

        <section class="profile-tabs">
            <a href="{{ route('dashboard.superadmin.profile-edit') }}" class="tab-btn active">Ubah Informasi Pribadi</a>
            <a href="{{ route('dashboard.superadmin.profile-edit-password') }}" class="tab-btn">Ubah Password</a>
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
                <a href="{{ route('dashboard.superadmin.profile') }}" class="btn-cancel">Batal</a>
                <button type="submit" class="btn-save">
                    <i class="fa-solid fa-save"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </section>
    </form>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const photoInput = document.getElementById('photoInput');
    const previewImage = document.getElementById('previewImage');

    if (photoInput && previewImage) {
        photoInput.addEventListener('change', function (e) {
            const file = e.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function (event) {
    previewImage.src = event.target.result;
    previewImage.style.display = 'block';

    const defaultIcon = document.getElementById('defaultAvatarIcon');
    if (defaultIcon) {
        defaultIcon.style.display = 'none';
    }
};
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endpush