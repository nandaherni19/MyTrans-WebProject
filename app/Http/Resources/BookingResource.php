<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class BookingResource extends JsonResource
{
    public function toArray($request)
    {
        $kendaraanText = $this->kendaraans->map(function ($k) {
            return $k->nama_kendaraan;
        })->implode(', ');

        $lokasi = '-';

        if ($this->paket && $this->paket->kota) {
            $lokasi = $this->paket->kota->nama_kota;

            if ($this->paket->kota->provinsi) {
                $lokasi .= ', ' . $this->paket->kota->provinsi->nama_provinsi;
            }
        }

        $pembayaranRefund = $this->pembayarans
            ->whereIn('status_refund', ['pending', 'selesai'])
            ->first();

        return [
            'id_booking' => $this->formatted_id,

            'nama' => optional($this->pelanggan)->nama ?? '-',
            'telepon' => optional($this->pelanggan)->no_hp ?? '-',
            'email' => optional($this->pelanggan)->email ?? '-',
            'alamat' => optional($this->pelanggan)->alamat ?? '-',

            'wisata' => optional($this->paket)->nama_paket ?? '-',

            'tipe_booking' => $this->tipe_booking,

            'tipe_booking_label' =>
                $this->tipe_booking === 'open_trip'
                    ? 'Open Trip'
                    : 'Paket Wisata',

            'tipe_pembayaran_label' =>
                strtoupper($this->tipe_pembayaran ?? '-'),

            'opsi_pembayaran_label' =>
                $this->opsi_pembayaran === 'dp'
                    ? 'DP 25%'
                    : 'Lunas',

            'metode_pembayaran' =>
                optional($this->pembayaranTerakhir)->metode_pembayaran ?? '-',

            'kode_pembayaran' =>
                optional($this->pembayaranTerakhir)->kode_pembayaran ?? '-',

            'jumlah_bayar' => 'Rp ' . number_format(
                $this->pembayarans
                    ->whereIn('transaction_status', ['berhasil', 'settlement', 'capture'])
                    ->sum('jumlah_bayar'),
                0,
                ',',
                '.'
            ),

            'lokasi' => $lokasi,

            'kota_layanan' =>
                optional($this->kotaLayanan)->nama_kota ?? '-',

            'alamat_jemput' => $this->alamat_jemput ?? '-',

            'tanggal_berangkat' =>
                $this->tanggal_berangkat
                    ? Carbon::parse($this->tanggal_berangkat)
                        ->translatedFormat('d F Y')
                    : '-',

            'tanggal_kembali' =>
                $this->tanggal_kembali
                    ? Carbon::parse($this->tanggal_kembali)
                        ->translatedFormat('d F Y')
                    : '-',

            'tanggal_berangkat_raw' =>
                $this->tanggal_berangkat
                    ? Carbon::parse($this->tanggal_berangkat)
                        ->format('Y-m-d')
                    : null,

            'tanggal_kembali_raw' =>
                $this->tanggal_kembali
                    ? Carbon::parse($this->tanggal_kembali)
                        ->format('Y-m-d')
                    : null,

            'tanggal_sort' => $this->tanggal_berangkat,

            'tanggal_transaksi' =>
                $this->created_at
                    ? Carbon::parse($this->created_at)->format('Y-m-d')
                    : null,

            'jumlah_peserta' =>
                $this->jumlah_peserta . ' Orang',

            'kendaraan' => $kendaraanText ?: '-',

            'kendaraan_list' => $this->kendaraans,

            'status_booking' => $this->status_booking,

            'status_booking_label' =>
                ucfirst($this->status_booking),

            'status_pembayaran' =>
                optional($this->pembayaranTerakhir)->transaction_status
                    ?? 'pending',

            'tipe_pembayaran' =>
                $this->tipe_pembayaran ?? '-',

            'opsi_pembayaran' =>
                $this->opsi_pembayaran ?? '-',

            'tanggal_booking' =>
                $this->created_at
                    ? Carbon::parse($this->created_at)
                        ->translatedFormat('d F Y')
                    : '-',

            'harga_per_orang' =>
                $this->paket
                    ? 'Rp ' . number_format($this->paket->harga, 0, ',', '.')
                    : '-',

            'total_pembayaran' =>
                'Rp ' . number_format($this->total_biaya, 0, ',', '.'),

            'jumlah_refund' =>
                optional($pembayaranRefund)->jumlah_refund ?? 0,

            'status_refund' =>
                optional($pembayaranRefund)->status_refund ?? 'tidak_ada',

            'sisa_bayar' => max(
                ($this->total_biaya ?? 0) -
                $this->pembayarans
                    ->whereIn('transaction_status', ['berhasil', 'settlement', 'capture'])
                    ->sum('jumlah_bayar'),
                0
            ),
        ];
    }
}