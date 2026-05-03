<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

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
    }

    public function password()
    {
        $user = Auth::user();

        return view('dashboard.superadmin.profile-password', compact('user'));
    }

    public function editPassword()
    {
        $user = Auth::user();

        return view('dashboard.superadmin.profile-edit-password', compact('user'));
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

        return redirect()->route('dashboard.superadmin.profile-password')
            ->with('success', 'Password berhasil diperbarui');
    }
}