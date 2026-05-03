<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Provinsi;
use App\Models\Kota;

class DestinasiController extends Controller
{
    public function index($section = 'provinsi', $mode = 'list')
    {
        $provinsis = Provinsi::withCount('kota')->get();
        $kotas = Kota::with('provinsi')->get();

        return view('dashboard.superadmin.kelola-destinasi', compact(
            'section',
            'mode',
            'provinsis',
            'kotas',
        ));
    }

    // ===================== PROVINSI =====================
    public function storeProvinsi(Request $request)
    {
        $request->validate([
            'nama_provinsi' => 'required|string|max:100',
        ]);

        Provinsi::create([
            'nama_provinsi' => $request->nama_provinsi,
        ]);

        return redirect('/dashboard/superadmin/kelola-destinasi/provinsi')->with('success', 'Provinsi berhasil ditambahkan.');
    }

    public function updateProvinsi(Request $request, $id)
    {
        $request->validate([
            'nama_provinsi' => 'required|string|max:100',
        ]);

        $provinsi = Provinsi::findOrFail($id);
        $provinsi->update([
            'nama_provinsi' => $request->nama_provinsi,
        ]);

        return back()->with('success', 'Provinsi berhasil diupdate.');
    }

    public function destroyProvinsi($id)
    {
        $provinsi = Provinsi::findOrFail($id);
        
        if ($provinsi->kota()->count() > 0) {
            return back()->with('error', 'Provinsi tidak dapat dihapus karena masih memiliki ' . $provinsi->kota()->count() . ' kota. Hapus semua kota terlebih dahulu.');
        }

        $provinsi->delete();

        return back()->with('success', 'Provinsi berhasil dihapus.');
    }

    // ===================== KOTA =====================
    public function storeKota(Request $request)
    {
        $request->validate([
            'nama_kota' => 'required|string|max:100',
            'id_provinsi' => 'required|exists:ms_provinsi,id_provinsi',
        ]);

        Kota::create([
            'nama_kota' => $request->nama_kota,
            'id_provinsi' => $request->id_provinsi,
        ]);

        return redirect('/dashboard/superadmin/kelola-destinasi/kota')->with('success', 'Kota berhasil ditambahkan.');
    }

    public function updateKota(Request $request, $id)
    {
        $request->validate([
            'nama_kota' => 'required|string|max:100',
            'id_provinsi' => 'required|exists:ms_provinsi,id_provinsi',
        ]);

        $kota = Kota::findOrFail($id);
        $kota->update([
            'nama_kota' => $request->nama_kota,
            'id_provinsi' => $request->id_provinsi,
        ]);

        return back()->with('success', 'Kota berhasil diupdate.');
    }

    public function destroyKota($id)
    {
        $kota = Kota::findOrFail($id);
        $paketCount = $kota->paketWisata()->count();

            if ($paketCount > 0) {
                return back()->with('error', 'Kota tidak dapat dihapus karena masih digunakan oleh paket wisata!');
            }

            $kota->delete();

        return back()->with('success', 'Kota berhasil dihapus.');
    }
}