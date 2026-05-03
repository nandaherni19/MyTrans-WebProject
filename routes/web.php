<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SuperAdmin\PaketWisataController;
use App\Http\Controllers\SuperAdmin\DestinasiController;
use App\Http\Controllers\SuperAdmin\KendaraanController;
use App\Http\Controllers\SuperAdmin\TrayekController;
use App\Http\Controllers\SuperAdmin\DataBookingController;
use App\Http\Controllers\SuperAdmin\LaporanTransaksiController; 
use App\Http\Controllers\SuperAdmin\RequestWisataController;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\SuperAdmin\UserManagementController;
use Illuminate\Http\Request;
use App\Http\Controllers\User\PaketWisataUserController;
use App\Models\PaketWisata;
use App\Mail\OtpMail;
use App\Models\Provinsi;
use App\Models\Trayek;
use App\Http\Controllers\User\BookingController;

// home - landing page
Route::get('/welcome', function () {
    if (Auth::check()) {
        if (Auth::user()->role == 'admin' || Auth::user()->role == 'superadmin') {
            return redirect()->route('dashboard.beranda-admin');
        }
        return redirect()->route('dashboard.user');
    }

    $paketTerbaru = PaketWisata::latest('id_paket')->take(3)->get();
    return view('welcome', compact('paketTerbaru'));
})->name('welcome');

Route::get('/paket-wisata', [PaketWisataUserController::class, 'guestIndex'])
    ->name('guest.katalogpaketwisata');

Route::get('/paket-wisata/detail/{id}', [PaketWisataUserController::class, 'guestDetail'])
    ->name('guest.detailpaket');

// ================= LOGOUT =================
Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('welcome')->with('success', 'Logout berhasil');
})->name('logout');

// ================= REGISTER =================
Route::get('/register', function () {
    if (Auth::check()) {
        if (Auth::user()->role == 'admin' || Auth::user()->role == 'superadmin') {
            return redirect()->route('dashboard.beranda-admin');
        }
        return redirect()->route('dashboard.user');
    }
    return view('auth.register');
})->name('register');

Route::post('/register', function () {
    request()->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'password' => 'required|min:6|confirmed',
        'phone_number' => 'required|string|max:20',
    ]);

    $email = request('email');
    $otp = rand(100000, 999999);

    $existingUser = DB::table('ms_users')
        ->where('email', $email)
        ->first();

    if ($existingUser) {
        if ($existingUser->is_verified) {
            return back()->withErrors([
                'email' => 'Email sudah terdaftar. Silakan login.'
            ])->withInput();
        }

        DB::table('ms_users')
            ->where('email', $email)
            ->update([
                'nama' => request('name'),
                'password' => Hash::make(request('password')),
                'no_hp' => request('phone_number'),
                'otp' => $otp,
                'otp_expires_at' => Carbon::now()->addMinutes(10),
                'updated_at' => now(),
            ]);

        try {
            Mail::to($email)->send(new OtpMail($otp));
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => 'Gagal mengirim OTP: ' . $e->getMessage()
            ])->withInput();
        }

        return redirect()->route('verify.otp.form', ['email' => $email])
            ->with('success', 'Akun sudah terdaftar tetapi belum diverifikasi. OTP baru sudah dikirim ke email Anda.');
    }

    DB::table('ms_users')->insert([
        'nama' => request('name'),
        'email' => $email,
        'password' => Hash::make(request('password')),
        'no_hp' => request('phone_number'),
        'role' => 'user',
        'otp' => $otp,
        'otp_expires_at' => Carbon::now()->addMinutes(10),
        'is_verified' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    try {
        Mail::to($email)->send(new OtpMail($otp));
    } catch (\Exception $e) {
        DB::table('ms_users')->where('email', $email)->delete();

        return back()->withErrors([
            'email' => 'Pendaftaran gagal karena email OTP tidak bisa dikirim: ' . $e->getMessage()
        ])->withInput();
    }

    return redirect()->route('verify.otp.form', ['email' => $email])
        ->with('success', 'Pendaftaran berhasil. Silakan cek email Anda untuk kode OTP.');
})->name('register.submit');

// ======= VERIFY EMAIL =======
Route::get('/verify-otp', [AuthController::class, 'showVerifyForm'])->name('verify.otp.form');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify.otp.submit');
Route::get('/verify-otp/resend', [AuthController::class, 'resendOtp'])->name('verify.otp.resend');

