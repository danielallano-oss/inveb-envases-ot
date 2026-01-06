<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('backup:clean')->daily()->at('00:00');
        $schedule->command('backup:run')->daily()->at('01:00');


        $schedule->command('command:CotizacionesPorAprobacion')
            ->weekly()
            ->days([1, 2, 3, 4, 5])
            ->at('08:00');


            $schedule->command('command:ActualizacionMatriz')
            ->weekly()
            ->days([5])
            ->at('05:00');

        $schedule->command('CleanOldDataAuditCommand')->daily()->at('02:00');
        $schedule->command('UpdateUserDataAuditCommand')->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
