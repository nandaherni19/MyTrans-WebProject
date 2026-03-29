<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\SuperAdmin\UserManagementController;

// home
Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

// ================= LOGOUT =================
Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('welcome')->with('success', 'Logout berhasil');
})->name('logout');

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

    // login auth
    Auth::loginUsingId($user->id_users);

    // REDIRECT KE DASHBOARD SESUAI ROLE
    if ($user->role === 'superadmin' || $user->role === 'admin') {
        return redirect()->route('dashboard.admin')->with('success', 'Login berhasil');
    } else {
        return redirect()->route('dashboard.user')->with('success', 'Login berhasil');
    }
})->name('login.submit');

// ================= FORGOT PASSWORD =================
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::post('/forgot-password', function () {
    request()->validate(['email' => 'required|email']);

    $user = DB::table('ms_users')->where('email', request('email'))->first();

    if (!$user) {
        return back()->withErrors([
            'email' => 'Email tidak ditemukan'
        ]);
    }

    $token = Str::random(60);

    DB::table('password_resets')->insert([
        'email' => request('email'),
        'token' => $token,
        'created_at' => now(),
    ]);

    $link = url('/reset-password?token=' . $token . '&email=' . request('email'));

    Mail::raw("
Halo,
Kami menerima permintaan untuk mereset kata sandi akun Anda di MyTrans Travels.

Untuk melanjutkan proses, silakan klik tautan di bawah ini:

" . url('/reset-password?token=' . $token . '&email=' . request('email')) . "

⚠️ Link ini hanya berlaku selama 60 menit demi keamanan akun Anda.

Jika Anda tidak melakukan permintaan ini, silakan abaikan email ini. Akun Anda tetap aman.

Terima Kasih,
Tim MyTrans Travels
    ", function ($message) {
    $message->to(request('email'))
            ->subject('Reset Password MyTrans');
    });

    return back()->with('success', 'Link reset sudah dikirim ke email!');
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

// ================= DASHBOARD ADMIN DAN SUPERADMIN=================
Route::get('/dashboard/admin', function () {
    return view('dashboard.admin');
})->name('dashboard.admin')->middleware('auth');   

// DASHBOARD SUPERADMIN - KELOLA PENGGUNA
Route::prefix('/dashboard/superadmin/kelola-pengguna')->controller(UserManagementController::class)->middleware('auth')->group(function () {
    Route::get('/', 'index')->name('dashboard.superadmin.kelola-pengguna');
    Route::post('/store', 'store')->name('dashboard.superadmin.kelola-pengguna.store');
    Route::put('/update/{id}', 'update')->name('dashboard.superadmin.kelola-pengguna.update');
    Route::delete('/delete/{id}', 'destroy')->name('dashboard.superadmin.kelola-pengguna.delete');
});

Route::get('/dashboard/superadmin/kelola-paket-wisata', fn() => 'Coming Soon')
    ->name('dashboard.superadmin.kelola-paket-wisata');

Route::get('/dashboard/superadmin/request-booking', fn() => 'Coming Soon')
    ->name('dashboard.superadmin.request-booking');

Route::get('/dashboard/superadmin/kelola-kendaraan', fn() => 'Coming Soon')
    ->name('dashboard.superadmin.kelola-kendaraan');

Route::get('/dashboard/superadmin/kelola-trayek', fn() => 'Coming Soon')
    ->name('dashboard.superadmin.kelola-trayek');

Route::get('/dashboard/superadmin/data-booking', fn() => 'Coming Soon')
    ->name('dashboard.superadmin.data-booking');

Route::get('/dashboard/superadmin/laporan-transaksi', fn() => 'Coming Soon')
    ->name('dashboard.superadmin.laporan-transaksi');

Route::get('/superadmin/profile', function () {
    $user = Auth::user();
    return view('dashboard.superadmin.profile', compact('user'));
})->middleware('auth')->name('dashboard.superadmin.profile');

// ================= DASHBOARD USER =================
Route::get('/dashboard/user', function () {
    return view('dashboard.user');
})->name('dashboard.user')->middleware('auth');

// DASHBOARD USER - KATALOG PAKET WISATA & DETAIL PAKET 
Route::get('/dashboard/user/katalogpaketwisata', function () {
    return view('dashboard.user.katalogpaketwisata');
})->name('dashboard.user.katalogpaketwisata');

// DASHBOARD USER - DETAIL PAKET 
Route::get('/dashboard/user/detailpaket', function () {
    return view('dashboard.user.detailpaket');
})->name('dashboard.user.detailpaket');

Route::get('/test-db', function () {
    return DB::table('ms_users')->get();
});

/*
|--------------------------------------------------------------------------
| DATA SESSION DEFAULT - USER
|--------------------------------------------------------------------------
*/

function defaultProfile()
{
    return session('profile', [
        'name' => 'Asha Farasya',
        'email' => 'ashafarasya21@gmail.com',
        'phone' => '089512345789',
        'address' => 'Madiun, Jawa Timur',
        'birthdate' => '1 Juni 2006',
        'photo' => null,
    ]);
}

function defaultPassword()
{
    return session('password_data', [
        'current_password' => 'password123',
    ]);
}

/*
|--------------------------------------------------------------------------
| USER - HALAMAN TAMPILAN
|--------------------------------------------------------------------------
*/

Route::get('/profile', function () {
    $profile = defaultProfile();
    return view('profile', compact('profile'));
});

Route::get('/profile/password', function () {
    $profile = defaultProfile();
    $passwordData = defaultPassword();
    return view('profile-password', compact('profile', 'passwordData'));
});

/*
|--------------------------------------------------------------------------
| USER - HALAMAN EDIT
|--------------------------------------------------------------------------
*/

Route::get('/profile/edit', function () {
    $profile = defaultProfile();
    return view('profile-edit', compact('profile'));
});

Route::get('/profile/edit/password', function () {
    $profile = defaultProfile();
    $passwordData = defaultPassword();
    return view('profile-edit-password', compact('profile', 'passwordData'));
});

/*
|--------------------------------------------------------------------------
| USER - UPDATE INFORMASI PRIBADI
|--------------------------------------------------------------------------
*/

Route::post('/profile/update', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'required|string|max:255',
        'birthdate' => 'required|string|max:255',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $profile = defaultProfile();

    $profile['name'] = $validated['name'];
    $profile['email'] = $validated['email'];
    $profile['phone'] = $validated['phone'];
    $profile['address'] = $validated['address'];
    $profile['birthdate'] = $validated['birthdate'];

    if ($request->hasFile('photo')) {
        $file = $request->file('photo');
        $filename = time() . '_' . $file->getClientOriginalName();

        $file->move(public_path('uploads/profile'), $filename);

        $profile['photo'] = 'uploads/profile/' . $filename;
    }

    session(['profile' => $profile]);

    return redirect('/profile')->with('success', 'Profil berhasil diperbarui');
});