// ================= LOGIN =================
Route::get('/login', function () {
    if (Auth::check()) {
        if (Auth::user()->role == 'admin' || Auth::user()->role == 'superadmin') {
            return redirect()->route('dashboard.beranda-admin');
        }
        return redirect()->route('dashboard.user');
    }
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    request()->validate([
        'email' => 'required|email',
        'password' => 'required|min:6',
    ]);

    $email = request('email');
    $password = request('password');

    $user = DB::table('ms_users')
        ->where('email', request('email'))
        ->first();

    if (!$user || !Hash::check(request('password'), $user->password)) {
        return back()->withErrors(['email' => 'Email atau password salah']);
    }

    // Kalau akun belum diverifikasi, kirim OTP baru lalu arahkan ke verifikasi
    if (!$user->is_verified) {
        $otp = rand(100000, 999999);

        DB::table('ms_users')
            ->where('email', $email)
            ->update([
                'otp' => $otp,
                'otp_expires_at' => Carbon::now()->addMinutes(10),
                'updated_at' => now(),
            ]);

        try {
            Mail::to($email)->send(new OtpMail($otp));
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => 'Akun belum diverifikasi, tetapi gagal mengirim OTP baru: ' . $e->getMessage()
            ])->withInput();
        }

        return redirect()->route('verify.otp.form', ['email' => $email])
            ->with('success', 'Akun belum diverifikasi. Kode OTP baru sudah dikirim ke email Anda.');
    }

    // login auth
    Auth::loginUsingId($user->id_users);

    // REDIRECT KE DASHBOARD SESUAI ROLE
    if ($user->role === 'superadmin' || $user->role === 'admin') {
        return redirect()->route('dashboard.beranda-admin')->with('success', 'Login berhasil');
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

    Mail::raw("Halo,\nKami menerima permintaan untuk mereset kata sandi akun Anda di MyTrans Travels.\n\nSilakan klik tautan berikut:\n\n" . $link . "\n\n⚠️ Link ini hanya berlaku selama 60 menit.\n\nJika Anda tidak melakukan permintaan ini, abaikan email ini.\n\nTerima Kasih,\nTim MyTrans Travels", function ($message) {
        $message->to(request('email'))->subject('Reset Password MyTrans');
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
Route::middleware(['auth', 'role:admin,superadmin'])->group(function () {

    Route::get('/dashboard/admin', function () {
        return view('dashboard.beranda-admin');
    })->name('dashboard.beranda-admin');

});

// DASHBOARD SUPERADMIN - KELOLA PENGGUNA
Route::prefix('/dashboard/superadmin/kelola-pengguna')->controller(UserManagementController::class)->middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/', 'index')->name('dashboard.superadmin.kelola-pengguna');
    Route::post('/store', 'store')->name('dashboard.superadmin.kelola-pengguna.store');
    Route::put('/update/{id}', 'update')->name('dashboard.superadmin.kelola-pengguna.update');
    Route::delete('/delete/{id}', 'destroy')->name('dashboard.superadmin.kelola-pengguna.delete');
});

// DASHBOARD SUPERADMIN - KELOLA PAKET WISATA
Route::prefix('/dashboard/superadmin/kelola-paket-wisata')->controller(PaketWisataController::class)->middleware(['auth', 'role:admin,superadmin'])->group(function () {
    Route::get('/','index')->name('dashboard.superadmin.kelola-paket-wisata');
    // Route::get('/trayek/{id}/destinasi', [PaketWisataController::class, 'getDestinasiByTrayek']);
    Route::post('/store', 'store')->name('dashboard.superadmin.kelola-paket-wisata.store');
    Route::put('/update/{id}', 'update')->name('dashboard.superadmin.kelola-paket-wisata.update');
    Route::delete('/delete/{id}', 'destroy')->name('dashboard.superadmin.kelola-paket-wisata.delete');
});

Route::middleware(['auth', 'role:admin,superadmin'])->prefix('/dashboard/superadmin/kelola-destinasi')->group(function () {
    Route::get('/{section?}/{mode?}', [DestinasiController::class, 'index'])
        ->name('dashboard.superadmin.kelola-destinasi');

    // Provinsi
    Route::post('/provinsi/store', [DestinasiController::class, 'storeProvinsi'])
        ->name('dashboard.superadmin.kelola-destinasi.provinsi.store');

    Route::put('/provinsi/update/{id}', [DestinasiController::class, 'updateProvinsi'])
        ->name('dashboard.superadmin.kelola-destinasi.provinsi.update');

    Route::delete('/provinsi/delete/{id}', [DestinasiController::class, 'destroyProvinsi'])
        ->name('dashboard.superadmin.kelola-destinasi.provinsi.delete');

    // Kota
    Route::post('/kota/store', [DestinasiController::class, 'storeKota'])
        ->name('dashboard.superadmin.kelola-destinasi.kota.store');

    Route::put('/kota/update/{id}', [DestinasiController::class, 'updateKota'])
        ->name('dashboard.superadmin.kelola-destinasi.kota.update');

    Route::delete('/kota/delete/{id}', [DestinasiController::class, 'destroyKota'])
        ->name('dashboard.superadmin.kelola-destinasi.kota.delete');
});

// DASHBOARD SUPERADMIN - KELOLA KENDARAAN
Route::prefix('/dashboard/superadmin/kelola-kendaraan')->controller(KendaraanController::class)->middleware(['auth', 'role:admin,superadmin'])->group(function () {
    Route::get('/', 'index')->name('dashboard.superadmin.kelola-kendaraan');
    Route::post('/store', 'store')->name('dashboard.superadmin.kelola-kendaraan.store');
    Route::put('/update/{id}', 'update')->name('dashboard.superadmin.kelola-kendaraan.update');
    Route::delete('/delete/{id}', 'destroy')->name('dashboard.superadmin.kelola-kendaraan.delete');
});

// DASHBOARD SUPERADMIN - KELOLA TRAYEK
Route::prefix('/dashboard/superadmin/kelola-trayek')->controller(TrayekController::class)->middleware(['auth', 'role:admin,superadmin'])->group(function () {
    Route::get('/', 'index')->name('dashboard.superadmin.kelola-trayek');
    Route::post('/store', 'store')->name('dashboard.superadmin.kelola-trayek.store');
    Route::put('/update/{id}', 'update')->name('dashboard.superadmin.kelola-trayek.update');
    Route::delete('/delete/{id}', 'destroy')->name('dashboard.superadmin.kelola-trayek.delete');
});

// DASHBOARD SUPERADMIN - KELOLA DATA BOOKIN
Route::prefix('/dashboard/superadmin/kelola-data-booking')->controller(DataBookingController::class)->middleware(['auth', 'role:admin,superadmin'])->group(function () {
    Route::get('/', 'index')->name('dashboard.superadmin.kelola-data-booking');
    Route::post('/store', 'store')->name('dashboard.superadmin.kelola-data-booking.store');
    Route::put('/update/{id}', 'update')->name('dashboard.superadmin.kelola-data-booking.update');
    Route::delete('/delete/{id}', 'destroy')->name('dashboard.superadmin.kelola-data-booking.delete');
});

// Route::get('/booking/detail', [BookingController::class, 'detail']);
Route::prefix('/dashboard/superadmin/kelola-laporan-transaksi')->controller(LaporanTransaksiController::class)->middleware(['auth', 'role:admin,superadmin'])->group(function () {
    Route::get('/', 'index')->name('dashboard.superadmin.kelola-laporan-transaksi');
    Route::post('/store', 'store')->name('dashboard.superadmin.kelola-laporan-transaksi.store');
    Route::put('/update/{id}', 'update')->name('dashboard.superadmin.kelola-laporan-transaksi.update');
    Route::delete('/delete/{id}', 'destroy')->name('dashboard.superadmin.kelola-laporan-transaksi.delete');
});

// DASHBOARD SUPERADMIN - PROFILE
// view profile
Route::get('/dashboard/superadmin/profile', function () {
    $user = Auth::user();
    return view('dashboard.superadmin.profile', compact('user'));
})->middleware(['auth', 'role:admin,superadmin'])->name('dashboard.superadmin.profile');

// edit profile
Route::get('/dashboard/superadmin/profile-edit', function () {
    $user = Auth::user();
    return view('dashboard.superadmin.profile-edit', compact('user'));
})->middleware(['auth', 'role:admin,superadmin'])->name('dashboard.superadmin.profile-edit');

// update profile
Route::post('/dashboard/superadmin/profile-edit', function (Request $request) {
    $user = Auth::user();

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:ms_users,email,' . $user->id_users . ',id_users',
        'no_hp' => 'required|string|max:20',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $user->nama = $validated['name'];
    $user->email = $validated['email'];
    $user->no_hp = $validated['no_hp'];

    if ($request->hasFile('photo')) {
        $folderPath = public_path('uploads/profile');

        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }

        $file = $request->file('photo');
        $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());

        $file->move($folderPath, $filename);

        $user->photo = 'uploads/profile/' . $filename;
    }

    $user->save();

    return redirect()->route('dashboard.superadmin.profile')
        ->with('success', 'Profil berhasil diperbarui');
})->middleware(['auth', 'role:admin,superadmin'])->name('dashboard.superadmin.profile-update');

