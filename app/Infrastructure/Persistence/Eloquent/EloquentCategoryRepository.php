<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Category\Entities\Category;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    public function getAll(): Collection
    {
        return Cache::remember(
            'categories.all',
            now()->addHours(24),
            fn () => Category::orderBy('name')->get()
        );
    }

    public function findById(int $id): ?Category
    {
        return Category::find($id);
    }

    public function findBySlug(string $slug): ?Category
    {
        return Cache::remember(
            "category.slug.{$slug}",
            now()->addHours(24),
            fn () => Category::where('slug', $slug)->first()
        );
    }

    public function findByName(string $name): ?Category
    {
        return Category::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();
    }

    public function findOrCreateByName(string $name): Category
    {
        $category = $this->findByName($name);

        if (!$category) {
            $category = Category::create([
                'name' => ucfirst($name),
                'slug' => Str::slug($name)
            ]);
            Cache::forget('categories.all');
        }

        return $category;
    }

    public function findOrCreateMultiple(array $names): Collection
    {
        return collect($names)->map(function ($name) {
            return $this->findOrCreateByName($name);
        });
    }
}
