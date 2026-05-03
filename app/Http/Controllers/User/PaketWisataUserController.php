<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PaketWisata;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaketWisataUserController extends Controller
{
    public function index()
    {
        $pakets = PaketWisata::with(['kota.provinsi', 'kendaraan'])
            ->where('status', 'aktif')
            ->get()
            ->filter(function ($paket) {
                if ($paket->tipe === 'open_trip') {
                    return $paket->sisa_kursi > 0;
                }

                return true;
            });

        return view('dashboard.user.katalogpaketwisata', compact('pakets'));
    }

    public function guestIndex()
    {
        $pakets = PaketWisata::with(['kota.provinsi', 'kendaraan'])
            ->where('status', 'aktif')
            ->get()
            ->filter(function ($paket) {
                if ($paket->tipe === 'open_trip') {
                    return $paket->sisa_kursi > 0;
                }

                return true;
            });

        return view('guest.katalogpaketwisata', compact('pakets'));
    }

    public function detail($id)
    {
        $paket = PaketWisata::with(['kota.provinsi', 'kendaraan'])->findOrFail($id);

        return view('dashboard.user.detailpaket', compact('paket'));
    }

    public function guestDetail($id)
    {
        $paket = PaketWisata::with(['kota.provinsi', 'kendaraan'])->findOrFail($id);

        return view('guest.detailpaket', compact('paket'));
    }

    public function kendaraanTersedia(Request $request)
    {
        $tanggalBerangkat = $request->tanggal_berangkat;
        $tanggalKembali   = $request->tanggal_kembali;

        if (!$tanggalBerangkat || !$tanggalKembali) {
            return response()->json(Kendaraan::all()->map(function ($k) {
                $k->dipakai = false;
                return $k;
            }));
        }

        $dipakaiIds = DB::table('tr_booking_kendaraan')
            ->join('ms_booking', 'tr_booking_kendaraan.id_booking', '=', 'ms_booking.id_booking')
            ->whereIn('ms_booking.status_booking', ['pending', 'aktif'])
            ->where(function ($q) use ($tanggalBerangkat, $tanggalKembali) {
                $q->whereBetween('ms_booking.tanggal_berangkat', [$tanggalBerangkat, $tanggalKembali])
                ->orWhereBetween('ms_booking.tanggal_kembali', [$tanggalBerangkat, $tanggalKembali])
                ->orWhere(function ($q2) use ($tanggalBerangkat, $tanggalKembali) {
                    $q2->where('ms_booking.tanggal_berangkat', '<=', $tanggalBerangkat)
                        ->where('ms_booking.tanggal_kembali', '>=', $tanggalKembali);
                });
            })
            ->pluck('tr_booking_kendaraan.id_kendaraan')
            ->toArray();

        $kendaraans = Kendaraan::all()->map(function ($k) use ($dipakaiIds) {
            $k->dipakai = in_array($k->id_kendaraan, $dipakaiIds);
            return $k;
        });

        return response()->json($kendaraans);
    }
}