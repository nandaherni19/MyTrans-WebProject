<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrayekController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('ms_trayek_wisata as t')
            ->leftJoin('ms_kota as asal', 't.id_kota_asal', '=', 'asal.id_kota')
            ->leftJoin('ms_kota as tujuan', 't.id_kota_tujuan', '=', 'tujuan.id_kota')
            ->select(
                't.id_trayek',
                't.kode_trayek',
                't.id_kota_asal',
                't.id_kota_tujuan',
                't.created_at',
                't.updated_at',
                'asal.nama_kota as kota_asal',
                'tujuan.nama_kota as kota_tujuan'
            );

        if ($request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('t.kode_trayek', 'like', '%' . $search . '%')
                  ->orWhere('asal.nama_kota', 'like', '%' . $search . '%')
                  ->orWhere('tujuan.nama_kota', 'like', '%' . $search . '%');
            });
        }

        $trayeks = $query->get();

        $kotas = DB::table('ms_kota')->orderBy('nama_kota')->get();

        return view('dashboard.superadmin.kelola-trayek', compact('trayeks', 'kotas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kota_asal' => 'required|different:id_kota_tujuan|exists:ms_kota,id_kota',
            'id_kota_tujuan' => 'required|different:id_kota_asal|exists:ms_kota,id_kota',
        ], [
            'id_kota_asal.required' => 'Kota asal wajib dipilih.',
            'id_kota_tujuan.required' => 'Kota tujuan wajib dipilih.',
            'id_kota_asal.different' => 'Kota asal dan tujuan tidak boleh sama.',
            'id_kota_tujuan.different' => 'Kota asal dan tujuan tidak boleh sama.',
        ]);

        $last = DB::table('ms_trayek_wisata')
            ->whereNotNull('kode_trayek')
            ->orderBy('id_trayek', 'desc')
            ->first();

        $no = $last ? ((int) substr($last->kode_trayek, 3)) + 1 : 1;
        $kode = 'TRY' . str_pad($no, 2, '0', STR_PAD_LEFT);

        DB::table('ms_trayek_wisata')->insert([
            'kode_trayek' => $kode,
            'id_kota_asal' => $request->id_kota_asal,
            'id_kota_tujuan' => $request->id_kota_tujuan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Trayek berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_kota_asal' => 'required|different:id_kota_tujuan|exists:ms_kota,id_kota',
            'id_kota_tujuan' => 'required|different:id_kota_asal|exists:ms_kota,id_kota',
        ], [
            'id_kota_asal.required' => 'Kota asal wajib dipilih.',
            'id_kota_tujuan.required' => 'Kota tujuan wajib dipilih.',
            'id_kota_asal.different' => 'Kota asal dan tujuan tidak boleh sama.',
            'id_kota_tujuan.different' => 'Kota asal dan tujuan tidak boleh sama.',
        ]);

        DB::table('ms_trayek_wisata')
            ->where('id_trayek', $id)
            ->update([
                'id_kota_asal' => $request->id_kota_asal,
                'id_kota_tujuan' => $request->id_kota_tujuan,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Trayek berhasil diupdate.');
    }

    public function destroy($id)
    {
        $dipakaiPaket = DB::table('ms_paket_wisata')
            ->where('id_trayek', $id)
            ->exists();

        if ($dipakaiPaket) {
            return back()->withErrors([
                'error' => 'Trayek tidak bisa dihapus karena masih digunakan pada paket wisata.'
            ]);
        }

        DB::table('ms_trayek_wisata')
            ->where('id_trayek', $id)
            ->delete();

        return back()->with('success', 'Trayek berhasil dihapus.');
    }
}