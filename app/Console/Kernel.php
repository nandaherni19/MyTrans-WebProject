<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\PaketWisata;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        {
            $schedule->command('app:nonaktifkan-paket')->daily();
            $schedule->command('booking:update-selesai')->dailyAt('00:00');
        }
    }
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}