<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\FetchAllArticlesJob;
use App\Domain\Source\Entities\Source;
use App\Jobs\FetchArticlesFromSourceJob;

class FetchNewsCommand extends Command
{
    protected $signature = 'news:fetch {--source= : Specific source slug to fetch}';
    protected $description = 'Fetch news articles from all active sources';

    public function handle(): int
    {
        $this->info('Dispatching news fetch jobs...');

        if ($sourceSlug = $this->option('source')) {
            $source = Source::where('slug', $sourceSlug)->first();

            if (!$source) {
                $this->error("Source '{$sourceSlug}' not found");
                return self::FAILURE;
            }

            FetchArticlesFromSourceJob::dispatch($source);
            $this->info("Dispatched job for {$source->name}");
            return self::SUCCESS;
        }

        FetchAllArticlesJob::dispatch();
        $this->info('Dispatched batch fetch job for all sources');
        return self::SUCCESS;
    }
}
