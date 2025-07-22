<?php

namespace App\Console;

use App\Console\Commands\AutoArchiveScreenings;
use App\Console\Commands\DeleteExpiredReservations;
use App\Console\Commands\AutoArchiveMovies;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     * Remove this property if you prefer auto-discovery of commands.
     *
     * @var array<int, class-string<\Illuminate\Console\Command>>
     */
    protected $commands = [
        DeleteExpiredReservations::class,
        AutoArchiveMovies::class,
        AutoArchiveScreenings::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('reservations:delete-expired')->everyMinute()->withoutOverlapping();
        $schedule->command('movies:auto-archive-movies')->everyMinute()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
