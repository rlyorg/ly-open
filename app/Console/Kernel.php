<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Carbon\Carbon;

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
        // $schedule->command('inspire')->hourly();
        // $schedule->command('ly:init')->dailyAt('0:05');
        $schedule->command('sync:category')->dailyAt('0:05');
        // $schedule->command('sync:program')->dailyAt('0:08');
        // $schedule->command('ly:update')->cron('10 0,5,10 * * *');
        $schedule->command('ly:sync')->cron('1 0,10,12,16,18 * * *');
        $schedule->command('ly:sync')->cron('10 11 * * *');
        $schedule->command('ly:sync')->cron('40 16 * * *');
        $yesterday = Carbon::yesterday()->format('Y-m-d');
        $schedule->command("ly:sync {$yesterday}")->cron('1 11 * * *');
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
