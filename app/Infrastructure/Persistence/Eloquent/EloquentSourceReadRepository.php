<?php

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use App\Domain\Source\Entities\Source;
use App\Domain\Source\Repositories\SourceReadRepositoryInterface;

class EloquentSourceReadRepository implements SourceReadRepositoryInterface
{
    /**
     * Get all active sources
     *
     * @return Collection
     */
    public function getActive(): Collection
    {
        return Cache::remember(
            'sources.active',
            now()->addHours(24),
            fn () => Source::where('is_active', true)->get()
        );
    }

    /**
     * Get all sources
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return Cache::remember(
            'sources.all',
            now()->addHours(24),
            fn () => Source::orderBy('name')->get()
        );
    }

    public function findById(int $id): ?Source
    {
        return Source::find($id);
    }

    /**
     * Find source by slug
     *
     * @param string $slug
     * @return Source|null
     */
    public function findBySlug(string $slug): ?Source
    {
        return Cache::remember(
            "source.slug.{$slug}",
            now()->addHours(24),
            fn () => Source::where('slug', $slug)->first()
        );
    }

    /**
     * Get sources with article count
     *
     * @return Collection
     */
    public function getWithArticleCount(): Collection
    {
        return Cache::remember(
            'sources.with_count',
            now()->addHours(1),
            fn () => Source::withCount('articles')->orderBy('name')->get()
        );
    }
}
