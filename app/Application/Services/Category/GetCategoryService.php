<?php

namespace App\Application\Services\Category;

use App\Domain\Category\Entities\Category;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use Illuminate\Support\Collection;

class GetCategoryService
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository
    ) {
    }

    public function getAll(): Collection
    {
        return $this->categoryRepository->getAll();
    }

    public function findById(int $id): ?Category
    {
        return $this->categoryRepository->findById($id);
    }

    public function findBySlug(string $slug): ?Category
    {
        return $this->categoryRepository->findBySlug($slug);
    }
}
