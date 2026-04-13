<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PaketWisata;

class NonaktifkanPaket extends Command
{
    protected $signature = 'app:nonaktifkan-paket';
    protected $description = 'Nonaktifkan paket yang sudah lewat tanggal';

    public function handle()
    {
        PaketWisata::where('tanggal_keberangkatan', '<', now())
            ->update(['status' => 'nonaktif']);

        $this->info('Paket lewat tanggal berhasil dinonaktifkan');
    }
}