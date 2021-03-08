<?php
/*
 * @Author: your name
 * @Date: 2020-12-02 18:06:10
 * @LastEditTime: 2021-01-25 15:28:55
 * @LastEditors: your name
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/app/Console/Kernel.php
 */

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
        $schedule->command('crontab:auto_expire_order')->everyMinute();
        $schedule->command('crontab:auto_finish_order')->daily();
        $schedule->command('crontab:auto_settle_order')->dailyAt('20:00');
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
