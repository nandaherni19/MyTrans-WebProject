<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PaketWisata;
use App\Models\Booking;
use App\Models\Pembayaran;
use App\Models\PaymentGateway;
use App\Models\Kendaraan;
use App\Models\Penumpang;
use Carbon\Carbon;
use App\Services\MidtransService;

class BookingController extends Controller
{
    public function checkStatus($id)
    {
        $booking = Booking::with('pembayaranTerakhir')->findOrFail($id);

        return response()->json([
            'status_pembayaran' => $booking->pembayaranTerakhir->status_pembayaran ?? 'pending',
            'jenis_pembayaran'  => $booking->pembayaranTerakhir->jenis_pembayaran ?? null,
            'status_booking'    => $booking->status_booking,
        ]);
    }

    public function bookingPaket($id_paket)
    {
        $paket = PaketWisata::with(['kota.provinsi', 'kendaraan'])->findOrFail($id_paket);
        $user = Auth::user();
        $kendaraans = Kendaraan::all();

        return view('dashboard.user.booking', [
            'page'        => 'booking',
            'paket'       => $paket,
            'user'        => $user,
            'request'     => null,
            'kendaraans'  => $kendaraans,
            'showWarning' => false,
            'bookingData' => null,
        ]);
    }

    public function check(Request $request, MidtransService $midtransService)
    {
        $user = Auth::user();

        $paket = PaketWisata::with('kendaraan')->findOrFail($request->id_paket);

        $request->validate([
            'id_paket'          => 'required|exists:ms_paket_wisata,id_paket',
            'jumlah_peserta'    => 'required|integer|min:1',
            'tipe_pembayaran'   => 'required|in:qris,cash',
            'opsi_pembayaran'   => 'required|in:dp,lunas',
            'id_kendaraan'      => 'nullable|array',
            'id_kendaraan.*'    => 'exists:ms_kendaraan,id_kendaraan',
            'tanggal_berangkat' => 'nullable|date|after_or_equal:today',
            'tanggal_kembali'   => 'nullable|date|after_or_equal:tanggal_berangkat',
            'no_ktp'            => 'required|digits:16',
            
            'id_kota_layanan'   => 'required|exists:ms_kota,id_kota',

            'id_titik_jemput'   => $paket->tipe === 'open_trip'
                ? 'required|exists:ms_titik_jemput,id_titik_jemput'
                : 'nullable',

            'alamat_jemput'     => $paket->tipe === 'paket'
                ? 'required|string|max:255'
                : 'nullable',
        ], [
            'no_ktp.required' => 'No KTP wajib diisi.',
            'no_ktp.digits'   => 'No KTP harus 16 digit angka.',
        ]);

        // $paket = PaketWisata::with('kendaraan')->findOrFail($request->id_paket);

        $jumlahPeserta = (int) $request->jumlah_peserta;
        $tipeBooking = $paket->tipe;
        $idKendaraan = null;
        $idKendaraanList = [];

        if ($paket->tipe === 'open_trip') {
            if ($jumlahPeserta > $paket->sisa_kursi) {
                return back()->withErrors([
                    'jumlah_peserta' => 'Jumlah peserta melebihi sisa kuota. Sisa kuota: ' . $paket->sisa_kursi . ' orang.'
                ])->withInput();
            }

            $tanggalBerangkat = $paket->tanggal_berangkat;
            $tanggalKembali   = $paket->tanggal_kembali;
            $idKendaraan      = $paket->id_kendaraan;

            $totalHarga = $paket->harga * $jumlahPeserta;
        } else {
            if ($jumlahPeserta < $paket->min_peserta) {
                return back()->withErrors([
                    'jumlah_peserta' => 'Minimal peserta untuk paket ini adalah ' . $paket->min_peserta . ' orang.'
                ])->withInput();
            }

            if (!$request->tanggal_berangkat || !$request->tanggal_kembali) {
                return back()->withErrors([
                    'tanggal_berangkat' => 'Tanggal berangkat dan tanggal kembali wajib diisi untuk paket wisata.'
                ])->withInput();
            }

            $idKendaraanList = $request->id_kendaraan ?? [];

            if (empty($idKendaraanList)) {
                return back()->withErrors([
                    'id_kendaraan' => 'Pilih minimal 1 kendaraan.'
                ])->withInput();
            }

            $totalKapasitas = Kendaraan::whereIn('id_kendaraan', $idKendaraanList)->sum('kapasitas');

            if ($totalKapasitas < $jumlahPeserta) {
                return back()->withErrors([
                    'id_kendaraan' => "Total kapasitas kendaraan ($totalKapasitas orang) tidak cukup untuk $jumlahPeserta peserta."
                ])->withInput();
            }

            $totalSewaKendaraan = Kendaraan::whereIn('id_kendaraan', $idKendaraanList)->sum('harga_sewa');

            $tanggalBerangkat = $request->tanggal_berangkat;
            $tanggalKembali   = $request->tanggal_kembali;

            $totalHarga = ($paket->harga * $jumlahPeserta) + $totalSewaKendaraan;
        }

        $isQris = $request->tipe_pembayaran === 'qris';
        $isCash = $request->tipe_pembayaran === 'cash';
        $isDp = $request->opsi_pembayaran === 'dp';

        $totalBayar = $isDp ? ($totalHarga * 0.25) : $totalHarga;
        $expiredAt = Carbon::now()->addDay();

        DB::beginTransaction();

        try {
            $booking = Booking::create([
                'jumlah_peserta'    => $jumlahPeserta,
                'total_biaya'       => $totalHarga,
                'status_booking'    => 'pending',
                'tipe_booking'      => $tipeBooking,
                'tipe_pembayaran'   => $request->tipe_pembayaran,
                'opsi_pembayaran'   => $request->opsi_pembayaran,
                'id_paket'          => $paket->id_paket,
                'id_users'          => $user->id_users,
                'tanggal_berangkat' => $tanggalBerangkat,
                'tanggal_kembali'   => $tanggalKembali,

                'id_kota_layanan' => $request->id_kota_layanan,
                'id_titik_jemput' => $request->id_titik_jemput,
                'alamat_jemput'   => $request->alamat_jemput,

                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            if ($paket->tipe === 'open_trip') {
                DB::table('tr_booking_kendaraan')->insert([
                    'id_booking'   => $booking->id_booking,
                    'id_kendaraan' => $idKendaraan,
                    
                ]);

                for ($i = 0; $i < $jumlahPeserta; $i++) {
                    Penumpang::create([
                        'id_booking' => $booking->id_booking,
                        'id_users'   => $user->id_users,
                    ]);
                }
            } else {
                foreach ($idKendaraanList as $idK) {
                    DB::table('tr_booking_kendaraan')->insert([
                        'id_booking'   => $booking->id_booking,
                        'id_kendaraan' => $idK,
                        
                    ]);
                }
            }

            if ($isQris) {
                $orderId = 'TRX-' . $booking->id_booking . '-' . time();

                $response = $midtransService->createQris([
                    'order_id'     => $orderId,
                    'gross_amount' => (int) $totalBayar,
                    'name'         => $user->nama,
                    'email'        => $user->email,
                    'phone'        => $user->no_hp,
                ]);

                $responseArray = json_decode(json_encode($response), true);
                $qrUrl = null;

                if (!empty($responseArray['actions'])) {
                    foreach ($responseArray['actions'] as $action) {
                        if (($action['name'] ?? '') === 'generate-qr-code-v2') {
                            $qrUrl = $action['url'] ?? null;
                            break;
                        }
                    }
                }

                $pembayaran = Pembayaran::create([
                    'id_booking'         => $booking->id_booking,
                    'jumlah_bayar'       => $totalBayar,
                    'tanggal_bayar'      => null,
                    'metode_pembayaran'  => 'qris',
                    'kode_pembayaran'    => $orderId,
                    'transaction_status' => 'pending',
                    'created_at'         => now(),
                ]);

                PaymentGateway::create([
                    'id_pembayaran'          => $pembayaran->id_pembayaran,
                    'gateway_name'           => 'midtrans',
                    'gateway_order_id'       => $orderId,
                    'gateway_transaction_id' => $responseArray['transaction_id'] ?? null,
                    'payment_type'           => 'qris',
                    'qr_url'                 => $qrUrl,
                    'expired_at'             => $expiredAt,
                    'transaction_status'     => 'pending',
                    'raw_response'           => json_encode($responseArray),
                ]);

                DB::commit();

                return redirect()->route('dashboard.user.booking.qris', $booking->id_booking);
            }

            if ($isCash) {
                Pembayaran::create([
                    'id_booking'         => $booking->id_booking,
                    'jumlah_bayar'       => $totalBayar,
                    'tanggal_bayar'      => null,
                    'metode_pembayaran'  => 'cash',
                    'kode_pembayaran'    => null,
                    'transaction_status' => 'pending',
                    'created_at'         => now(),
                ]);

                DB::commit();

                return redirect()->route('dashboard.user.booking.cash', $booking->id_booking);
            }

            throw new \Exception('Metode pembayaran tidak valid.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal membuat transaksi pembayaran: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function showQris($id)
    {
        $user = Auth::user();
        $booking = Booking::with([
            'paket',
            'pembayaranTerakhir.paymentGateway',
            'kendaraans',
            'kotaLayanan',
            'titikJemput'
        ])->findOrFail($id);

        if ($booking->id_users != $user->id_users) {
            abort(403);
        }

        $pembayaran = $booking->pembayaranTerakhir;
        $gateway = $pembayaran->paymentGateway ?? null;

        $kendaraanText = $booking->kendaraans
            ->map(function ($k) {
                return $k->nama_kendaraan;
            })
            ->implode(', ');

        $bookingData = [
            'id_booking'        => $booking->id_booking,
            'nama_lengkap'      => $user->nama,
            'email'             => $user->email,
            'telepon'           => $user->no_hp,
            'jumlah_peserta'    => $booking->jumlah_peserta,
            'paket_wisata'      => $booking->paket->nama_paket ?? '-',
            'tipe_booking'      => $booking->tipe_booking,
            'tanggal_berangkat' => Carbon::parse($booking->tanggal_berangkat)->format('d M Y'),
            'tanggal_kembali'   => Carbon::parse($booking->tanggal_kembali)->format('d M Y'),
            'tipe_pembayaran'   => $booking->tipe_pembayaran,
            'opsi_pembayaran'   => $booking->opsi_pembayaran,
            'total_harga'       => $booking->total_biaya,
            'total_bayar'       => $pembayaran->jumlah_bayar ?? 0,
            'qr_url'            => $gateway->qr_url ?? null,
            'expired_at'        => $gateway->expired_at ?? null,
            'kendaraan'         => $kendaraanText ?: '-',
        ];

        return view('dashboard.user.booking', [
            'page'           => 'qris',
            'user'           => $user,
            'paket'          => $booking->paket,
            'booking'        => $booking,
            'bookingData'    => $bookingData,
            'showWarning'    => false,
            'successMessage' => null,
        ]);
    }

    public function showCash($id)
    {
        $user = Auth::user();

        $booking = Booking::with([
            'paket',
            'pembayaranTerakhir.paymentGateway',
            'kendaraans',
            'kotaLayanan',
            'titikJemput'
        ])->findOrFail($id);

        if ($booking->id_users != $user->id_users) {
            abort(403);
        }

        $pembayaran = $booking->pembayaranTerakhir;

        $kendaraanText = $booking->kendaraans
            ->map(function ($k) {
                return $k->nama_kendaraan;
            })
            ->implode(', ');

        $bookingData = [
            'id_booking'        => $booking->id_booking,
            'nama_lengkap'      => $user->nama,
            'email'             => $user->email,
            'telepon'           => $user->no_hp,
            'jumlah_peserta'    => $booking->jumlah_peserta,
            'paket_wisata'      => $booking->paket->nama_paket ?? '-',
            'tipe_booking'      => $booking->tipe_booking,
            'tanggal_berangkat' => Carbon::parse($booking->tanggal_berangkat)->format('d M Y'),
            'tanggal_kembali'   => Carbon::parse($booking->tanggal_kembali)->format('d M Y'),
            'tipe_pembayaran'   => $booking->tipe_pembayaran,
            'opsi_pembayaran'   => $booking->opsi_pembayaran,
            'total_harga'       => $booking->total_biaya,
            'total_bayar'       => $pembayaran->jumlah_bayar ?? 0,
            'qr_url'            => null,
            'expired_at'        => $pembayaran->expired_at ?? null,
            'kendaraan'         => $kendaraanText ?: '-',
        ];

        return view('dashboard.user.booking', [
            'page'           => 'cash',
            'user'           => $user,
            'paket'          => $booking->paket,
            'booking'        => $booking,
            'bookingData'    => $bookingData,
            'showWarning'    => false,
            'successMessage' => null,
        ]);
    }

    public function showPelunasan($id)
    {
        $booking = Booking::with(['paket', 'pembayarans', 'pembayaranTerakhir'])
            ->where('id_users', auth()->user()->id_users)
            ->findOrFail($id);

        $sudahBayar = $booking->pembayarans
            ->where('transaction_status', 'berhasil')
            ->sum('jumlah_bayar');

        $sisaBayar = max($booking->total_biaya - $sudahBayar, 0);

        return view('dashboard.user.pelunasan', compact('booking', 'sudahBayar', 'sisaBayar'));
    }

    public function qrisPelunasan($id, MidtransService $midtransService)
    {
        $user = Auth::user();

        $booking = Booking::with(['paket', 'pembayarans'])
            ->where('id_users', $user->id_users)
            ->findOrFail($id);

        $sudahBayar = $booking->pembayarans
            ->where('transaction_status', 'berhasil')
            ->sum('jumlah_bayar');

        $sisaBayar = max($booking->total_biaya - $sudahBayar, 0);

        if ($sisaBayar <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Booking sudah lunas.'
            ], 400);
        }

        $orderId = 'LUNAS-' . $booking->id_booking . '-' . time();
        $expiredAt = Carbon::now()->addDay();

        DB::beginTransaction();

        try {
            $response = $midtransService->createQris([
                'order_id'     => $orderId,
                'gross_amount' => (int) $sisaBayar,
                'name'         => $user->nama,
                'email'        => $user->email,
                'phone'        => $user->no_hp,
            ]);

            $responseArray = json_decode(json_encode($response), true);
            $qrUrl = null;

            if (!empty($responseArray['actions'])) {
                foreach ($responseArray['actions'] as $action) {
                    if (($action['name'] ?? '') === 'generate-qr-code-v2') {
                        $qrUrl = $action['url'] ?? null;
                        break;
                    }
                }
            }

            $pembayaran = Pembayaran::create([
                'id_booking'         => $booking->id_booking,
                'jumlah_bayar'       => $sisaBayar,
                'tanggal_bayar'      => null,
                'metode_pembayaran'  => 'qris',
                'kode_pembayaran'    => $orderId,
                'transaction_status' => 'pending',
                'created_at'         => now(),
            ]);

            PaymentGateway::create([
                'id_pembayaran'          => $pembayaran->id_pembayaran,
                'gateway_name'           => 'midtrans',
                'gateway_order_id'       => $orderId,
                'gateway_transaction_id' => $responseArray['transaction_id'] ?? null,
                'payment_type'           => 'qris',
                'qr_url'                 => $qrUrl,
                'expired_at'             => $expiredAt,
                'transaction_status'     => 'pending',
                'raw_response'           => json_encode($responseArray),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'qr_url' => $qrUrl,
                'order_id' => $orderId,
                'sisa_bayar' => 'Rp ' . number_format($sisaBayar, 0, ',', '.'),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat QRIS pelunasan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function cashPelunasan($id)
    {
        $user = Auth::user();

        $booking = Booking::with(['paket', 'pembayarans'])
            ->where('id_users', $user->id_users)
            ->findOrFail($id);

        $sudahBayar = $booking->pembayarans
            ->where('transaction_status', 'berhasil')
            ->sum('jumlah_bayar');

        $sisaBayar = max($booking->total_biaya - $sudahBayar, 0);

        if ($sisaBayar <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Booking sudah lunas.'
            ], 400);
        }

        Pembayaran::create([
            'id_booking'         => $booking->id_booking,
            'jumlah_bayar'       => $sisaBayar,
            'tanggal_bayar'      => null,
            'metode_pembayaran'  => 'cash',
            'kode_pembayaran'    => 'LUNAS-' . $booking->id_booking . '-' . time(),
            'transaction_status' => 'pending',
            'created_at'         => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan pelunasan cash berhasil. Silakan hubungi admin untuk konfirmasi.',
        ]);
    }
}