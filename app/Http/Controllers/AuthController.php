<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\OtpMail;

class AuthController extends Controller
{
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

        if ($user->OTP != $request->otp) {
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
                'OTP' => null,
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
                'OTP' => $otp,
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