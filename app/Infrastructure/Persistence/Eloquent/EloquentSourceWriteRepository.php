<?php

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Support\Facades\Cache;
use App\Domain\Source\Entities\Source;
use App\Domain\Source\Repositories\SourceWriteRepositoryInterface;

class EloquentSourceWriteRepository implements SourceWriteRepositoryInterface
{
    /**
     * Create a new source
     *
     * @param array $data
     * @return Source
     */
    public function create(array $data): Source
    {
        $source = Source::create($data);
        $this->clearCache();
        return $source;
    }

    /**
     * Update a source
     *
     * @param Source $source
     * @param array $data
     * @return Source|null
     */
    public function update(Source $source, array $data): ?Source
    {
        $updated = $source->update($data);
        if ($updated) {
            $this->clearCache();
        }
        return $source;
    }

    /**
     * Toggle source active status
     *
     * @param Source $source
     * @return bool
     */
    public function toggleActive(Source $source): ?Source
    {
        $source->is_active = !$source->is_active;
        $saved = $source->save();

        if ($saved) {
            $this->clearCache();
        }

        return $source;
    }

    /**
     * Delete a source
     *
     * @param Source $source
     * @return bool
     */
    public function delete(Source $source): bool
    {
        $deleted = $source->delete();
        if ($deleted) {
            $this->clearCache();
        }
        return $deleted;
    }

    /**
     * Clear all source-related cache
     *
     * @return void
     */
    public function clearCache(): void
    {
        Cache::forget('sources.active');
        Cache::forget('sources.all');
        Cache::forget('sources.with_count');
    }
}