// view password
Route::get('/dashboard/superadmin/profile-password', function () {
    $user = Auth::user();
    return view('dashboard.superadmin.profile-password', compact('user'));
})->middleware(['auth', 'role:admin,superadmin'])->name('dashboard.superadmin.profile-password');

// edit password
Route::get('/dashboard/superadmin/profile-edit-password', function () {
    $user = Auth::user();
    return view('dashboard.superadmin.profile-edit-password', compact('user'));
})->middleware(['auth', 'role:admin,superadmin'])->name('dashboard.superadmin.profile-edit-password');

// update password
Route::post('/dashboard/superadmin/profile-edit-password', function (Request $request) {
    $user = Auth::user();

    $validated = $request->validate([
        'current_password' => 'required|string',
        'new_password' => 'required|string|min:6|confirmed',
    ]);

    if (!Hash::check($validated['current_password'], $user->password)) {
        return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai'])->withInput();
    }

    $user->password = Hash::make($validated['new_password']);
    $user->save();

    return redirect()->route('dashboard.superadmin.profile-password')->with('success', 'Password berhasil diperbarui');
})->middleware(['auth', 'role:admin,superadmin'])->name('dashboard.superadmin.profile-password-update');




// ================= DASHBOARD USER =================
Route::get('/dashboard/user', function () {
    $pakets = PaketWisata::latest('id_paket')->take(3)->get();

    return view('dashboard.beranda-user', compact('pakets'));
})->name('dashboard.user')->middleware(['auth', 'role:user']);

