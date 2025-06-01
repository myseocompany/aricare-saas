<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $files = Storage::files();
            foreach ($files as $file) {
                if (str_starts_with($file, 'rips_temp_')) {
                    Storage::delete($file);
                }
            }
        })->daily();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}
