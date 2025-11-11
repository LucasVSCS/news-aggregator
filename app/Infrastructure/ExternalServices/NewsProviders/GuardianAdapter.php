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

class GuardianAdapter implements NewsProviderInterface
{
    public function __construct(
        private readonly Source $source
    ) {
    }

    public function fetchArticles(array $params = []): Collection
    {
        try {
            $response = Http::get($this->source->url . '/search', [
                'api-key' => config('services.guardian.key'),
                'page-size' => $params['page_size'] ?? 50,
                'show-fields' => 'headline,standfirst,body,thumbnail,byline',
                'show-tags' => 'keyword',
                'order-by' => 'newest'
            ]);

            if ($response->failed()) {
                Log::error('Guardian API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return collect();
            }

            $data = $response->json();

            return collect($data['response']['results'] ?? [])->map(function ($article) {
                return $this->transformArticle($article);
            });
        } catch (Exception $e) {
            Log::error('Guardian fetch error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return collect();
        }
    }

    private function transformArticle(array $article): ArticleData
    {
        $fields = $article['fields'] ?? [];

        return new ArticleData(
            externalId: $article['id'],
            sourceId: $this->source->id,
            title: $fields['headline'] ?? $article['webTitle'],
            description: $fields['standfirst'] ?? null,
            content: $fields['body'] ?? null,
            url: $article['webUrl'],
            imageUrl: $fields['thumbnail'] ?? null,
            author: $fields['byline'] ?? null,
            publishedAt: Carbon::parse($article['webPublicationDate']),
            categories: $this->extractCategories($article)
        );
    }

    private function extractCategories(array $article): array
    {
        $categories = [];

        // Section name
        if (isset($article['sectionName'])) {
            $categories[] = $article['sectionName'];
        }

        // Tags
        if (isset($article['tags'])) {
            foreach ($article['tags'] as $tag) {
                if ($tag['type'] === 'keyword') {
                    $categories[] = $tag['webTitle'];
                }
            }
        }

        return array_unique($categories);
    }

    public function getName(): string
    {
        return 'The Guardian';
    }
}
