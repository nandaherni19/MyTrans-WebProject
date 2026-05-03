<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;

class UpdateBookingSelesai extends Command
{
    protected $signature   = 'booking:update-selesai';
    protected $description = 'Update status booking menjadi selesai jika tanggal kembali sudah lewat';

    public function handle()
    {
        $updated = Booking::where('status_booking', 'aktif')
            ->where('tanggal_kembali', '<', Carbon::today())
            ->whereHas('pembayaranTerakhir', function ($q) {
                $q->where('transaction_status', 'berhasil');
            })
            ->update([
                'status_booking' => 'selesai',
                'updated_at'     => now(),
            ]);

        $this->info("$updated booking diupdate menjadi selesai.");
    }
}