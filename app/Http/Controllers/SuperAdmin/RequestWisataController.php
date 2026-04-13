<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\RequestWisata;
use Illuminate\Http\Request;

class RequestWisataController extends Controller
{
    public function index()
    {
    //     $requests = collect([
    //     (object)[
    //         'id_request' => 1,
    //         'user' => (object)['nama' => 'Budi Santoso'],
    //         'kotaAsal' => (object)['nama_kota' => 'Jakarta'],
    //         'kotaTujuan' => (object)['nama_kota' => 'Bali'],
    //         'tanggal_keberangkatan' => '2025-06-01',
    //         'jumlah_peserta' => 4,
    //         'durasi' => '3 Hari 2 Malam',
    //         'catatan' => 'Tolong siapkan guide lokal',
    //         'estimasi_harga' => null,
    //         'status_request' => 'pending',
    //     ],
    //     (object)[
    //         'id_request' => 2,
    //         'user' => (object)['nama' => 'Siti Rahayu'],
    //         'kotaAsal' => (object)['nama_kota' => 'Surabaya'],
    //         'kotaTujuan' => (object)['nama_kota' => 'Lombok'],
    //         'tanggal_keberangkatan' => '2025-07-10',
    //         'jumlah_peserta' => 6,
    //         'durasi' => '5 Hari 4 Malam',
    //         'catatan' => 'Preferensi hotel bintang 4',
    //         'estimasi_harga' => 12000000,
    //         'status_request' => 'diproses',
    //     ],
    //     (object)[
    //         'id_request' => 3,
    //         'user' => (object)['nama' => 'Ahmad Fauzi'],
    //         'kotaAsal' => (object)['nama_kota' => 'Bandung'],
    //         'kotaTujuan' => (object)['nama_kota' => 'Yogyakarta'],
    //         'tanggal_keberangkatan' => '2025-08-15',
    //         'jumlah_peserta' => 2,
    //         'durasi' => '2 Hari 1 Malam',
    //         'catatan' => '-',
    //         'estimasi_harga' => 35000,
    //         'status_request' => 'disetujui',
    //     ],
    // ]);

        $requests = RequestWisata::with(['user', 'kotaAsal', 'kotaTujuan'])->get();

        return view('dashboard.superadmin.kelola-request-wisata', compact('requests'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'estimasi_harga' => 'required|numeric|min:0',
            'status_request' => 'required|string'
        ]);

        $data = RequestWisata::findOrFail($id);
        $data->update([
            'estimasi_harga' => $request->estimasi_harga,
            'status_request' => $request->status_request,
        ]);

        return redirect()->back()->with('success', 'Request berhasil diupdate');
    }

    public function acc($id)
    {
        $data = RequestWisata::findOrFail($id);
        $data->update([
            'status_request' => 'disetujui'
        ]);

        return redirect()->back()->with('success', 'Request berhasil di-ACC');
    }

    public function reject($id)
    {
        $data = RequestWisata::findOrFail($id);
        $data->update([
            'status_request' => 'ditolak'
        ]);

        return redirect()->back()->with('success', 'Request berhasil ditolak');
    }
}