Route::get('/test-db', function () {
    return DB::table('ms_users')->get();
});

// DASHBOARD USER - PROFIL
// view profile
Route::get('/dashboard/user/profile', function () {
    $user = Auth::user();
    return view('dashboard.user.profile', compact('user'));
})->middleware(['auth', 'role:user'])->name('dashboard.user.profile');

// edit profile
Route::get('/dashboard/user/profile-edit', function () {
    $user = Auth::user();
    return view('dashboard.user.profile-edit', compact('user'));
})->middleware(['auth', 'role:user'])->name('dashboard.user.profile-edit');

// update profile
Route::post('/dashboard/user/profile-edit', function (Request $request) {
    $user = Auth::user();

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:ms_users,email,' . $user->id_users . ',id_users',
        'no_hp' => 'required|string|max:20',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $user->nama = $validated['name'];
    $user->email = $validated['email'];
    $user->no_hp = $validated['no_hp'];

    if ($request->hasFile('photo')) {
        $file = $request->file('photo');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $path = public_path('uploads/profile');

        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $file->move($path, $filename);

        $user->photo = 'uploads/profile/' . $filename;
    }

    $user->save();

    return redirect()->route('dashboard.user.profile')->with('success', 'Profil berhasil diperbarui');
})->middleware(['auth', 'role:user'])->name('dashboard.user.profile-update');

// view password
Route::get('/dashboard/user/profile-password', function () {
    $user = Auth::user();
    return view('dashboard.user.profile-password', compact('user'));
})->middleware(['auth', 'role:user'])->name('dashboard.user.profile-password');

// edit password
Route::get('/dashboard/user/profile-edit-password', function () {
    $user = Auth::user();
    return view('dashboard.user.profile-edit-password', compact('user'));
})->middleware(['auth', 'role:user'])->name('dashboard.user.profile-edit-password');

// update password
Route::post('/dashboard/user/profile-edit-password', function (Request $request) {
    $user = Auth::user();

    $validated = $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:6|confirmed',
    ]);

    if (!Hash::check($validated['current_password'], $user->password)) {
        return back()->withErrors(['current_password' => 'Password salah']);
    }

    $user->password = Hash::make($validated['new_password']);
    $user->save();

    return redirect()->route('dashboard.user.profile')->with('success', 'Password berhasil diubah');
})->middleware(['auth', 'role:user'])->name('dashboard.user.profile-password-update');



//===================LAPORAN TRANSAKSI===================

Route::get('/laporan-transaksi', [LaporanTransaksiController::class, 'index'])
    ->name('laporan-transaksi.index');

