<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class KendaraanController extends Controller
{
    public function index()
    {
        $kendaraans = Kendaraan::all();
        return view('dashboard.superadmin.kelola-kendaraan', compact('kendaraans'));
    }

public function store(Request $request)
{
    // VALIDASI
    $data = $request->validate([
        'nama_kendaraan'   => 'required',
        'jenis_kendaraan'  => 'required|in:bus,elf,hiace,mobil',
        'kapasitas'        => 'required|integer|min:1',
        'plat_nomor'       => 'required',
        'harga_sewa'       => 'required|numeric|min:0',
        'status_kendaraan' => 'required|in:tersedia,tidak_tersedia,maintenance',
        'foto_kendaraan'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    // bersihkan format harga
        $data['harga_sewa'] = str_replace(['Rp', 'RP', '.', ' '], '', $request->harga_sewa);

    // UPLOAD GAMBAR
    if ($request->hasFile('foto_kendaraan')) {
        $data['foto_kendaraan'] = $request->file('foto_kendaraan')
            ->store('kendaraan', 'public');
    }

    // SIMPAN KE DATABASE
    Kendaraan::create($data);

    return redirect()->back()->with('success', 'Berhasil ditambahkan');
}

    public function update(Request $request, $id)
    {
        $kendaraan = Kendaraan::findOrFail($id);

        $data = $request->validate([
            'nama_kendaraan'   => 'required',
            'jenis_kendaraan'  => 'required|in:bus,elf,hiace,mobil',
            'kapasitas'        => 'required|integer|min:1',
            'plat_nomor'       => 'required',
            'harga_sewa'       => 'required|numeric|min:0',
            'status_kendaraan' => 'required|in:tersedia,tidak_tersedia,maintenance',
            'foto_kendaraan'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $data['harga_sewa'] = str_replace(['Rp', 'RP', '.', ' '], '', $request->harga_sewa);

        // jika upload gambar baru → hapus lama
        if ($request->hasFile('foto_kendaraan')) {
            if ($kendaraan->foto_kendaraan) {
                Storage::disk('public')->delete($kendaraan->foto_kendaraan);
            }

            $data['foto_kendaraan'] = $request->file('foto_kendaraan')
                ->store('kendaraan', 'public');
        }

        $kendaraan->update($data);

        return redirect()
            ->route('dashboard.superadmin.kelola-kendaraan')
            ->with('success', 'Kendaraan berhasil diupdate');
    }

    public function destroy($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);

        if ($kendaraan->paketWisata()->exists()) {
        return redirect()
            ->back()
            ->with('error', 'Kendaraan masih digunakan oleh paket wisata!');
        }

        if ($kendaraan->foto_kendaraan) {
            Storage::disk('public')->delete($kendaraan->foto_kendaraan);
        }

        $kendaraan->delete();

        return redirect()
            ->route('dashboard.superadmin.kelola-kendaraan')
            ->with('success', 'Kendaraan berhasil dihapus');
    }
}