<?php

namespace App\Console;

use App\Console\Commands\AggregateYrkesgrupper;
use App\Console\Commands\AggregateYrkesomraden;
use App\Console\Commands\ImportTaxonomy;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

const EXIT_OK = 0;
const EXIT_FAILURE = 1;

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
        //$schedule->command(ImportTaxonomy::class)->everyMinute();
                
        $schedule->command(AggregateYrkesgrupper::class)->dailyAt('03:45')->withoutOverlapping()->onOneServer();
        $schedule->command(AggregateYrkesomraden::class)->dailyAt('03:45')->withoutOverlapping()->onOneServer();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
