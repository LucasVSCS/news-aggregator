<?php

namespace App\Application\Services\Source;

use App\Domain\Source\Entities\Source;
use App\Domain\Source\Repositories\SourceReadRepositoryInterface;
use Illuminate\Support\Collection;

class GetSourceService
{
    private SourceReadRepositoryInterface $sourceReadRepository;

    public function __construct(SourceReadRepositoryInterface $sourceReadRepository)
    {
        $this->sourceReadRepository = $sourceReadRepository;
    }

    public function getActive(): Collection
    {
        return $this->sourceReadRepository->getActive();
    }

    public function getAll(): Collection
    {
        return $this->sourceReadRepository->getAll();
    }

    public function findById(int $id): ?Source
    {
        return $this->sourceReadRepository->findById($id);
    }

    public function findBySlug(string $slug): ?Source
    {
        return $this->sourceReadRepository->findBySlug($slug);
    }

    public function getWithArticleCount(): Collection
    {
        return $this->sourceReadRepository->getWithArticleCount();
    }
}
