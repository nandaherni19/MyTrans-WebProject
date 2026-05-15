<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SuperAdmin\PaketWisataController;
use App\Http\Controllers\SuperAdmin\DestinasiController;
use App\Http\Controllers\SuperAdmin\KendaraanController;
use App\Http\Controllers\SuperAdmin\DataBookingController;
use App\Http\Controllers\SuperAdmin\LaporanTransaksiController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\PasswordController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\ProfileController as SuperAdminProfileController;
use App\Http\Controllers\User\ProfileController as UserProfileController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\User\BookingController;
use App\Http\Controllers\SuperAdmin\UserManagementController;
use App\Http\Controllers\User\PaketWisataUserController;
use App\Http\Controllers\User\RiwayatBookingUserController;
use App\Models\Booking;
use App\Http\Controllers\MidtransTestController;
use App\Http\Controllers\User\MidtransCallbackController;

Route::post('/midtrans/webhook', [MidtransCallbackController::class, 'handle']);

// home - landing page
// Route::get('/welcome', [GuestController::class, 'welcome'])->name('welcome'); (ubah)
Route::get('/', [GuestController::class, 'welcome'])->name('welcome');

Route::get('/paket-wisata', [PaketWisataUserController::class, 'guestIndex'])
    ->name('guest.katalogpaketwisata');
Route::get('/paket-wisata/detail/{id}', [PaketWisataUserController::class, 'guestDetail'])
    ->name('guest.detailpaket');

// ================= LOGOUT =================
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', function () {
    return redirect()->route('login');
});
// ================= REGISTER =================
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

// ======= VERIFY EMAIL =======
Route::get('/verify-otp', [AuthController::class, 'showVerifyForm'])->name('verify.otp.form');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify.otp.submit');
Route::get('/verify-otp/resend', [AuthController::class, 'resendOtp'])->name('verify.otp.resend');

// ================= LOGIN =================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// ================= FORGOT PASSWORD =================
Route::get('/forgot-password', [PasswordController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [PasswordController::class, 'sendResetLink'])->name('password.email');

// ================= RESET PASSWORD =================
Route::get('/reset-password', [PasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordController::class, 'resetPassword'])->name('password.update');

// ================= DASHBOARD ADMIN DAN SUPERADMIN =================
Route::get('/dashboard/admin', [SuperAdminDashboardController::class, 'index'])
    ->middleware(['auth', 'role:admin,superadmin'])
    ->name('dashboard.beranda-admin');

// DASHBOARD SUPERADMIN - KELOLA PENGGUNA
Route::prefix('/dashboard/superadmin/kelola-pengguna')->controller(UserManagementController::class)->middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/', 'index')->name('dashboard.superadmin.kelola-pengguna');
    Route::post('/store', 'store')->name('dashboard.superadmin.kelola-pengguna.store');
    Route::put('/update/{id}', 'update')->name('dashboard.superadmin.kelola-pengguna.update');
    Route::delete('/delete/{id}', 'destroy')->name('dashboard.superadmin.kelola-pengguna.delete');
});

