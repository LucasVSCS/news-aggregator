<?php

namespace App\Domain\NewsProvider\Contracts;

use Illuminate\Support\Collection;

interface NewsProviderInterface
{
    public function fetchArticles(array $params = []): Collection;

    public function getName(): string;
}
