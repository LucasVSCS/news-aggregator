<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use App\Domain\Source\Entities\Source;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Application\Services\Article\ArticleAggregatorService;

class FetchArticlesFromSourceJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900]; // 1min, 5min, 15min
    public $timeout = 300; // 5 minutes

    /**
     * Create a new job instance.
     */
    public function __construct(public readonly Source $source) {}

    /**
     * Execute the job.
     */
    public function handle(ArticleAggregatorService $aggregator): void
    {
        Log::info("Starting article fetch job for source: {$this->source->name}");

        $result = $aggregator->aggregateFromSource($this->source);

        if ($result['success']) {
            Log::info('Completed article fetch', $result);
            return;
        }

        Log::error('Failed article fetch', $result);

        throw new Exception($result['error'] ?? 'Unknown error');
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Job failed permanently for source: {$this->source->name}", [
            'error' => $exception->getMessage(),
            'source_id' => $this->source->id
        ]);
    }

    /**
     * Get the tags for the job.
     */
    public function tags(): array
    {
        return [
            'source:' . $this->source->slug,
            'fetch-articles'
        ];
    }
}
