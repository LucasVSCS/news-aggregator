<?php

namespace App\Application\Services\Article;

use App\Domain\Article\Entities\Article;
use App\Domain\Article\Repositories\ArticleReadRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class GetArticleService
{
    public function __construct(
        private readonly ArticleReadRepositoryInterface $articleReadRepository
    ) {
    }

    public function search(
        ?string $keyword = null,
        ?array $sources = null,
        ?array $categories = null,
        ?array $authors = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        int $perPage = 20
    ): LengthAwarePaginator {
        return $this->articleReadRepository->search(
            $keyword,
            $sources,
            $categories,
            $authors,
            $dateFrom,
            $dateTo,
            $perPage
        );
    }

    public function findById(int $id): ?Article
    {
        return $this->articleReadRepository->findById($id);
    }

    public function getLatest(int $limit = 10): Collection
    {
        return $this->articleReadRepository->getLatest($limit);
    }

    public function getBySource(int $sourceId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->articleReadRepository->getBySource($sourceId, $perPage);
    }

    public function getByCategory(int $categoryId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->articleReadRepository->getByCategory($categoryId, $perPage);
    }

    public function getUniqueAuthors(int $limit = 50): Collection
    {
        return $this->articleReadRepository->getUniqueAuthors($limit);
    }
}
