<?php

namespace App\Application\Services\Source;

use App\Domain\Source\Entities\Source;
use App\Domain\Source\Repositories\SourceWriteRepositoryInterface;

class CreateSourceService
{
    private SourceWriteRepositoryInterface $sourceWriteRepository;

    public function __construct(SourceWriteRepositoryInterface $sourceWriteRepository)
    {
        $this->sourceWriteRepository = $sourceWriteRepository;
    }

    public function execute(array $data): Source
    {
        $sourceData = [
            'name' => $data['name'],
            'slug' => $data['slug'],
            'url' => $data['url'],
        ];

        return $this->sourceWriteRepository->create($sourceData);
    }
}
