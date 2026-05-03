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

    {{-- ===================== --}}
    {{-- HERO / AVATAR CARD   --}}
    {{-- ===================== --}}
    <section class="profile-hero-card">
        <div class="avatar-wrapper">
            <div class="avatar-circle">
                @if(!empty($user->photo))
                    <img src="{{ asset($user->photo) }}"
                         alt="Foto Profil"
                         class="avatar-image"
                         id="previewImage"
                         onerror="this.style.display='none'; document.getElementById('defaultAvatarIcon').style.display='flex';">
                @else
                    <img src="" alt="" class="avatar-image" id="previewImage" style="display:none;">
                @endif

                <span class="avatar-icon" id="defaultAvatarIcon"
                      style="{{ !empty($user->photo) ? 'display:none;' : 'display:flex;' }}">
                    <i class="fa-solid fa-user"></i>
                </span>
            </div>

            {{-- Tombol kamera hanya muncul saat mode edit personal --}}
            <label for="photoInput" class="camera-icon" id="cameraLabel" style="display:none;">
                <i class="fa-solid fa-camera"></i>
            </label>
            <input type="file" id="photoInput" name="photo" accept="image/*" hidden>
        </div>

        <div class="profile-hero-name">
            <h2>{{ $user->nama }}</h2>
        </div>
    </section>

    {{-- ===================== --}}
    {{-- TAB SWITCHER         --}}
    {{-- ===================== --}}
    <section class="profile-tabs">
        <button type="button" class="tab-btn active" id="tabPersonal" onclick="switchTab('personal')">
            <span id="labelPersonal">Informasi Pribadi</span>
        </button>
        <button type="button" class="tab-btn" id="tabPassword" onclick="switchTab('password')">
            <span id="labelPassword">Password</span>
        </button>
    </section>

    {{-- ================================ --}}
    {{-- PANEL: INFORMASI PRIBADI        --}}
    {{-- ================================ --}}
    <div id="panelPersonal">

        {{-- VIEW MODE --}}
        <div id="viewPersonal">
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
                <button type="button" class="btn-edit" onclick="enterEditMode('personal')">Edit Profil</button>
            </div>
        </div>

        {{-- EDIT MODE --}}
        <div id="editPersonal" style="display:none;">
            <form action="{{ route('dashboard.superadmin.profile-update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" id="photoInputForm" name="photo" accept="image/*" hidden>

                <section class="profile-info-card">
                    <div class="info-title">
                        <h3>Edit Profil</h3>
                        <p>Lengkapi identitas Anda</p>
                    </div>

                    <div class="info-grid">
                        <div class="info-item">
                            <label>Nama Lengkap</label>
                            <input type="text" name="name" class="form-input"
                                   value="{{ old('name', $user->nama) }}">
                            @error('name')
                                <small class="error-text">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="info-item">
                            <label>Email</label>
                            <input type="email" name="email" class="form-input"
                                   value="{{ old('email', $user->email) }}">
                            @error('email')
                                <small class="error-text">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="info-item">
                            <label>Nomor Telepon</label>
                            <input type="text" name="no_hp" class="form-input"
                                   value="{{ old('no_hp', $user->no_hp) }}">
                            @error('no_hp')
                                <small class="error-text">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="edit-action-wrapper">
                        <button type="button" class="btn-cancel" onclick="exitEditMode('personal')">Batal</button>
                        <button type="submit" class="btn-save">
                            <i class="fa-solid fa-save"></i>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </section>
            </form>
        </div>
    </div>

    {{-- ================================ --}}
    {{-- PANEL: PASSWORD                 --}}
    {{-- ================================ --}}
    <div id="panelPassword" style="display:none;">

        {{-- VIEW MODE --}}
        <div id="viewPassword">
            <section class="profile-info-card">
                <div class="info-title">
                    <h3>Password</h3>
                    <p>Keamanan akun Anda</p>
                </div>

                <div class="info-grid">
                    <div class="info-item full-width">
                        <label>Password Saat Ini</label>
                        <div class="info-box">••••••••</div>
                    </div>
                </div>
            </section>

            <section class="security-tip-card">
                <div class="security-icon">
                    <i class="fa-solid fa-shield"></i>
                </div>
                <div class="security-text">
                    <h4>Tips Keamanan</h4>
                    <p>Untuk keamanan akun Anda, pastikan menggunakan password yang kuat dan tidak membagikan informasi login Anda kepada siapapun.</p>
                </div>
            </section>

            <div class="profile-action">
                <button type="button" class="btn-edit" onclick="enterEditMode('password')">Edit Profil</button>
            </div>
        </div>

        {{-- EDIT MODE --}}
        <div id="editPassword" style="display:none;">
            <form action="{{ route('dashboard.superadmin.profile-password-update') }}" method="POST">
                @csrf

                <section class="profile-info-card">
                    <div class="info-title">
                        <h3>Ubah Password</h3>
                        <p>Perbarui keamanan akun Anda</p>
                    </div>

                    <div class="password-form-group">
                        <label>Password Saat Ini</label>
                        <div class="password-input-wrapper">
                            <span class="password-left-icon"><i class="fa-solid fa-lock"></i></span>
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
                            <span class="password-left-icon"><i class="fa-solid fa-lock"></i></span>
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
                            <span class="password-left-icon"><i class="fa-solid fa-lock"></i></span>
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
                        <button type="button" class="btn-cancel" onclick="exitEditMode('password')">Batal</button>
                        <button type="submit" class="btn-save">
                            <i class="fa-solid fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </section>
            </form>

            <section class="security-tip-card">
                <div class="security-icon">
                    <i class="fa-solid fa-shield"></i>
                </div>
                <div class="security-text">
                    <h4>Tips Keamanan</h4>
                    <p>Untuk keamanan akun Anda, pastikan menggunakan password yang kuat dan tidak membagikan informasi login Anda kepada siapapun.</p>
                </div>
            </section>
        </div>
    </div>

