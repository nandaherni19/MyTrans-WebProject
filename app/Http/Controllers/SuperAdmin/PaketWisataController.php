<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaketWisata;
use App\Models\Trayek;
use App\Models\Kendaraan;
use Carbon\Carbon;

class PaketWisataController extends Controller
{
    public function index()
    {
        PaketWisata::where('tanggal_keberangkatan', '<', Carbon::now())
            ->update(['status' => 'nonaktif']);

        $pakets = PaketWisata::with(['trayek.kotaAsal', 'trayek.kotaTujuan'])->get();
        $trayeks = Trayek::with(['kotaAsal', 'kotaTujuan'])->get();
        $kendaraans = Kendaraan::all();

    return view('dashboard.superadmin.kelola-paket-wisata', compact('pakets', 'trayeks', 'kendaraans'));
    }

    // CREATE
    public function store(Request $request)
{
    $request->validate([
        'nama_paket' => 'required|string|max:150',
        'kapasitas' => 'required|integer|min:1',
        'deskripsi' => 'nullable|string',
        'harga' => 'required|integer|min:0',
        'durasi' => 'required|string|max:50',
        'id_trayek' => 'required|exists:ms_trayek_wisata,id_trayek',
        'id_kendaraan' => 'required|exists:ms_kendaraan,id_kendaraan',
        'fasilitas_didapat' => 'nullable|string',
        'status' => 'required|in:aktif,nonaktif',
        'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'tanggal_keberangkatan' => 'required|date|after_or_equal:today'
    ]);

    $kendaraan = Kendaraan::find($request->id_kendaraan);

    if ($request->kapasitas > $kendaraan->kapasitas) {
        return back()->withErrors([
            'kapasitas' => 'Kapasitas melebihi kapasitas kendaraan (' . $kendaraan->kapasitas . ' orang)'
        ])->withInput();
    }

    $gambarPath = null;

    if ($request->hasFile('gambar')) {
        $gambarPath = $request->file('gambar')->store('paket', 'public');
    }

    PaketWisata::create([
        'nama_paket' => $request->nama_paket,
        'kapasitas' => $request->kapasitas,
        'deskripsi' => $request->deskripsi,
        'harga' => $request->harga,
        'durasi' => $request->durasi,
        'gambar' => $gambarPath,
        'id_trayek' => $request->id_trayek,
        'id_kendaraan' => $request->id_kendaraan,
        'status' => $request->status,
        'fasilitas_didapat' => $request->fasilitas_didapat,
        'tanggal_keberangkatan' => $request->tanggal_keberangkatan,
    ]);

    return redirect()->back()->with('success', 'Data berhasil ditambahkan');
}

    // UPDATE
    public function update(Request $request, $id)
{
    $request->validate([
        'nama_paket' => 'required|string|max:150',
        'kapasitas' => 'required|integer|min:1',
        'deskripsi' => 'nullable|string',
        'harga' => 'required|integer|min:0',
        'durasi' => 'required|string|max:50',
        'id_trayek' => 'required|exists:ms_trayek_wisata,id_trayek',
        'id_kendaraan' => 'required|exists:ms_kendaraan,id_kendaraan',
        'fasilitas_didapat' => 'nullable|string',
        'status' => 'required|in:aktif,nonaktif',
        'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'tanggal_keberangkatan' => 'required|date'
    ]);

    $paket = PaketWisata::findOrFail($id);

    $data = [
        'nama_paket' => $request->nama_paket,
        'kapasitas' => $request->kapasitas,
        'deskripsi' => $request->deskripsi,
        'harga' => $request->harga,
        'durasi' => $request->durasi,
        'id_trayek' => $request->id_trayek,
        'id_kendaraan' => $request->id_kendaraan,
        'status' => $request->status,
        'fasilitas_didapat' => $request->fasilitas_didapat,
        'tanggal_keberangkatan' => $request->tanggal_keberangkatan,
    ];

    $kendaraan = Kendaraan::find($request->id_kendaraan);

    if ($request->kapasitas > $kendaraan->kapasitas) {
        return back()->withErrors([
            'kapasitas' => 'Kapasitas melebihi kapasitas kendaraan (' . $kendaraan->kapasitas . ' orang)'
        ])->withInput();
    }

    if ($request->hasFile('gambar')) {
        $data['gambar'] = $request->file('gambar')->store('paket', 'public');
    }

    $paket->update($data);

    return redirect()->back()->with('success', 'Data berhasil diupdate');
}
    // DELETE
    public function destroy($id)
    {
        $paket = PaketWisata::findOrFail($id);
        $paket->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }
}