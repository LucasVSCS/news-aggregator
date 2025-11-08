<?php

namespace App\Application\Services\Source;

use App\Domain\Source\Repositories\SourceReadRepositoryInterface;
use App\Domain\Source\Repositories\SourceWriteRepositoryInterface;

class DeleteSourceService
{
    private SourceWriteRepositoryInterface $sourceWriteRepository;
    private SourceReadRepositoryInterface $sourceReadRepository;


    public function __construct(SourceWriteRepositoryInterface $sourceWriteRepository, SourceReadRepositoryInterface $sourceReadRepository)
    {
        $this->sourceWriteRepository = $sourceWriteRepository;
        $this->sourceReadRepository = $sourceReadRepository;
    }

    public function execute(int $id): bool
    {
        $source = $this->sourceReadRepository->findById($id);
        if (!$source) {
            return false;
        }

        return $this->sourceWriteRepository->delete($source);
    }
}
