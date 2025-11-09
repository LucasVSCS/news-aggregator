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

class NewsApiOrgAdapter implements NewsProviderInterface
{
    public function __construct(
        private readonly Source $source
    ) {}

    public function fetchArticles(array $params = []): Collection
    {
        try {
            $response = Http::get($this->source->url . '/v2/top-headlines', [
                'apiKey' => config('services.newsapiorg.key'),
                'country' => $params['country'] ?? 'us',
                'pageSize' => $params['page_size'] ?? 100,
                'page' => $params['page'] ?? 1,
            ]);

            if (!$response->successful()) {
                Log::error('NewsAPI error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return collect();
            }

            $data = $response->json();

            return collect($data['articles'] ?? [])->map(function ($article) {
                return $this->transformArticle($article);
            })->filter(fn($article) => $article !== null);
        } catch (Exception $e) {
            Log::error('NewsAPI fetch error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return collect();
        }
    }

    private function transformArticle(array $article): ?ArticleData
    {
        // NewsAPI sometimes returns removed articles
        if ($article['title'] === '[Removed]') {
            return null;
        }

        // Create a unique external ID based on the URL
        $externalId = md5($article['url']);

        return new ArticleData(
            externalId: $externalId,
            sourceId: $this->source->id,
            title: $article['title'],
            description: $article['description'] ?? null,
            content: $article['content'] ?? null,
            url: $article['url'],
            imageUrl: $article['urlToImage'] ?? null,
            author: $article['author'] ?? $article['source']['name'] ?? null,
            publishedAt: Carbon::parse($article['publishedAt']),
            categories: [] // NewsAPI does not provide categories
        );
    }

    public function getName(): string
    {
        return 'NewsAPI.org';
    }
}