</main>
@endsection

@push('scripts')
<script>
    // ── State ──────────────────────────────────────────────────
    let currentTab = 'personal';
    let isEditing  = false;

    // ── Tab Switching ──────────────────────────────────────────
    function switchTab(tab) {
        if (isEditing) exitEditMode(currentTab);
        currentTab = tab;

        document.getElementById('panelPersonal').style.display = (tab === 'personal') ? 'block' : 'none';
        document.getElementById('panelPassword').style.display  = (tab === 'password')  ? 'block' : 'none';

        document.getElementById('tabPersonal').classList.toggle('active', tab === 'personal');
        document.getElementById('tabPassword').classList.toggle('active', tab === 'password');

        resetTabLabels();
    }

    // ── Enter Edit Mode ────────────────────────────────────────
    function enterEditMode(tab) {
        isEditing = true;
        currentTab = tab;

        document.getElementById('labelPersonal').textContent = 'Ubah Informasi Pribadi';
        document.getElementById('labelPassword').textContent  = 'Ubah Password';

        if (tab === 'personal') {
            document.getElementById('cameraLabel').style.display = 'flex';
            document.getElementById('viewPersonal').style.display = 'none';
            document.getElementById('editPersonal').style.display = 'block';
        } else {
            document.getElementById('viewPassword').style.display  = 'none';
            document.getElementById('editPassword').style.display  = 'block';
        }
    }

    // ── Exit Edit Mode ─────────────────────────────────────────
    function exitEditMode(tab) {
        isEditing = false;

        document.getElementById('cameraLabel').style.display = 'none';
        resetTabLabels();

        if (tab === 'personal') {
            document.getElementById('viewPersonal').style.display = 'block';
            document.getElementById('editPersonal').style.display = 'none';
        } else {
            document.getElementById('viewPassword').style.display  = 'block';
            document.getElementById('editPassword').style.display  = 'none';
        }
    }

    // ── Reset Label Tab ke Default ─────────────────────────────
    function resetTabLabels() {
        document.getElementById('labelPersonal').textContent = 'Informasi Pribadi';
        document.getElementById('labelPassword').textContent  = 'Password';
    }

    // ── Toggle Show/Hide Password ──────────────────────────────
    function togglePassword(id, btn) {
        const input = document.getElementById(id);
        const icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    // ── Preview Foto Avatar ────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        const photoInput   = document.getElementById('photoInput');
        const previewImage = document.getElementById('previewImage');
        const defaultIcon  = document.getElementById('defaultAvatarIcon');

        photoInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            // Salin file ke input form agar ikut ter-submit
            const photoInputForm = document.getElementById('photoInputForm');
            const dt = new DataTransfer();
            dt.items.add(file);
            photoInputForm.files = dt.files;

            // Preview gambar
            const reader = new FileReader();
            reader.onload = function (event) {
                previewImage.src = event.target.result;
                previewImage.style.display = 'block';
                if (defaultIcon) defaultIcon.style.display = 'none';
            };
            reader.readAsDataURL(file);
        });
    });

    // ── Auto-open tab & mode jika ada error validasi ───────────
    @if($errors->has('current_password') || $errors->has('new_password') || $errors->has('new_password_confirmation'))
        switchTab('password');
        enterEditMode('password');
    @elseif($errors->has('name') || $errors->has('email') || $errors->has('no_hp'))
        enterEditMode('personal');
    @endif
</script>
@endpush