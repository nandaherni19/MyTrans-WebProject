<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

// home
Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

Route::get('/test-db', function () {
    return DB::table('ms_users')->get();
});

// ================= REGISTER =================
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', function () {
    request()->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:ms_users,email',
        'password' => 'required|min:6|confirmed',
        'phone_number' => 'required|string|max:20',
    ]);

    DB::table('ms_users')->insert([
        'nama' => request('name'),
        'email' => request('email'),
        'password' => Hash::make(request('password')),
        'no_hp' => request('phone_number'),
        'role' => 'user',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()->route('login')->with('success', 'Pendaftaran berhasil. Silakan login.');
})->name('register.submit');

// ================= LOGIN =================
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    request()->validate([
        'email' => 'required|email',
        'password' => 'required|min:6',
    ]);

    $user = DB::table('ms_users')
        ->where('email', request('email'))
        ->first();

    if (!$user || !Hash::check(request('password'), $user->password)) {
        return back()->withErrors(['email' => 'Email atau password salah']);
    }

    // LOGIN MANUAL
    session(['user_id' => $user->id_users]);

    // REDIRECT KE DASHBOARD SESUAI ROLE
    if ($user->role === 'superadmin' || $user->role === 'admin') {
        return redirect()->route('dashboard.admin')->with('success', 'Login berhasil');
    } else {
        return redirect()->route('dashboard.user')->with('success', 'Login berhasil');
    }
})->name('login.submit');

// ================= DASHBOARD ADMIN =================
Route::get('/dashboard/admin', function () {
    return view('dashboard.admin');
})->name('dashboard.admin')->middleware('auth');    

// ================= DASHBOARD USER =================
Route::get('/dashboard/user', function () {
    return view('dashboard.user');
})->name('dashboard.user')->middleware('auth');

// ================= FORGOT PASSWORD =================
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::post('/forgot-password', function () {
    request()->validate(['email' => 'required|email']);

    $user = DB::table('ms_users')->where('email', request('email'))->first();

    if ($user) {
        $token = Str::random(60);

        DB::table('password_resets')->insert([
            'email' => request('email'),
            'token' => $token,
            'created_at' => now(),
        ]);

        $link = url('/reset-password?token=' . $token . '&email=' . request('email'));

        Mail::raw("Klik link berikut untuk reset password:\n\n" . $link, function ($message) {
            $message->to(request('email'))
                ->subject('Reset Password MyTrans');
});
    }

    return back()->with('status', 'Cek email untuk reset password.');
})->name('password.email');

// ================= RESET PASSWORD =================
Route::get('/reset-password', function () {
    return view('auth.reset-password', ['token' => request('token')]);
})->name('password.reset');

Route::post('/reset-password', function () {
    request()->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:6|confirmed',
    ]);

    $reset = DB::table('password_resets')
        ->where('email', request('email'))
        ->where('token', request('token'))
        ->first();

    if (!$reset || Carbon::parse($reset->created_at)->addMinutes(60)->isPast()) {
        return back()->withErrors(['email' => 'Token tidak valid / expired']);
    }

    DB::table('ms_users')
        ->where('email', request('email'))
        ->update(['password' => Hash::make(request('password'))]);

    DB::table('password_resets')
        ->where('email', request('email'))
        ->delete();

    return redirect()->route('login')->with('success', 'Password berhasil direset');
})->name('password.update');

// ================= LOGOUT =================
Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('home')->with('success', 'Logout berhasil');
})->name('logout');

// ================= DASHBOARD =================
Route::get('/home', function () {
    return view('home');
})->name('home');

// ================= TAMBAH ADMIN =================
Route::get('/superadmin/tambah-admin', function () {

    if(!session('user_id')) {
        return redirect()->route('login')->withErrors(['email' => 'Harus login dulu']);
    }

    $user = DB::table('ms_users')
        ->where('id_users', session('user_id'))
        ->first();

    if(!$user || $user->role !== 'superadmin') {
        return redirect()->route('login')->withErrors(['email' => 'Akses ditolak']);
    }

    return view('dashboard.superadmin.tambah-admin');

})->name('superadmin.tambah-admin');

Route::post('/superadmin/tambah-admin', function () {

    if(!session('user_id')) {
        return redirect()->route('login')->withErrors(['email' => 'Harus login dulu']);
    }

    $user = DB::table('ms_users')
        ->where('id_users', session('user_id'))
        ->first();

    if(!$user || $user->role !== 'superadmin') {
        return redirect()->route('login')->withErrors(['email' => 'Akses ditolak']);
    }

    request()->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:ms_users,email',
        'password' => 'required|min:6|confirmed',
        'phone_number' => 'required|string|max:20',
    ]);

    DB::table('ms_users')->insert([
        'nama' => request('name'),
        'email' => request('email'),
        'password' => Hash::make(request('password')),
        'no_hp' => request('phone_number'),
        'role' => 'admin',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()->route('superadmin.tambah-admin')->with('success', 'Admin berhasil ditambahkan');
})->name('superadmin.tambah-admin');