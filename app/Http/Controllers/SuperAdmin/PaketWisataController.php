<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaketWisata;
use App\Models\Kendaraan;
use App\Models\Kota;
use App\Models\TitikJemput;
use Carbon\Carbon;

class PaketWisataController extends Controller
{
    public function index()
    {
        PaketWisata::where('tipe', 'open_trip')
            ->whereNotNull('tanggal_berangkat')
            ->where('tanggal_berangkat', '<', Carbon::now())
            ->update(['status' => 'nonaktif']);

        $pakets = PaketWisata::with(['kota.provinsi', 'kendaraan', 'titikJemput', 'kotaLayanan'])->get();
        $kotas = Kota::with('provinsi')->get();
        $kendaraans = Kendaraan::all();
        $titikJemputs = TitikJemput::all();

        return view('dashboard.superadmin.kelola-paket-wisata', compact('pakets', 'kotas', 'kendaraans', 'titikJemputs'));
    }

    public function store(Request $request)
    {
        $rules = [
            'id_kota' => 'required|exists:ms_kota,id_kota',
            'nama_paket' => 'required|string|max:45',
            'tipe' => 'required|in:paket,open_trip',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|integer|min:0',
            'durasi' => 'required|integer|min:1',
            'fasilitas' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'id_kendaraan' => 'nullable|exists:ms_kendaraan,id_kendaraan',
        ];

        if ($request->tipe === 'open_trip') {
            $rules['kapasitas'] = 'required|integer|min:1';
            $rules['tanggal_berangkat'] = 'required|date|after_or_equal:today';
            $rules['tanggal_kembali'] = 'required|date|after_or_equal:tanggal_berangkat';
            $rules['id_kendaraan'] = 'required|exists:ms_kendaraan,id_kendaraan';
            $rules['min_peserta'] = 'nullable|integer|min:1';
            $rules['titik_jemput']      = 'nullable|array';
            $rules['titik_jemput.*'] = 'nullable|string|max:100';
            $rules['kota_layanan']      = 'nullable|array';
            $rules['kota_layanan.*']    = 'exists:ms_kota,id_kota';
        }

        if ($request->tipe === 'paket') {
            $rules['min_peserta'] = 'required|integer|min:1';
            $rules['kapasitas'] = 'nullable|integer|min:1';
            $rules['id_kendaraan'] = 'nullable';
            $rules['tanggal_berangkat'] = 'nullable|date';
            $rules['tanggal_kembali'] = 'nullable|date|after_or_equal:tanggal_berangkat';
        }

        $request->validate($rules);

        if ($request->tipe === 'open_trip' && $request->id_kendaraan) {
            $kendaraan = Kendaraan::find($request->id_kendaraan);

            if ($request->kapasitas > $kendaraan->kapasitas) {
                return back()->withErrors([
                    'kapasitas' => 'Kapasitas melebihi kapasitas kendaraan (' . $kendaraan->kapasitas . ' orang)'
                ])->withInput();
            }
        }

        $gambarPath = null;

        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('paket', 'public');
        }

        $paket = PaketWisata::create([
            'id_kota' => $request->id_kota,
            'nama_paket' => $request->nama_paket,
            'tipe' => $request->tipe,
            'kapasitas' => $request->tipe === 'open_trip' ? $request->kapasitas : null,
            'min_peserta' => $request->min_peserta,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
            'durasi' => $request->durasi,
            'gambar' => $gambarPath,
            'fasilitas' => $request->fasilitas,
            'status' => $request->status,
            'id_kendaraan' => $request->tipe === 'open_trip'
                ? $request->id_kendaraan
                : null,
            'tanggal_berangkat' => $request->tipe === 'open_trip' ? $request->tanggal_berangkat : null,
            'tanggal_kembali' => $request->tipe === 'open_trip' ? $request->tanggal_kembali : null,
        ]);

        // sync titik jemput ke tr_titik_jemput (hanya open trip)
        if ($request->tipe === 'open_trip') {

            $titikJemputIds = collect($request->titik_jemput ?? [])
                ->map(function ($value) {
                    // kalau angka = berarti pilih dari database
                    if (is_numeric($value)) {
                        return $value;
                    }

                    // kalau teks = input baru → simpan ke DB
                    return \App\Models\TitikJemput::firstOrCreate([
                        'nama' => trim($value)
                    ])->id_titik_jemput;
                })
                ->toArray();

            $paket->titikJemput()->sync($titikJemputIds);

            $paket->kotaLayanan()->sync($request->kota_layanan ?? []);
        }

        return redirect()->back()->with('success', 'Data berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $paket = PaketWisata::findOrFail($id);

        $rules = [
            'id_kota' => 'required|exists:ms_kota,id_kota',
            'nama_paket' => 'required|string|max:45',
            'tipe' => 'required|in:paket,open_trip',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|integer|min:0',
            'durasi' => 'required|integer|min:1',
            'fasilitas' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'id_kendaraan' => 'nullable|exists:ms_kendaraan,id_kendaraan',
        ];

        if ($request->tipe === 'open_trip') {
            $rules['kapasitas'] = 'required|integer|min:1';
            $rules['tanggal_berangkat'] = 'required|date';
            $rules['tanggal_kembali'] = 'required|date|after_or_equal:tanggal_berangkat';
            $rules['min_peserta'] = 'nullable|integer|min:1';
        }

        if ($request->tipe === 'paket') {
            $rules['min_peserta'] = 'required|integer|min:1';
        }

        $request->validate($rules);

        $data = [
            'id_kota' => $request->id_kota,
            'nama_paket' => $request->nama_paket,
            'tipe' => $request->tipe,
            'kapasitas' => $request->tipe === 'open_trip' ? $request->kapasitas : null,
            'min_peserta' => $request->min_peserta,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
            'durasi' => $request->durasi,
            'fasilitas' => $request->fasilitas,
            'status' => $request->status,
            'id_kendaraan' => $request->tipe === 'open_trip' ? $request->id_kendaraan : null,
            'tanggal_berangkat' => $request->tipe === 'open_trip' ? $request->tanggal_berangkat : null,
            'tanggal_kembali' => $request->tipe === 'open_trip' ? $request->tanggal_kembali : null,
        ];

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('paket', 'public');
        }

        $paket->update($data);

        if ($request->tipe === 'open_trip') {
        $titikJemputIds = collect($request->titik_jemput ?? [])
            ->map(function ($value) {
                if (is_numeric($value)) {
                    return $value;
                }

                return \App\Models\TitikJemput::firstOrCreate([
                    'nama' => trim($value)
                ])->id_titik_jemput;
            })
            ->toArray();

        $paket->titikJemput()->sync($titikJemputIds);
    } else {
        $paket->titikJemput()->detach();
    }

$paket->kotaLayanan()->sync($request->kota_layanan ?? []);

        return redirect()->back()->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        $paket = PaketWisata::findOrFail($id);

        // hapus relasi dulu sebelum delete
        $paket->titikJemput()->detach();
        $paket->kotaLayanan()->detach();

        $paket->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }
}