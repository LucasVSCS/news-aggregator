<?php

namespace App\Infrastructure\ExternalServices\NewsProviders;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Domain\Source\Entities\Source;
use App\Domain\Article\ValueObjects\ArticleData;
use App\Domain\NewsProvider\Contracts\NewsProviderInterface;

class NYTimesAdapter implements NewsProviderInterface
{
    public function __construct(
        private readonly Source $source
    ) {
    }

    public function fetchArticles(array $params = []): Collection
    {
        try {
            $response = Http::get($this->source->url . '/svc/search/v2/articlesearch.json', [
                'api-key' => config('services.nytimes.key'),
                'sort' => 'newest',
                'page' => $params['page'] ?? 0,
            ]);

            if (!$response->successful()) {
                Log::error('NYTimes API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return collect();
            }

            $data = $response->json();

            return collect($data['response']['docs'] ?? [])->map(function ($article) {
                return $this->transformArticle($article);
            });
        } catch (Exception $e) {
            Log::error('NYTimes fetch error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return collect();
        }
    }

    private function transformArticle(array $article): ArticleData
    {
        return new ArticleData(
            externalId: $article['_id'],
            sourceId: $this->source->id,
            title: $article['headline']['main'] ?? $article['headline']['print_headline'] ?? 'No title',
            description: $article['abstract'] ?? null,
            content: $article['lead_paragraph'] ?? null,
            url: $article['web_url'],
            imageUrl: null,
            author: $this->extractAuthor($article),
            publishedAt: Carbon::parse($article['pub_date']),
            categories: $this->extractCategories($article)
        );
    }

    private function extractAuthor(array $article): ?string
    {
        if (!isset($article['byline']['original'])) {
            return null;
        }

        // Remove "By " prefix
        return str_replace('By ', '', $article['byline']['original']);
    }

    private function extractCategories(array $article): array
    {
        $categories = [];

        // Section
        if (isset($article['section_name'])) {
            $categories[] = $article['section_name'];
        }

        // News desk
        if (isset($article['news_desk'])) {
            $categories[] = $article['news_desk'];
        }

        // Keywords
        if (isset($article['keywords'])) {
            foreach ($article['keywords'] as $keyword) {
                if ($keyword['name'] === 'subject' || $keyword['name'] === 'glocations') {
                    $categories[] = $keyword['value'];
                }
            }
        }

        return array_unique($categories);
    }

    public function getName(): string
    {
        return 'The New York Times';
    }
}
