<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Kendaraan;
use App\Models\PaketWisata;
use App\Models\Pembayaran;
use App\Models\Penumpang;
use App\Models\User;
use App\Http\Resources\BookingResource;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DataBookingController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('page', 'index');
        $id = $request->query('id');

        $bookings = Booking::with([
            'paket.kota.provinsi',
            'pelanggan',
            'kendaraans',
            'kotaLayanan',
            'pembayaranTerakhir',
            'pembayarans'
        ])->get();

        $bookingData = collect(
            BookingResource::collection($bookings)->resolve()
        )
            ->sortByDesc('tanggal_sort')
            ->values()
            ->mapWithKeys(fn($item) => [
                $item['id_booking'] => $item
            ]);

        $currentId = $id ?? ($bookingData->keys()->first() ?? null);
        $current = $currentId
            ? ($bookingData[$currentId] ?? null)
            : null;

        return view('dashboard.superadmin.kelola-data-booking', [
            'page' => $page,
            'id' => $id,
            'bookingData' => $bookingData,
            'current' => $current,

            'pakets' => PaketWisata::with([
                'kota.provinsi',
                'kendaraan',
                'kotaLayanan'
            ])
                ->where('status', 'aktif')
                ->get(),

            'users' => User::where('role', 'user')->get(),

            'allKendaraan' => Kendaraan::all(),

            'kendaraanDipakaiIds' => [],
        ]);
    }

    public function update(Request $request, $id)
    {
        if (!str_starts_with($id, 'BK')) {
            return back()->with('error', 'ID booking tidak valid.');
        }

        $bookingId = is_numeric($id) ? $id : (int) str_replace('BK', '', $id);
        $booking = Booking::findOrFail($bookingId);

        $request->validate([
            'status_booking' => 'required|in:pending,aktif,selesai,batal',
            'status_pembayaran' => 'required|in:pending,berhasil,gagal,expired',
            'kendaraan_checked' => 'nullable|array',
            'kendaraan_checked.*' => 'exists:ms_kendaraan,id_kendaraan',
        ]);

        $statusPembayaran = $request->status_pembayaran;

        // Status booking otomatis berdasarkan status pembayaran
        if ($statusPembayaran === 'berhasil') {
            $statusBooking = $booking->tanggal_kembali && Carbon::parse($booking->tanggal_kembali)->isPast()
                ? 'selesai'
                : 'aktif';
        } elseif (in_array($statusPembayaran, ['expired', 'gagal'])) {
            $statusBooking = 'batal';
        } else {
            $statusBooking = $request->status_booking;
        }

        $dataUpdate = [
            'status_booking' => $statusBooking,
            'updated_at' => now(),
        ];

        if ($booking->tipe_booking === 'paket') {
            $dataUpdate['tanggal_berangkat'] = $request->tanggal_berangkat;
            $dataUpdate['tanggal_kembali'] = $request->tanggal_kembali;
        }

        $booking->update($dataUpdate);
        if ($booking->pembayaranTerakhir) {

            $dataPembayaran = [
                'transaction_status' => $statusPembayaran,
            ];

            // kalau pembayaran berhasil dan tanggal belum ada
            if ($statusPembayaran === 'berhasil' && !$booking->pembayaranTerakhir->tanggal_bayar) {
                $dataPembayaran['tanggal_bayar'] = now();
            }

            $booking->pembayaranTerakhir->update($dataPembayaran);
        }

        // Sync kendaraan (hanya untuk paket)
        if ($booking->tipe_booking === 'paket') {
            $syncData = [];

            if ($request->kendaraan_checked) {
                foreach ($request->kendaraan_checked as $idKendaraan) {
                    $sudahDipakai = DB::table('tr_booking_kendaraan')
                        ->join('ms_booking', 'tr_booking_kendaraan.id_booking', '=', 'ms_booking.id_booking')
                        ->where('tr_booking_kendaraan.id_kendaraan', $idKendaraan)
                        ->where('ms_booking.id_booking', '!=', $booking->id_booking)
                        ->where('ms_booking.created_at', '<=', $booking->created_at)
                        ->whereIn('ms_booking.status_booking', ['pending', 'aktif'])
                        ->where(function ($q) use ($booking) {
                            $q->whereBetween('ms_booking.tanggal_berangkat', [
                                $booking->tanggal_berangkat,
                                $booking->tanggal_kembali,
                            ])
                                ->orWhereBetween('ms_booking.tanggal_kembali', [
                                    $booking->tanggal_berangkat,
                                    $booking->tanggal_kembali,
                                ])
                                ->orWhere(function ($q2) use ($booking) {
                                    $q2->where('ms_booking.tanggal_berangkat', '<=', $booking->tanggal_berangkat)
                                        ->where('ms_booking.tanggal_kembali', '>=', $booking->tanggal_kembali);
                                });
                        })
                        ->exists();

                    if ($sudahDipakai) {
                        return back()->withErrors([
                            'kendaraan' => 'Kendaraan yang dipilih sudah dipakai pada tanggal tersebut.'
                        ])->withInput();
                    }

                    $syncData[] = $idKendaraan;
                }
            }

            $booking->kendaraans()->sync($syncData);
        }

        return redirect()
            ->route('booking.index')
            ->with('success', 'Update berhasil.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_users' => 'required|exists:ms_users,id_users',
            'id_paket' => 'required|exists:ms_paket_wisata,id_paket',
            'jumlah_peserta' => 'required|integer|min:1',
            'tipe_pembayaran' => 'required|in:qris,cash',
            'opsi_pembayaran' => 'required|in:dp,lunas',
            'status_pembayaran_awal' => 'nullable|in:pending,berhasil',
            'id_kendaraan' => 'nullable|array',
            'id_kendaraan.*' => 'exists:ms_kendaraan,id_kendaraan',
            'id_kota_layanan' => 'required|exists:ms_kota,id_kota',
            'alamat_jemput' => 'nullable|string|max:255',
            'tanggal_berangkat' => 'nullable|date',
            'tanggal_kembali' => 'nullable|date|after_or_equal:tanggal_berangkat',
        ]);

        $paket = PaketWisata::with('kendaraan')->findOrFail($request->id_paket);
        $jumlahPeserta = (int) $request->jumlah_peserta;

        if ($paket->tipe === 'open_trip') {
            if ($jumlahPeserta > $paket->sisa_kursi) {
                return back()->withErrors([
                    'jumlah_peserta' => 'Sisa kursi open trip hanya ' . $paket->sisa_kursi . ' kursi.'
                ])->withInput();
            }

            // Validasi tanggal tidak lewat (double check di backend)
            if (Carbon::parse($paket->tanggal_berangkat)->isPast()) {
                return back()->withErrors([
                    'id_paket' => 'Paket open trip ini sudah tidak tersedia karena tanggal sudah lewat.'
                ])->withInput();
            }

            $tanggalBerangkat = $paket->tanggal_berangkat;
            $tanggalKembali = $paket->tanggal_kembali;
            $idKendaraan = $paket->id_kendaraan;
            $idKendaraanList = null;
            $totalBiaya = $paket->harga * $jumlahPeserta;

        } else {
            if ($jumlahPeserta < $paket->min_peserta) {
                return back()->withErrors([
                    'jumlah_peserta' => 'Minimal peserta paket ini adalah ' . $paket->min_peserta . ' orang.'
                ])->withInput();
            }

            if (!$request->tanggal_berangkat || !$request->tanggal_kembali) {
                return back()->withErrors([
                    'tanggal_berangkat' => 'Tanggal wajib diisi untuk paket wisata.'
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
            $tanggalKembali = $request->tanggal_kembali;
            $idKendaraan = null;
            $totalBiaya = ($paket->harga * $jumlahPeserta) + $totalSewaKendaraan;
        }

        $jumlahBayar = $request->opsi_pembayaran === 'dp'
            ? $totalBiaya * 0.25
            : $totalBiaya;

        DB::beginTransaction();

        try {
            $booking = Booking::create([
                'jumlah_peserta' => $jumlahPeserta,
                'total_biaya' => $totalBiaya,
                'status_booking' => 'pending',
                'tipe_booking' => $paket->tipe,
                'tipe_pembayaran' => $request->tipe_pembayaran,
                'opsi_pembayaran' => $request->opsi_pembayaran,
                'id_paket' => $paket->id_paket,
                'id_users' => $request->id_users,
                'id_kota_layanan' => $request->id_kota_layanan,
                'alamat_jemput' => $request->alamat_jemput,
                'tanggal_berangkat' => $tanggalBerangkat,
                'tanggal_kembali' => $tanggalKembali,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $statusAwal = 'pending';
            if ($request->tipe_pembayaran === 'cash' && $request->status_pembayaran_awal === 'berhasil') {
                $statusAwal = 'berhasil';
            }

            // Tentukan status booking berdasarkan status pembayaran awal
            $statusBookingAwal = 'pending';
            if ($statusAwal === 'berhasil') {
                $tglKembali = $tanggalKembali ?? null;
                $statusBookingAwal = ($tglKembali && Carbon::parse($tglKembali)->isPast())
                    ? 'selesai'
                    : 'aktif';
            }

            // Update status booking jika perlu
            if ($statusBookingAwal !== 'pending') {
                $booking->update([
                    'status_booking' => $statusBookingAwal,
                    'updated_at' => now(),
                ]);
            }

            if ($paket->tipe === 'open_trip') {
                DB::table('tr_booking_kendaraan')->insert([
                    'id_booking' => $booking->id_booking,
                    'id_kendaraan' => $idKendaraan,

                ]);

                for ($i = 0; $i < $jumlahPeserta; $i++) {
                    Penumpang::create([
                        'id_booking' => $booking->id_booking,
                        'id_users' => $request->id_users,
                    ]);
                }
            } else {
                foreach ($idKendaraanList as $idK) {
                    DB::table('tr_booking_kendaraan')->insert([
                        'id_booking' => $booking->id_booking,
                        'id_kendaraan' => $idK,

                    ]);
                }
            }

            Pembayaran::create([
                'id_booking' => $booking->id_booking,
                'jumlah_bayar' => $jumlahBayar,
                'tanggal_bayar' => $statusAwal === 'berhasil' ? now() : null,
                'metode_pembayaran' => $request->tipe_pembayaran,
                'transaction_status' => $statusAwal,
                'kode_pembayaran' => 'TRX-' . $booking->id_booking . '-' . time(),
                'created_at' => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('booking.index')
                ->with('success', 'Booking manual berhasil dibuat.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat booking: ' . $e->getMessage())->withInput();
        }
    }

    public function kendaraanTersedia(Request $request)
    {
        $tanggalBerangkat = $request->tanggal_berangkat;
        $tanggalKembali = $request->tanggal_kembali;
        $currentBookingId = $request->current_booking_id;

        if (!$tanggalBerangkat || !$tanggalKembali) {
            return response()->json(Kendaraan::all());
        }

        $dipakaiIds = DB::table('tr_booking_kendaraan')
            ->join('ms_booking', 'tr_booking_kendaraan.id_booking', '=', 'ms_booking.id_booking')
            ->whereIn('ms_booking.status_booking', ['pending', 'aktif'])
            ->when($currentBookingId, function ($q) use ($currentBookingId) {
                $q->where('ms_booking.id_booking', '!=', $currentBookingId);
            })
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

    public function lunasi(Request $request, $id)
    {
        if (!str_starts_with($id, 'BK')) {
            return back()->with('error', 'ID booking tidak valid.');
        }

        $bookingId = (int) str_replace('BK', '', $id);
        $booking = Booking::with('pembayaranTerakhir')->findOrFail($bookingId);

        if ($booking->status_booking !== 'aktif') {
            return back()->with('error', 'Booking tidak dalam status aktif.');
        }

        if ($booking->opsi_pembayaran !== 'dp') {
            return back()->with('error', 'Booking ini bukan DP.');
        }

        $sudahBayar = $booking->pembayarans()
            ->whereIn('transaction_status', ['berhasil', 'settlement', 'capture'])
            ->sum('jumlah_bayar');
        $sisa = $booking->total_biaya - $sudahBayar;

        if ($sisa <= 0) {
            return back()->with('error', 'Pembayaran sudah lunas.');
        }

        // Metode pelunasan bebas pilih, default cash
        $metodePelunasan = $request->metode_pelunasan ?? 'cash';

        DB::beginTransaction();
        try {
            // Tambah record pembayaran baru untuk sisa
            Pembayaran::create([
                'id_booking' => $booking->id_booking,
                'jumlah_bayar' => $sisa,
                'tanggal_bayar' => now(),
                'metode_pembayaran' => $metodePelunasan,
                'transaction_status' => 'berhasil',
                'kode_pembayaran' => 'LUNAS-' . $booking->id_booking . '-' . time(),
                'created_at' => now(),
            ]);

            // Update opsi pembayaran jadi lunas
            $booking->update([
                'opsi_pembayaran' => 'lunas',
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('dashboard.superadmin.kelola-data-booking', ['page' => 'detail', 'id' => $id])
                ->with('success', 'Pelunasan berhasil dikonfirmasi.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal melunasi: ' . $e->getMessage());
        }
    }

    public function getSisa($id)
    {
        if (!str_starts_with($id, 'BK')) {
            return response()->json(['error' => 'ID tidak valid'], 400);
        }

        $bookingId = (int) str_replace('BK', '', $id);
        $booking = Booking::findOrFail($bookingId);

        $sudahBayar = $booking->pembayarans()
            ->whereIn('transaction_status', ['berhasil', 'settlement', 'capture'])
            ->sum('jumlah_bayar');
        $booking->pembayarans()->sum('jumlah_bayar');
        $sisa = $booking->total_biaya - $sudahBayar;

        return response()->json([
            'sisa' => (int) $sisa,
            'sisa_format' => 'Rp ' . number_format($sisa, 0, ',', '.'),
            'sudah_bayar' => (int) $sudahBayar,
            'total_biaya' => (int) $booking->total_biaya,
        ]);
    }

    public function qrisPelunasan(Request $request, $id)
    {
        if (!str_starts_with($id, 'BK')) {
            return response()->json(['error' => 'ID tidak valid'], 400);
        }

        $bookingId = (int) str_replace('BK', '', $id);

        $booking = Booking::with(['pembayarans', 'pelanggan'])->findOrFail($bookingId);

        $sudahBayar = $booking->pembayarans()
            ->whereIn('transaction_status', ['berhasil', 'settlement', 'capture'])
            ->sum('jumlah_bayar');

        $sisa = max($booking->total_biaya - $sudahBayar, 0);

        if ($sisa <= 0) {
            return response()->json(['error' => 'Sudah lunas'], 400);
        }

        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $orderId = 'LUNAS-' . $booking->id_booking . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $sisa,
            ],
            'payment_type' => 'qris',
            'qris' => ['acquirer' => 'gopay'],
            'customer_details' => [
                'first_name' => optional($booking->pelanggan)->nama ?? 'Pelanggan',
                'email' => optional($booking->pelanggan)->email ?? 'noemail@example.com', // ← fix: jangan kosong
                'phone' => optional($booking->pelanggan)->no_hp ?? '08000000000',         // ← fix: jangan kosong
            ],
            'custom_expiry' => [                                      // ← tambahan
                'order_time' => now()->format('Y-m-d H:i:s O'),
                'expiry_duration' => 24,
                'unit' => 'hour',
            ],
        ];

        try {
            $response = \Midtrans\CoreApi::charge($params);
            $responseArray = json_decode(json_encode($response), true);

            // Debug: log full response
            \Log::info('Midtrans QRIS Pelunasan Response', $responseArray);

            $qrUrl = null;

            // Cek di actions
            if (!empty($responseArray['actions'])) {
                foreach ($responseArray['actions'] as $action) {
                    if (
                        in_array(($action['name'] ?? ''), [
                            'generate-qr-code',
                            'generate-qr-code-v2',
                        ])
                    ) {
                        $qrUrl = $action['url'] ?? null;
                        break;
                    }
                }
            }

            // Fallback: kadang Midtrans sandbox taruh di qr_string atau actions berbeda
            if (!$qrUrl && !empty($responseArray['qr_string'])) {
                // Generate QR dari string menggunakan Google Charts API sebagai fallback
                $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data='
                    . urlencode($responseArray['qr_string']);
            }

            if (!$qrUrl) {
                \Log::error('QR URL tidak ditemukan', $responseArray);
                return response()->json([
                    'error' => 'QR URL tidak ditemukan dari Midtrans',
                    'debug' => $responseArray  // tampil di console browser
                ], 500);
            }

            // Simpan pembayaran
            $pembayaran = Pembayaran::create([
                'id_booking' => $booking->id_booking,
                'jumlah_bayar' => $sisa,
                'tanggal_bayar' => null,
                'metode_pembayaran' => 'qris',
                'transaction_status' => 'pending',
                'kode_pembayaran' => $orderId,
                'created_at' => now(),
            ]);

            // Simpan ke payment gateway juga (sebelumnya tidak ada - ini bug!)
            \App\Models\PaymentGateway::create([
                'id_pembayaran' => $pembayaran->id_pembayaran,
                'gateway_name' => 'midtrans',
                'gateway_order_id' => $orderId,
                'gateway_transaction_id' => $responseArray['transaction_id'] ?? null,
                'payment_type' => 'qris',
                'qr_url' => $qrUrl,
                'expired_at' => now()->addDay(),
                'transaction_status' => 'pending',
                'raw_response' => json_encode($responseArray),
            ]);

            return response()->json([
                'qr_url' => $qrUrl,
                'order_id' => $orderId,
                'sisa' => 'Rp ' . number_format($sisa, 0, ',', '.'),
            ]);

        } catch (\Throwable $e) {
            \Log::error('Midtrans Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function batal($id)
    {
        if (!str_starts_with($id, 'BK')) {
            return back()->with('error', 'ID booking tidak valid.');
        }

        $bookingId = (int) str_replace('BK', '', $id);
        $booking = Booking::with(['pembayarans'])->findOrFail($bookingId);

        if (in_array($booking->status_booking, ['selesai', 'batal'])) {
            return back()->with('error', 'Booking tidak bisa dibatalkan.');
        }

        // Hitung TOTAL semua pembayaran berhasil
        $sudahBayar = $booking->pembayarans()
            ->whereIn('transaction_status', ['berhasil', 'settlement', 'capture'])
            ->sum('jumlah_bayar');

        // Hitung refund jika pembayaran sudah lunas
        $jumlahRefund = 0;

        if ($sudahBayar >= $booking->total_biaya) {
            $jumlahRefund = floor($sudahBayar * 0.85);
        }

        // Update semua pembayaran pending → gagal
        $booking->pembayarans()
            ->where('transaction_status', 'pending')
            ->update(['transaction_status' => 'gagal']);

        // Update booking
        $booking->update([
            'status_booking' => 'batal',
            'updated_at' => now(),
        ]);

        // Simpan refund hanya di pembayaran PERTAMA yang berhasil
        // Set semua ke tidak_ada dulu
        $booking->pembayarans()
            ->whereIn('transaction_status', ['berhasil', 'settlement', 'capture'])
            ->update([
                'jumlah_refund' => null,
                'status_refund' => 'tidak_ada',
            ]);

        // Baru set refund di pembayaran pertama saja
        if ($jumlahRefund > 0) {
            $pembayaranPertama = $booking->pembayarans()
                ->whereIn('transaction_status', ['berhasil', 'settlement', 'capture'])
                ->oldest('id_pembayaran')
                ->first();

            if ($pembayaranPertama) {
                $pembayaranPertama->update([
                    'jumlah_refund' => $jumlahRefund,
                    'status_refund' => 'pending',
                ]);
            }
        }

        $pesan = 'Booking berhasil dibatalkan.';
        if ($jumlahRefund > 0) {
            $pesan .= ' Refund 85%: Rp ' . number_format($jumlahRefund, 0, ',', '.') . '. Silakan transfer ke pelanggan.';
        } else {
            $pesan .= ' Tidak ada refund (DP hangus).';
        }

        return redirect()
            ->route('booking.index', [
                'page' => 'detail',
                'id' => $id
            ])
            ->with('success', $pesan);
    }

    public function refundSelesai($id)
    {
        if (!str_starts_with($id, 'BK')) {
            return back()->with('error', 'ID booking tidak valid.');
        }

        $bookingId = (int) str_replace('BK', '', $id);

        $pembayaran = Pembayaran::where('id_booking', $bookingId)
            ->where('status_refund', 'pending')
            ->latest('id_pembayaran')
            ->first();

        if (!$pembayaran) {
            return back()->with('error', 'Tidak ada refund pending.');
        }

        $pembayaran->update([
            'status_refund' => 'selesai',
        ]);

        return back()->with('success', 'Refund berhasil ditandai selesai.');
    }
    public function show($id)
    {
        return redirect()->route('booking.index', [
            'page' => 'detail',
            'id' => $id
        ]);
    }
    public function edit($id)
{
    return redirect()->route('booking.index', [
        'page' => 'edit',
        'id' => $id
    ]);
}
}