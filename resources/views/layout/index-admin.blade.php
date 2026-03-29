<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            display: flex;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background: #1e293b;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar h2 {
            padding: 20px;
        }

        .menu a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
        }

        .menu a:hover {
            background: #334155;
        }

        .main {
            flex: 1;
            padding: 20px;
            background: #f1f5f9;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .logout-btn {
            background: red;
            color: white;
            border: none;
            padding: 8px 12px;
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div>
        <h2>Super Admin</h2>

        <div class="menu">
            <a href="{{ route('superadmin.users.index') }}">Kelola Pengguna</a>
            <a href="{{ route('paket.index') }}">Paket Wisata</a>
            <a href="#">Booking</a>
            <a href="#">Kendaraan</a>
        </div>
    </div>

    <div style="padding:20px;">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="logout-btn">Logout</button>
        </form>
    </div>
</div>

<!-- MAIN -->
<div class="main">
    
    <div class="topbar">
        <h3>@yield('title')</h3>
    </div>

    @yield('content')

</div>

</body>
</html>