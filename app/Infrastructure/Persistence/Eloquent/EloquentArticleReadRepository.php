<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Article\Entities\Article;
use App\Domain\Article\Repositories\ArticleReadRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class EloquentArticleReadRepository implements ArticleReadRepositoryInterface
{
    public function search(
        ?string $keyword = null,
        ?array $sources = null,
        ?array $categories = null,
        ?array $authors = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        int $perPage = 20
    ): LengthAwarePaginator {
        $query = Article::with(['source', 'categories']);

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'LIKE', "%{$keyword}%")
                    ->orWhere('description', 'LIKE', "%{$keyword}%")
                    ->orWhere('content', 'LIKE', "%{$keyword}%");
            });
        }

        if ($sources && count($sources) > 0) {
            $query->whereIn('source_id', $sources);
        }

        if ($categories && count($categories) > 0) {
            $query->whereHas('categories', function ($q) use ($categories) {
                $q->whereIn('categories.id', $categories);
            });
        }

        if ($authors && count($authors) > 0) {
            $query->whereIn('author', $authors);
        }

        if ($dateFrom) {
            $query->where('published_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('published_at', '<=', $dateTo);
        }

        return $query->latest('published_at')->paginate($perPage);
    }

    public function findById(int $id): ?Article
    {
        return Cache::remember(
            "article.{$id}",
            now()->addHours(1),
            fn () => Article::with(['source', 'categories'])->find($id)
        );
    }

    public function getLatest(int $limit = 10): Collection
    {
        return Cache::remember(
            "articles.latest.{$limit}",
            now()->addMinutes(15),
            fn () => Article::with(['source', 'categories'])
                ->latest('published_at')
                ->limit($limit)
                ->get()
        );
    }

    public function getBySource(int $sourceId, int $perPage = 20): LengthAwarePaginator
    {
        return Article::with(['source', 'categories'])
            ->where('source_id', $sourceId)
            ->latest('published_at')
            ->paginate($perPage);
    }

    public function getByCategory(int $categoryId, int $perPage = 20): LengthAwarePaginator
    {
        return Article::with(['source', 'categories'])
            ->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            })
            ->latest('published_at')
            ->paginate($perPage);
    }

    public function getUniqueAuthors(int $limit = 50): Collection
    {
        return Article::select('author')
            ->whereNotNull('author')
            ->distinct()
            ->orderBy('author')
            ->limit($limit)
            ->pluck('author');
    }
}
