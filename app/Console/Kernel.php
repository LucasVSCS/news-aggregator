<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\FetchAllArticlesJob;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Fetch news every 30 minutes
        $schedule->job(new FetchAllArticlesJob())
            ->everyThirtyMinutes()
            ->withoutOverlapping(10) // Lock expires after 10 min
            ->onOneServer()
            ->runInBackground();

        // Cleanup old articles (older than 60 days) - runs daily at 2am
        $schedule->command('articles:cleanup')
            ->dailyAt('02:00')
            ->onOneServer();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