/*|--------------------------------------------------------------------------
| REQUEST WISATA - USER
|--------------------------------------------------------------------------*/
Route::get('/dashboard/user/requestbooking/{step?}', function (Illuminate\Http\Request $request, $step = 'home') {
    if ($step === 'home') {
        $request->session()->forget('request_booking');
    }

    $provinsis = collect();
    $trayeks = collect();

    if ($step === 'destinasi') {
        $provinsis = Provinsi::orderBy('nama_provinsi')->get();
        $trayeks = Trayek::with('kotaTujuan')->get();
    }

    return view('dashboard.user.requestbooking', compact('step', 'provinsis', 'trayeks'));
})->middleware(['auth', 'role:user'])->name('dashboard.user.requestbooking');


Route::post('/dashboard/user/requestbooking/informasi', function (Illuminate\Http\Request $request) {
    $validated = $request->validate(
        [
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email',
            'no_ktp' => 'required|string|max:30',
            'no_telepon' => 'required|string|max:20',
            'tanggal_keberangkatan' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_keberangkatan',
            'jumlah_peserta' => 'required|integer|min:1',
        ],
        [
            'nama_lengkap.required' => 'Data Informasi WAJIB diisi lengkap.',
            'email.required' => 'Data Informasi WAJIB diisi lengkap.',
            'email.email' => 'Data Informasi WAJIB diisi lengkap.',
            'no_ktp.required' => 'Data Informasi WAJIB diisi lengkap.',
            'no_telepon.required' => 'Data Informasi WAJIB diisi lengkap.',
            'tanggal_keberangkatan.required' => 'Data Informasi WAJIB diisi lengkap.',
            'tanggal_kembali.required' => 'Data Informasi WAJIB diisi lengkap.',
            'tanggal_kembali.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal keberangkatan.',
            'jumlah_peserta.required' => 'Data Informasi WAJIB diisi lengkap.',
            'jumlah_peserta.min' => 'Jumlah peserta minimal 1.',
        ]
    );

    session(['request_booking.informasi' => $validated]);

    return redirect()->route('dashboard.user.requestbooking', 'destinasi');
})->middleware(['auth', 'role:user'])->name('dashboard.user.requestbooking.informasi.store');

Route::post('/dashboard/user/requestbooking/destinasi', function (Illuminate\Http\Request $request) {
    $validated = $request->validate(
    [
        'provinsi' => 'required',
        'kota_tujuan' => 'required',
        'provinsi_input' => 'required|string|max:255',
        'kota_asal' => 'required|string|max:255',
        'titik_jemput' => 'required|string|max:255',
        'alamat' => 'required|string|max:500',
        'catatan' => 'nullable|string|max:1000',
    ],
    [
        'provinsi.required' => 'Data destinasi wajib diisi lengkap.',
        'kota_tujuan.required' => 'Data destinasi wajib diisi lengkap.',
        'provinsi_input.required' => 'Data destinasi wajib diisi lengkap.',
        'kota_asal.required' => 'Data destinasi wajib diisi lengkap.',
        'titik_jemput.required' => 'Data destinasi wajib diisi lengkap.',
        'alamat.required' => 'Alamat lengkap wajib diisi.',
    ]
    );

    session(['request_booking.destinasi' => $validated]);

    return redirect()->route('dashboard.user.requestbooking', 'ringkasan');
})->middleware(['auth', 'role:user'])->name('dashboard.user.requestbooking.destinasi.store');

Route::get('/api/kota-by-provinsi/{nama_provinsi}', function ($nama_provinsi) {
    $provinsi = \App\Models\Provinsi::where('nama_provinsi', $nama_provinsi)->first();
    if (!$provinsi) return response()->json([]);

    $kotas = \App\Models\Kota::where('id_provinsi', $provinsi->id_provinsi)->get();
    return response()->json($kotas);
})->middleware(['auth']);

