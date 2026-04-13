<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MyTrans')</title>

    <link rel="stylesheet" href="{{ asset('css/user/navbar.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @stack('styles')
    <style>
        #modal-logout {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(0, 0, 0, 0.45);
            align-items: center;
            justify-content: center;
        }
        #modal-logout.active {
            display: flex;
        }
        .modal-logout-box {
            background: #fff;
            border-radius: 14px;
            padding: 2rem 1.75rem;
            max-width: 360px;
            width: 90%;
            text-align: center;
        }
        .modal-logout-icon {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #fef2f2;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        .modal-logout-icon i {
            font-size: 20px;
            color: #e24b4a;
        }
        .modal-logout-box h2 {
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 0.4rem;
            color: #1a1a1a;
            font-family: 'Poppins', sans-serif;
        }
        .modal-logout-box p {
            font-size: 13px;
            color: #888;
            margin: 0 0 1.5rem;
            line-height: 1.6;
            font-family: 'Poppins', sans-serif;
        }
        .modal-logout-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .btn-logout-cancel {
            padding: 8px 24px;
            border-radius: 8px;
            border: 1px solid #ddd;
            background: transparent;
            cursor: pointer;
            font-size: 13px;
            font-family: 'Poppins', sans-serif;
            color: #555;
            transition: background 0.15s;
        }
        .btn-logout-cancel:hover {
            background: #f5f5f5;
        }
        .btn-logout-confirm {
            padding: 8px 24px;
            border-radius: 8px;
            border: none;
            background: #e24b4a;
            color: #fff;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            transition: background 0.15s;
        }
        .btn-logout-confirm:hover {
            background: #c93b3a;
        }
    </style>


</head>
<body>

    @include('components.navbar')

    <main>
        @yield('content')
    </main>

    {{-- MODAL KONFIRMASI LOGOUT --}}
<div id="modal-logout">
    <div class="modal-logout-box">
        <div class="modal-logout-icon">
            <i class="fa-solid fa-right-from-bracket"></i>
        </div>
        <h2>Keluar dari akun</h2>
        <p>Yakin ingin keluar sekarang?</p>
        <div class="modal-logout-actions">
            <button class="btn-logout-cancel" onclick="tutupModalLogout()">Batal</button>
            <button class="btn-logout-confirm" onclick="document.getElementById('form-logout').submit()">Ya, Keluar</button>
        </div>
    </div>
</div>

{{-- FORM LOGOUT TERSEMBUNYI --}}
<form id="form-logout" method="POST" action="{{ route('logout') }}" style="display:none;">
    @csrf
</form>

@stack('scripts')
<script>
    function bukaModalLogout() {
        document.getElementById('modal-logout').classList.add('active');
    }

    function tutupModalLogout() {
        document.getElementById('modal-logout').classList.remove('active');
    }

    // Klik di luar modal = tutup
    document.getElementById('modal-logout').addEventListener('click', function(e) {
        if (e.target === this) tutupModalLogout();
    });

    // Tekan ESC = tutup
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') tutupModalLogout();
    });
</script>
  
</body>
</html>