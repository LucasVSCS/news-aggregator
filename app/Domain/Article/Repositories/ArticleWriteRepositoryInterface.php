<?php

namespace App\Domain\Article\Repositories;

use App\Domain\Article\Entities\Article;
use Illuminate\Support\Collection;

interface ArticleWriteRepositoryInterface
{
    public function bulkUpsert(Collection $articles): int;

    public function syncCategories(Article $article, array $categoryIds): void;

    public function deleteOlderThan(int $daysOld = 30): int;

    public function clearCache(): void;
}
