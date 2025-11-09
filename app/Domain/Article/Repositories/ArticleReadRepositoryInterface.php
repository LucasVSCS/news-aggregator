<?php

namespace App\Domain\Article\Repositories;

use App\Domain\Article\Entities\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ArticleReadRepositoryInterface
{
    public function search(
        ?string $keyword = null,
        ?array $sources = null,
        ?array $categories = null,
        ?array $authors = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        int $perPage = 20
    ): LengthAwarePaginator;

    public function findById(int $id): ?Article;

    public function getLatest(int $limit = 10): Collection;

    public function getBySource(int $sourceId, int $perPage = 20): LengthAwarePaginator;

    public function getByCategory(int $categoryId, int $perPage = 20): LengthAwarePaginator;

    public function getUniqueAuthors(int $limit = 50): Collection;
}
