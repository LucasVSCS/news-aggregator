<?php

namespace App\Domain\Source\Repositories;

use Illuminate\Support\Collection;
use App\Domain\Source\Entities\Source;

interface SourceReadRepositoryInterface
{
    public function findById(int $id): ?Source;
    public function findBySlug(string $slug): ?Source;
    public function getActive(): Collection;
    public function getAll(): Collection;
}