/*
|--------------------------------------------------------------------------
| USER - UPDATE PASSWORD
|--------------------------------------------------------------------------
*/

Route::post('/profile/password/update', function (Request $request) {
    $passwordData = defaultPassword();

    $validated = $request->validate([
        'current_password' => 'required|string',
        'new_password' => 'required|string|min:3',
        'confirm_password' => 'required|string|same:new_password',
    ]);

    if ($validated['current_password'] !== $passwordData['current_password']) {
        return back()->withErrors([
            'current_password' => 'Password saat ini tidak sesuai.',
        ])->withInput();
    }

    session([
        'password_data' => [
            'current_password' => $validated['new_password'],
        ]
    ]);

    return redirect('/profile/password')->with('success', 'Password berhasil diperbarui');
});

/*
|--------------------------------------------------------------------------
| DATA SESSION DEFAULT - ADMIN
|--------------------------------------------------------------------------
*/

function defaultAdminProfile()
{
    return session('admin_profile', [
        'name' => 'Super Admin',
        'email' => 'superadmin21@gmail.com',
        'phone' => '089512345789',
        'photo' => null,
    ]);
}

function defaultAdminPassword()
{
    return session('admin_password', [
        'current_password' => 'password123',
    ]);
}

/*
|--------------------------------------------------------------------------
| ADMIN - HALAMAN TAMPILAN
|--------------------------------------------------------------------------
*/

Route::get('/admin/profile', function () {
    $adminProfile = defaultAdminProfile();
    return view('admin-profile', compact('adminProfile'));
});

Route::get('/admin/profile/password', function () {
    $adminProfile = defaultAdminProfile();
    $adminPassword = defaultAdminPassword();
    return view('admin-profile-password', compact('adminProfile', 'adminPassword'));
});

/*
|--------------------------------------------------------------------------
| ADMIN - HALAMAN EDIT
|--------------------------------------------------------------------------
*/

Route::get('/admin/profile/edit', function () {
    $adminProfile = defaultAdminProfile();
    return view('admin-profile-edit', compact('adminProfile'));
});

Route::get('/admin/profile/edit/password', function () {
    $adminProfile = defaultAdminProfile();
    $adminPassword = defaultAdminPassword();
    return view('admin-profile-edit-password', compact('adminProfile', 'adminPassword'));
});

/*
|--------------------------------------------------------------------------
| ADMIN - UPDATE INFORMASI PRIBADI
|--------------------------------------------------------------------------
*/