// DASHBOARD SUPERADMIN - KELOLA PAKET WISATA
Route::prefix('/dashboard/superadmin/kelola-paket-wisata')->controller(PaketWisataController::class)->middleware(['auth', 'role:admin,superadmin'])->group(function () {
    Route::get('/', 'index')->name('dashboard.superadmin.kelola-paket-wisata');
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

// DASHBOARD SUPERADMIN - KELOLA DATA BOOKING
Route::get(
    '/dashboard/superadmin/kelola-data-booking/kendaraan-tersedia',
    [DataBookingController::class, 'kendaraanTersedia']
)->middleware(['auth', 'role:admin,superadmin'])
    ->name('dashboard.superadmin.kelola-data-booking.kendaraan-tersedia');

Route::prefix('/dashboard/superadmin/kelola-data-booking')
    ->middleware(['auth', 'role:admin,superadmin'])
    ->group(function () {
        Route::get('/', [DataBookingController::class, 'index'])->name('booking.index');
        Route::get('/{id}/detail', [DataBookingController::class, 'show'])->name('booking.show');
        Route::get('/{id}/edit', [DataBookingController::class, 'edit'])->name('booking.edit');
        Route::post('/store', [DataBookingController::class, 'store'])->name('booking.store');
        Route::put('/update/{id}', [DataBookingController::class, 'update'])->name('booking.update');
        Route::delete('/delete/{id}', [DataBookingController::class, 'destroy'])->name('booking.delete');
        
        // Route tambahan yang harus masuk grup agar namanya jadi booking.batal dll
        Route::get('/sisa/{id}', [DataBookingController::class, 'getSisa'])->name('booking.sisa');
        Route::post('/{id}/lunasi', [DataBookingController::class, 'lunasi'])->name('booking.lunasi');
        Route::post('/qris-pelunasan/{id}', [DataBookingController::class, 'qrisPelunasan'])->name('booking.qris-pelunasan');
        Route::patch('/{id}/batal', [DataBookingController::class, 'batal'])->name('booking.batal');
        Route::patch('/{id}/refund-selesai', [DataBookingController::class, 'refund-selesai'])->name('booking.refund-selesai');
        Route::get('/kendaraan-tersedia', [DataBookingController::class, 'kendaraanTersedia'])->name('booking.kendaraan-tersedia');
    });

Route::patch(
    '/dashboard/superadmin/kelola-data-booking/{id}/batal',
    [DataBookingController::class, 'batal']
)->name('dashboard.superadmin.kelola-data-booking.batal');

Route::get(
    '/dashboard/superadmin/kelola-data-booking/sisa/{id}',
    [DataBookingController::class, 'getSisa']
)->name('dashboard.superadmin.kelola-data-booking.sisa');

Route::post(
    '/dashboard/superadmin/kelola-data-booking/{id}/lunasi',
    [DataBookingController::class, 'lunasi']
)->name('dashboard.superadmin.kelola-data-booking.lunasi');

Route::post(
    '/dashboard/superadmin/kelola-data-booking/qris-pelunasan/{id}',
    [DataBookingController::class, 'qrisPelunasan']
)->name('dashboard.superadmin.kelola-data-booking.qris-pelunasan');

Route::patch(
    '/dashboard/superadmin/kelola-data-booking/{id}/refund-selesai',
    [DataBookingController::class, 'refundSelesai']
)->name('dashboard.superadmin.kelola-data-booking.refund-selesai');

// Route::get('/booking/detail', [BookingController::class, 'detail']);
Route::prefix('/dashboard/superadmin/kelola-laporan-transaksi')->controller(LaporanTransaksiController::class)->middleware(['auth', 'role:admin,superadmin'])->group(function () {
    Route::get('/', 'index')->name('dashboard.superadmin.kelola-laporan-transaksi');


    Route::get('/export-xls', 'exportXls')
        ->name('dashboard.superadmin.kelola-laporan-transaksi.export-xls');
    Route::post('/store', 'store')->name('dashboard.superadmin.kelola-laporan-transaksi.store');
    Route::put('/update/{id}', 'update')->name('dashboard.superadmin.kelola-laporan-transaksi.update');
    Route::delete('/delete/{id}', 'destroy')->name('dashboard.superadmin.kelola-laporan-transaksi.delete');
});

Route::get(
    '/superadmin/laporan-transaksi/export-pdf',
    [LaporanTransaksiController::class, 'exportPdf']
)->name('dashboard.superadmin.kelola-laporan-transaksi.export-pdf');

// ================= PROFILE ADMIN / SUPERADMIN =================
Route::middleware(['auth', 'role:admin,superadmin'])->group(function () {
    Route::get('/dashboard/superadmin/profile', [SuperAdminProfileController::class, 'show'])
        ->name('dashboard.superadmin.profile');
    Route::get('/dashboard/superadmin/profile-edit', [SuperAdminProfileController::class, 'edit'])
        ->name('dashboard.superadmin.profile-edit');
    Route::post('/dashboard/superadmin/profile-edit', [SuperAdminProfileController::class, 'update'])
        ->name('dashboard.superadmin.profile-update');
    Route::get('/dashboard/superadmin/profile-password', [SuperAdminProfileController::class, 'password'])
        ->name('dashboard.superadmin.profile-password');
    Route::get('/dashboard/superadmin/profile-edit-password', [SuperAdminProfileController::class, 'editPassword'])
        ->name('dashboard.superadmin.profile-edit-password');
    Route::post('/dashboard/superadmin/profile-edit-password', [SuperAdminProfileController::class, 'updatePassword'])
        ->name('dashboard.superadmin.profile-password-update');
});

//LAPORAN TRANSAKSI
Route::get('/laporan-transaksi', [LaporanTransaksiController::class, 'index'])
    ->name('laporan-transaksi.index');

// ================= DASHBOARD USER =================
// Route::get('/dashboard/user', [UserDashboardController::class, 'index'])
Route::get('/dashboard/user', [GuestController::class, 'welcome'])
    ->middleware(['auth', 'role:user'])
    ->name('dashboard.user');

// ================= PROFILE USER =================
Route::middleware(['auth', 'role:user'])->group(function () {
    // Hapus 4 GET lama, ganti jadi 1:
    Route::get('/dashboard/user/profile', [UserProfileController::class, 'show'])
        ->name('dashboard.user.profile');

    // POST tetap 2, tidak berubah:
    Route::post('/dashboard/user/profile-update', [UserProfileController::class, 'update'])
        ->name('dashboard.user.profile-update');
    Route::post('/dashboard/user/profile-password-update', [UserProfileController::class, 'updatePassword'])
        ->name('dashboard.user.profile-password-update');
});

//RIWAYAT BOOKING - USER
Route::get(
    '/dashboard/user/riwayatbooking/{filter?}/{page?}',
    [RiwayatBookingUserController::class, 'index']
)->middleware(['auth', 'role:user'])->name('dashboard.user.riwayatbooking');

// DETAIL PESANAN - USER
Route::get('/dashboard/user/detail-pesanan/{id}', function ($id) {

    $data = Booking::with([
        'paket.kota.provinsi',
        'pembayaranTerakhir',
        'pembayarans',
        'kotaLayanan'
    ])->findOrFail($id);

    return view('dashboard.user.detailpesanan', compact('data'));

})->middleware(['auth', 'role:user'])
    ->name('dashboard.user.detailpesanan');

// PAKET WISATA - USER
Route::get('/dashboard/user/katalogpaketwisata', [PaketWisataUserController::class, 'index'])
    ->name('dashboard.user.katalogpaketwisata')
;
Route::get('/dashboard/user/detailpaket/{id}', [PaketWisataUserController::class, 'detail'])
    ->name('dashboard.user.detailpaket')
;
Route::get('/dashboard/user/requestbooking', function () {
    return view('dashboard.user.requestbooking');
})->middleware(['auth', 'role:user'])->name('dashboard.user.requestbooking');

// BOOKING - dari Paket Wisata
Route::get('/dashboard/user/booking/paket/{id_paket}', [BookingController::class, 'bookingPaket'])
    ->middleware(['auth', 'role:user'])
    ->name('dashboard.user.booking.paket');
// KENDARAAN TERSEDIA (AJAX)
Route::get('/dashboard/user/kendaraan-tersedia', [PaketWisataUserController::class, 'kendaraanTersedia'])
    ->middleware(['auth', 'role:user'])
    ->name('dashboard.user.kendaraan.tersedia');

// HALAMAN QRIS
Route::get('/dashboard/user/booking/qris/{id}', [BookingController::class, 'showQris'])
    ->middleware(['auth', 'role:user'])
    ->name('dashboard.user.booking.qris');

// HALAMAN CASH
Route::get('/dashboard/user/booking/cash/{id}', [BookingController::class, 'showCash'])
    ->middleware(['auth', 'role:user'])
    ->name('dashboard.user.booking.cash');

// CEK STATUS PEMBAYARAN MIDTRANS
Route::get('/dashboard/user/cek-status-pembayaran/{id}', [BookingController::class, 'cekStatusPembayaran'])
    ->middleware(['auth', 'role:user'])
    ->name('dashboard.user.cek-status-pembayaran');

// SUBMIT FORM BOOKING
Route::post('/dashboard/user/booking/check', [BookingController::class, 'check'])
    ->middleware(['auth', 'role:user'])
    ->name('dashboard.user.booking.check');

// BOOKING - pelunasan
Route::get('/dashboard/user/booking/pelunasan/{id}', [BookingController::class, 'showPelunasan'])
    ->middleware(['auth', 'role:user'])
    ->name('dashboard.user.booking.pelunasan');

// PROSES QRIS PELUNASAN
Route::post('/dashboard/user/booking/pelunasan/{id}/qris', [BookingController::class, 'qrisPelunasan'])
    ->middleware(['auth', 'role:user'])
    ->name('dashboard.user.booking.pelunasan.qris');

// PROSES CASH PELUNASAN
Route::post('/dashboard/user/booking/pelunasan/{id}/cash', [BookingController::class, 'cashPelunasan'])
    ->middleware(['auth', 'role:user'])
    ->name('dashboard.user.booking.pelunasan.cash');

Route::get('/booking/check-status/{id}', [BookingController::class, 'checkStatus']);

// ============ MIDTRANS - CALLBACK URL ======================
Route::get('/test-midtrans', [MidtransTestController::class, 'config']);
Route::get('/cek-env-midtrans', [MidtransTestController::class, 'env']);
Route::get('/test-snap-token', [MidtransTestController::class, 'snapToken']);