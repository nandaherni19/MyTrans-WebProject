<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Mail\OtpMail;

class AuthController extends Controller
{
    public function showRegister()
    {
        if (Auth::check()) {
            if (Auth::user()->role == 'admin' || Auth::user()->role == 'superadmin') {
                return redirect()->route('dashboard.beranda-admin');
            }

            return redirect()->route('dashboard.user');
        }

        return view('auth.register');
    }

    public function register()
    {
        request()->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
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
    }

    public function showLogin()
    {
        if (Auth::check()) {
            if (Auth::user()->role == 'admin' || Auth::user()->role == 'superadmin') {
                return redirect()->route('dashboard.beranda-admin');
            }

            return redirect()->route('dashboard.user');
        }

        return view('auth.login');
    }

    public function login()
    {
        request()->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $email = request('email');

        $user = DB::table('ms_users')
            ->where('email', $email)
            ->first();

        if (!$user || !Hash::check(request('password'), $user->password)) {
            return back()->withErrors(['email' => 'Email atau password salah']);
        }

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

        Auth::loginUsingId($user->id_users);

        if ($user->role === 'superadmin' || $user->role === 'admin') {
            return redirect()->route('dashboard.beranda-admin')->with('success', 'Login berhasil');
        }

        return redirect()->intended(route('dashboard.user'));
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('welcome')->with('success', 'Logout berhasil');
    }
    public function showVerifyForm(Request $request)
    {
        return view('auth.verify-otp', [
            'email' => $request->email
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $user = DB::table('ms_users')
            ->where('email', $request->email)
            ->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'User tidak ditemukan.'
            ])->withInput();
        }

        if ($user->is_verified) {
            return redirect()->route('login')->with('success', 'Akun sudah terverifikasi, silakan login.');
        }

        if ($user->otp != $request->otp) {
            return back()->withErrors([
                'otp' => 'Kode OTP salah.'
            ])->withInput();
        }

        if (now()->gt($user->otp_expires_at)) {
            return back()->withErrors([
                'otp' => 'Kode OTP sudah kadaluarsa.'
            ])->withInput();
        }

        DB::table('ms_users')
            ->where('email', $request->email)
            ->update([
                'is_verified' => 1,
                'otp' => null,
                'otp_expires_at' => null,
                'updated_at' => now(),
            ]);

        return redirect()->route('login')->with('success', 'Akun berhasil diverifikasi. Silakan login.');
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;

        $user = DB::table('ms_users')
            ->where('email', $email)
            ->first();

        if (!$user) {
            return redirect()->route('verify.otp.form', ['email' => $email])
                ->withErrors(['email' => 'User tidak ditemukan.']);
        }

        if ($user->is_verified) {
            return redirect()->route('login')
                ->with('success', 'Akun sudah terverifikasi. Silakan login.');
        }

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
                'email' => 'Gagal mengirim ulang OTP: ' . $e->getMessage()
            ]);
        }

        return redirect()->route('verify.otp.form', ['email' => $email])
            ->with('success', 'Kode OTP baru sudah dikirim ke email Anda.');
    }
}