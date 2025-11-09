<?php

namespace App\Domain\Article\ValueObjects;

use DateTimeInterface;

class ArticleData
{
    public function __construct(
        public readonly string $externalId,
        public readonly int $sourceId,
        public readonly string $title,
        public readonly ?string $description,
        public readonly ?string $content,
        public readonly string $url,
        public readonly ?string $imageUrl,
        public readonly ?string $author,
        public readonly DateTimeInterface $publishedAt,
        public readonly array $categories = []
    ) {
    }

    public function toArray(): array
    {
        return [
            'external_id' => $this->externalId,
            'source_id' => $this->sourceId,
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            'url' => $this->url,
            'image_url' => $this->imageUrl,
            'author' => $this->author,
            'published_at' => $this->publishedAt,
        ];
    }
}
