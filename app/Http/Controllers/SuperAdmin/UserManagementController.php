<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('dashboard.superadmin.kelola-pengguna', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:ms_users,email',
            'password' => 'required|min:8|confirmed',
            'no_hp' => 'required|string|max:20',
            'role' => 'required|in:admin,superadmin',
        ]);

        User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'no_hp' => $request->no_hp,
            'role' => $request->role,
        ]);

        return redirect()->route('dashboard.superadmin.kelola-pengguna')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:ms_users,email,' . $user->id_users . ',id_users',
            'password' => 'nullable|min:8|confirmed',
            'no_hp' => 'required|string|max:20',
            'role' => 'required|in:admin,superadmin',
        ]);

        $user->nama = $request->nama;
        $user->email = $request->email;

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        $user->no_hp = $request->no_hp;
        $user->role = $request->role;
        $user->save();

        return redirect()->route('dashboard.superadmin.kelola-pengguna')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('dashboard.superadmin.kelola-pengguna')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}