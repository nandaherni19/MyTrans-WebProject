<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink()
    {
        request()->validate([
            'email' => 'required|email',
        ]);

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

        Mail::raw(
            "Halo,\nKami menerima permintaan untuk mereset kata sandi akun Anda di MyTrans Travels.\n\nSilakan klik tautan berikut:\n\n" . $link . "\n\nLink ini hanya berlaku selama 60 menit.\n\nJika Anda tidak melakukan permintaan ini, abaikan email ini.\n\nTerima Kasih,\nTim MyTrans Travels",
            function ($message) {
                $message->to(request('email'))->subject('Reset Password MyTrans');
            }
        );

        return back()->with('success', 'Link reset sudah dikirim ke email!');
    }

    public function showResetForm()
    {
        return view('auth.reset-password', [
            'token' => request('token')
        ]);
    }

    public function resetPassword()
    {
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
            return back()->withErrors([
                'email' => 'Token tidak valid / expired'
            ]);
        }

        DB::table('ms_users')
            ->where('email', request('email'))
            ->update([
                'password' => Hash::make(request('password'))
            ]);

        DB::table('password_resets')
            ->where('email', request('email'))
            ->delete();

        return redirect()->route('login')->with('success', 'Password berhasil direset');
    }
}