<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test-db', function () {
    return DB::table('ms_users')->get();
});
// register
Route::get('/register', function () {
    return view('auth.register');
})->name('register');
Route::post('/register', function () {
    request()->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:ms_users,email',
        'password' => 'required|min:6|confirmed',
    ]);
    DB::table('ms_users')->insert([
        'name' => request('name'),
        'email' => request('email'),
        'password' => Hash::make(request('password')),
    ]);
    return redirect()->route('login')->with('success', 'Pendaftaran berhasil. Silakan login.');
})->name('register.submit');

// login
Route::get('/login', function () {   
    return view('auth.login');
})->name('login');
Route::post('/login', function () {
    request()->validate([
        'email' => 'required|email',
        'password' => 'required|min:6',
    ]);
    $user = DB::table('ms_users')->where('email', request('email'))->first();
    if (!$user || !Hash::check(request('password'), $user->password)) {
        return redirect()->route('home')->with('success', 'Login berhasil');
    }
    return back()->withErrors(['email' => 'Email atau password salah']);
})->name('login.submit');

// Forgot Password
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
        // Simulate sending email by logging the reset link
        \Log::info("Password reset link: " . url('/reset-password?token=' . $token . '&email=' . request('email')));
    }
    return back()->with('status', 'Jika email Anda terdaftar, Anda akan menerima tautan reset password.');
})->name('password.email'); 

// Reset Password
Route::get('/reset-password', function () {
    return view('auth.reset-password', ['token' => request('token')]);
})->name('password.reset');
Route::post('/reset-password', function () {
    request()->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:6|confirmed',
    ]); 
    $reset = DB::table('password_resets')->where('email', request('email'))->where('token', request('token'))->first();
    if (!$reset || Carbon::parse($reset->created_at)->addMinutes(60)->isPast()) {
        return back()->withErrors(['email' => 'Token reset password tidak valid atau sudah kadaluarsa.']);
    }
    DB::table('ms_users')->where('email', request('email'))->update(['password' => Hash::make(request('password'))]);
    DB::table('password_resets')->where('email', request('email'))->delete();
    return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login dengan password baru Anda.');
})->name('password.update');

// logout
Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('home')->with('success', 'Logout berhasil');
})->name('logout'); 

// dashboard
Route::get('/home', function () {
    return view('home');
})->name('home');