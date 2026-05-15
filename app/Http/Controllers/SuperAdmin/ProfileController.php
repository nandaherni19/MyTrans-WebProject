<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('dashboard.superadmin.profile', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('dashboard.superadmin.profile-edit', compact('user'));
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

            // HAPUS FOTO LAMA (biar clean seperti user controller)
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            // buat nama file unik
            $filename = time() . '.' . $request->file('photo')->getClientOriginalExtension();

            // simpan ke storage/public/profile
            $request->file('photo')->storeAs('profile', $filename, 'public');

            // simpan path ke DB
            $user->photo = 'profile/' . $filename;
        }

        $user->save();

        return redirect()->route('dashboard.superadmin.profile')
            ->with('success', 'Profil berhasil diperbarui');
    }

    public function password()
    {
        return redirect()->route('dashboard.superadmin.profile');
    }

    public function editPassword()
    {
        return redirect()->route('dashboard.superadmin.profile');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini tidak sesuai'
            ])->withInput();
        }

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return redirect()->route('dashboard.superadmin.profile')
            ->with('success', 'Password berhasil diperbarui');
    }
}