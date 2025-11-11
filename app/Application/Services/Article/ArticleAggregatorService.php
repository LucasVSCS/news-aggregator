<?php

namespace App\Application\Services\Article;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Domain\Source\Entities\Source;
use App\Domain\Article\Entities\Article;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use App\Domain\Article\Repositories\ArticleWriteRepositoryInterface;
use App\Infrastructure\ExternalServices\NewsProviders\GuardianAdapter;
use App\Infrastructure\ExternalServices\NewsProviders\NewsApiOrgAdapter;
use App\Infrastructure\ExternalServices\NewsProviders\NYTimesAdapter;

class ArticleAggregatorService
{
    public function __construct(
        private readonly ArticleWriteRepositoryInterface $articleWriteRepository,
        private readonly CategoryRepositoryInterface $categoryRepository
    ) {
    }

    /**
     * Aggregate articles from a specific source
     */
    public function aggregateFromSource(Source $source): array
    {
        try {
            $adapter = $this->createAdapter($source);

            if (!$adapter) {
                return [
                    'success' => false,
                    'source' => $source->name,
                    'error' => 'Adapter not found for source'
                ];
            }

            Log::info("Fetching articles from {$source->name}");

            $articles = $adapter->fetchArticles();

            if ($articles->isEmpty()) {
                return [
                    'success' => true,
                    'source' => $source->name,
                    'count' => 0,
                    'message' => 'No articles fetched'
                ];
            }

            // Process in transaction
            DB::beginTransaction();

            $processedCount = 0;

            foreach ($articles as $articleData) {
                // Bulk upsert articles
                $affected = $this->articleWriteRepository->bulkUpsert(collect([$articleData]));

                // Handle categories if present
                if (!empty($articleData->categories)) {
                    $this->attachCategories($articleData);
                }

                $processedCount += $affected;
            }

            DB::commit();

            Log::info("Successfully processed {$processedCount} articles from {$source->name}");

            return [
                'success' => true,
                'source' => $source->name,
                'count' => $processedCount
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Error aggregating from {$source->name}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'source' => $source->name,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Create appropriate adapter based on source slug
     */
    private function createAdapter(Source $source)
    {
        return match ($source->slug) {
            'guardian' => new GuardianAdapter($source),
            'newsapiorg' => new NewsApiOrgAdapter($source),
            'nytimes' => new NYTimesAdapter($source),
            default => null
        };
    }

    /**
     * Attach categories to article
     */
    private function attachCategories($articleData): void
    {
        if (empty($articleData->categories)) {
            return;
        }

        // Find or create categories
        $categories = $this->categoryRepository->findOrCreateMultiple($articleData->categories);

        // Find the article by external_id and source_id to sync categories
        $article = Article::where('external_id', $articleData->externalId)
            ->where('source_id', $articleData->sourceId)
            ->first();

        if ($article) {
            $this->articleWriteRepository->syncCategories(
                $article,
                $categories->pluck('id')->toArray()
            );
        }
    }
}
