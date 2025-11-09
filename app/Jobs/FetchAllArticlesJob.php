<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Application\Services\Source\GetSourceService;

class FetchAllArticlesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $timeout = 60;

    /**
     * Execute the job - dispatches individual jobs for each source
     */
    public function handle(GetSourceService $sourceService): void
    {
        Log::info('Starting batch fetch for all active sources');

        $sources = $sourceService->getActive();

        foreach ($sources as $source) {
            // Dispatch individual job for each source
            FetchArticlesFromSourceJob::dispatch($source)->onQueue('news-fetch');
        }

        Log::info("Dispatched {$sources->count()} fetch jobs");
    }

    /**
     * Get the tags for the job.
     */
    public function tags(): array
    {
        return ['fetch-all-articles', 'batch'];
    }
}
