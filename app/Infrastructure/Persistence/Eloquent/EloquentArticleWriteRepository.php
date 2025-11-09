<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Article\Entities\Article;
use App\Domain\Article\Repositories\ArticleWriteRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class EloquentArticleWriteRepository implements ArticleWriteRepositoryInterface
{
    public function bulkUpsert(Collection $articles): int
    {
        if ($articles->isEmpty()) {
            return 0;
        }

        $data = $articles->map(function ($article) {
            $articleArray = is_array($article) ? $article : $article->toArray();

            return [
                'external_id' => $articleArray['external_id'],
                'source_id' => $articleArray['source_id'],
                'title' => $articleArray['title'],
                'description' => $articleArray['description'] ?? null,
                'content' => $articleArray['content'] ?? null,
                'url' => $articleArray['url'],
                'image_url' => $articleArray['image_url'] ?? null,
                'author' => $articleArray['author'] ?? null,
                'published_at' => $articleArray['published_at'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        $affected = Article::upsert(
            $data,
            ['external_id', 'source_id'],
            ['title', 'description', 'content', 'url', 'image_url', 'author', 'published_at', 'updated_at']
        );

        $this->clearCache();

        return $affected;
    }

    public function syncCategories(Article $article, array $categoryIds): void
    {
        $article->categories()->sync($categoryIds);
        Cache::forget("article.{$article->id}");
    }

    public function deleteOlderThan(int $daysOld = 30): int
    {
        $deleted = Article::where('published_at', '<', now()->subDays($daysOld))->delete();

        if ($deleted > 0) {
            $this->clearCache();
        }

        return $deleted;
    }

    public function clearCache(): void
    {
        Cache::forget('articles.latest.10');
        Cache::forget('articles.latest.20');
        Cache::forget('articles.latest.50');
    }
}