Route::post('/admin/profile/update', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $adminProfile = defaultAdminProfile();

    $adminProfile['name'] = $validated['name'];
    $adminProfile['email'] = $validated['email'];
    $adminProfile['phone'] = $validated['phone'];

    if ($request->hasFile('photo')) {
        $file = $request->file('photo');
        $filename = time() . '_' . $file->getClientOriginalName();

        $file->move(public_path('uploads/profile'), $filename);

        $adminProfile['photo'] = 'uploads/profile/' . $filename;
    }

    session(['admin_profile' => $adminProfile]);

    return redirect('/admin/profile')->with('success', 'Profil admin berhasil diperbarui.');
});

/*
|--------------------------------------------------------------------------
| ADMIN - UPDATE PASSWORD
|--------------------------------------------------------------------------
*/

Route::post('/admin/profile/password/update', function (Request $request) {
    $adminPassword = defaultAdminPassword();

    $validated = $request->validate([
        'current_password' => 'required|string',
        'new_password' => 'required|string|min:3',
        'confirm_password' => 'required|string|same:new_password',
    ]);

    if ($validated['current_password'] !== $adminPassword['current_password']) {
        return back()->withErrors([
            'current_password' => 'Password saat ini tidak sesuai.',
        ])->withInput();
    }

    session([
        'admin_password' => [
            'current_password' => $validated['new_password'],
        ]
    ]);

    return redirect('/admin/profile/password')->with('success', 'Password admin berhasil diperbarui.');
});

/*
|--------------------------------------------------------------------------
| DATA SESSION DEFAULT - SUPER ADMIN
|--------------------------------------------------------------------------
*/

function defaultSuperAdminProfile()
{
    return session('superadmin_profile', [
        'name' => 'Super Admin',
        'email' => 'superadmin@gmail.com',
        'phone' => '081234567890',
        'photo' => null,
    ]);
}

/*
|--------------------------------------------------------------------------
| SUPER ADMIN - HALAMAN TAMPILAN
|--------------------------------------------------------------------------
*/

Route::get('/superadmin/profile', function () {
    $superadminProfile = defaultSuperAdminProfile();
    return view('superadmin-profile', compact('superadminProfile'));
});

Route::get('/superadmin/profile/password', function () {
    $superadminProfile = defaultSuperAdminProfile();
    $superadminPassword = defaultSuperAdminPassword();
    return view('superadmin-profile-password', compact('superadminProfile', 'superadminPassword'));
});

/*
|--------------------------------------------------------------------------
| SUPER ADMIN - HALAMAN EDIT
|--------------------------------------------------------------------------
*/

Route::get('/superadmin/profile/edit', function () {
    $superadminProfile = defaultSuperAdminProfile();
    return view('superadmin-profile-edit', compact('superadminProfile'));
});

Route::get('/superadmin/profile/edit/password', function () {
    $superadminProfile = defaultSuperAdminProfile();
    $superadminPassword = defaultSuperAdminPassword();
    return view('superadmin-profile-edit-password', compact('superadminProfile', 'superadminPassword'));
});

/*
|--------------------------------------------------------------------------
| SUPER ADMIN - UPDATE INFORMASI PRIBADI
|--------------------------------------------------------------------------
*/

Route::post('/superadmin/profile/update', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $profile = defaultSuperAdminProfile();

    $profile['name'] = $validated['name'];
    $profile['email'] = $validated['email'];
    $profile['phone'] = $validated['phone'];

    if ($request->hasFile('photo')) {
        $file = $request->file('photo');
        $filename = time() . '_' . $file->getClientOriginalName();

        $file->move(public_path('uploads/profile'), $filename);

        $profile['photo'] = 'uploads/profile/' . $filename;
    }

    session(['superadmin_profile' => $profile]);

    return redirect('/superadmin/profile')->with('success', 'Profil berhasil diperbarui');
});

/*
|--------------------------------------------------------------------------
| SUPER ADMIN - UPDATE PASSWORD
|--------------------------------------------------------------------------
*/

Route::post('/superadmin/profile/password/update', function (Request $request) {
    $superadminPassword = defaultSuperAdminPassword();

    $validated = $request->validate([
        'current_password' => 'required|string',
        'new_password' => 'required|string|min:3',
        'confirm_password' => 'required|string|same:new_password',
    ], [
        'current_password.required' => 'Password saat ini wajib diisi.',
        'new_password.required' => 'Password baru wajib diisi.',
        'new_password.min' => 'Password baru minimal 3 karakter.',
        'confirm_password.required' => 'Konfirmasi password wajib diisi.',
        'confirm_password.same' => 'Konfirmasi password harus sama dengan password baru.',
    ]);

    if ($validated['current_password'] !== $superadminPassword['current_password']) {
        return back()->withErrors([
            'current_password' => 'Password saat ini tidak sesuai.',
        ])->withInput();
    }

    session([
        'superadmin_password' => [
            'current_password' => $validated['new_password'],
        ]
    ]);

    return redirect('/superadmin/profile/password')->with('success', 'Password super admin berhasil diperbarui.');
});