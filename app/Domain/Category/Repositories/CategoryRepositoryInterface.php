<?php

namespace App\Domain\Category\Repositories;

use App\Domain\Category\Entities\Category;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
    public function getAll(): Collection;

    public function findById(int $id): ?Category;

    public function findBySlug(string $slug): ?Category;

    public function findByName(string $name): ?Category;

    public function findOrCreateByName(string $name): Category;

    public function findOrCreateMultiple(array $names): Collection;
}
