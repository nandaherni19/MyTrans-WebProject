<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        return view('dashboard.user.profile', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();

        return view('dashboard.user.profile-edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:ms_users,email,' . $user->id_users . ',id_users',
            'no_hp' => 'required|string|max:20',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:512',
        ]);

        $user->nama = $validated['name'];
        $user->email = $validated['email'];
        $user->no_hp = $validated['no_hp'];

        if ($request->hasFile('photo')) {

            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            $filename = time() . '.jpg';
            $request->file('photo')->storeAs('profile', $filename, 'public');
            $user->photo = 'profile/' . $filename;
        }

        $user->save();

        return redirect()->route('dashboard.user.profile')
            ->with('success', 'Profil berhasil diperbarui');
    }

    public function password()
    {
        $user = Auth::user();

        return view('dashboard.user.profile-password', compact('user'));
    }

    public function editPassword()
    {
        $user = Auth::user();

        return view('dashboard.user.profile-edit-password', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password salah'
            ])->withInput();
        }

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return redirect()->route('dashboard.user.profile')
            ->with('success', 'Password berhasil diubah');
    }
}