Route::post('/dashboard/user/requestbooking/store', function () {

    $informasi = session('request_booking.informasi');
    $destinasi = session('request_booking.destinasi');

    if (!$informasi || !$destinasi) {
        return back()->withErrors('Data tidak lengkap');
    }

    DB::table('ms_request_wisata')->insert([
        'id_user' => Auth::user()->getAuthIdentifier(),
        'no_ktp' => $informasi['no_ktp'],
        'tanggal_keberangkatan' => $informasi['tanggal_keberangkatan'],
        'tanggal_kembali' => $informasi['tanggal_kembali'],
        'jumlah_peserta' => $informasi['jumlah_peserta'],
        'provinsi_asal' => $destinasi['provinsi_input'],
        'kota_asal' => $destinasi['kota_asal'],
        'kota_tujuan' => $destinasi['kota_tujuan'],
        'titik_jemput' => $destinasi['titik_jemput'],
        'alamat' => $destinasi['alamat'],
        'catatan' => $destinasi['catatan'] ?? null,
        'status_request' => 'pending',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    session()->forget('request_booking');

    return redirect()->route('dashboard.user.requestbooking', 'request');
})->name('dashboard.user.requestbooking.store')->middleware(['auth', 'role:user']);

/*|--------------------------------------------------------------------------
| RIWAYAT BOOKING - USER
|--------------------------------------------------------------------------*/
Route::get('/dashboard/user/riwayatbooking/{filter?}/{page?}', function ($filter = 'semua', $page = null) {
    return view('dashboard.user.riwayatbooking', compact('filter', 'page'));
})->middleware(['auth', 'role:user'])->name('dashboard.user.riwayatbooking');


/*|--------------------------------------------------------------------------
| DETAIL PESANAN - USER
|--------------------------------------------------------------------------*/
Route::get('/dashboard/user/detail-pesanan', function () {
    return view('dashboard.user.detailpesanan');
})->middleware(['auth', 'role:user'])->name('dashboard.user.detailpesanan');

//----PAKET WISATA USER
Route::get('/dashboard/user/katalogpaketwisata', [PaketWisataUserController::class, 'index'])
    ->name('dashboard.user.katalogpaketwisata')
        ;

Route::get('/dashboard/user/detailpaket/{id}', [PaketWisataUserController::class, 'detail'])
    ->name('dashboard.user.detailpaket')
        ;

//BOOKING USER
Route::get('/dashboard/user/booking/{page?}', function (Illuminate\Http\Request $request, $page = 'booking') {
    $selectedPaketId = $request->session()->get('selected_paket');
    $paket = null;
    $showWarning = false;
    $user = Auth::user();

    if ($selectedPaketId) {
        $paket = PaketWisata::find($selectedPaketId);
    }

    if (!$paket) {
        $showWarning = true;
    }

    return view('dashboard.user.booking', compact('page', 'showWarning', 'paket', 'user'));
})->middleware(['auth', 'role:user'])->name('dashboard.user.booking');


Route::get('/dashboard/user/booking/paket/{id}', function ($id) {
    session(['selected_paket' => $id]);
    return redirect()->route('dashboard.user.booking');
})->middleware(['auth', 'role:user'])->name('dashboard.user.booking.paket');

// booking-qris
Route::post('/dashboard/user/booking/qris', [BookingController::class, 'qris'])
    ->name('dashboard.user.booking.qris');


//cek validasi no ktp / pilih metode pembayaran untuk booking
Route::post('/dashboard/user/booking/check', function (Illuminate\Http\Request $request) {
    $selectedPaketId = $request->session()->get('selected_paket');
    $paket = PaketWisata::find($selectedPaketId);

    if (!$paket) {
        return redirect()->route('dashboard.user.katalogpaketwisata')
            ->with('error', 'Paket wisata tidak ditemukan atau belum dipilih.');
    }

    $request->validate([
        'no_ktp' => 'required|digits:16',
        'jumlah_peserta' => 'required|integer|min:1',
        'metode_pembayaran' => 'required|in:dp,pelunasan',
    ], [
        'no_ktp.required' => 'No KTP wajib diisi.',
        'no_ktp.digits' => 'No KTP harus 16 digit.',
        'jumlah_peserta.required' => 'Masukkan jumlah peserta.',
        'jumlah_peserta.integer' => 'Jumlah peserta harus berupa angka.',
        'jumlah_peserta.min' => 'Jumlah peserta minimal 1.',
        'metode_pembayaran.required' => 'Pilih metode pembayaran terlebih dahulu.',
        'metode_pembayaran.in' => 'Metode pembayaran tidak valid.',
    ]);

    if ($request->jumlah_peserta > $paket->sisa_kursi) {
        return back()->withErrors([
            'jumlah_peserta' => 'Jumlah peserta melebihi sisa kuota yang tersedia. Sisa kuota saat ini ' . $paket->sisa_kursi . ' orang.'
        ])->withInput();
    }

    return redirect()->route('dashboard.user.booking', 'qris');
})->middleware(['auth', 'role:user'])->name('dashboard.user.booking.check');