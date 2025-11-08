<?php

namespace App\Application\Services\Source;

use App\Domain\Source\Entities\Source;
use App\Domain\Source\Repositories\SourceReadRepositoryInterface;
use App\Domain\Source\Repositories\SourceWriteRepositoryInterface;

class UpdateSourceService
{
    private SourceWriteRepositoryInterface $sourceWriteRepository;
    private SourceReadRepositoryInterface $sourceReadRepository;

    public function __construct(SourceWriteRepositoryInterface $sourceWriteRepository, SourceReadRepositoryInterface $sourceReadRepository)
    {
        $this->sourceWriteRepository = $sourceWriteRepository;
        $this->sourceReadRepository = $sourceReadRepository;
    }

    public function update(int $id, array $data): ?Source
    {
        $source = $this->sourceReadRepository->findById($id);
        if (!$source) {
            return null;
        }

        return $this->sourceWriteRepository->update($source, $data);
    }

    public function toggleActive(int $id): ?Source
    {
        $source = $this->sourceReadRepository->findById($id);
        if (!$source) {
            return null;
        }

        return $this->sourceWriteRepository->toggleActive($source);
    }
}
