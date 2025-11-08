<?php

namespace App\Domain\Source\Repositories;

use App\Domain\Source\Entities\Source;


interface SourceWriteRepositoryInterface
{
    public function create(array $source): Source;
    public function update(Source $source, array $data): ?Source;
    public function toggleActive(Source $source): ?Source;
    public function delete(Source $source): bool;
    public function clearCache(): void;
}
