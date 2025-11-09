<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// Domain & Infrastructure paths
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use App\Domain\Source\Repositories\SourceReadRepositoryInterface;
use App\Domain\Source\Repositories\SourceWriteRepositoryInterface;
use App\Domain\Article\Repositories\ArticleReadRepositoryInterface;
use App\Domain\Article\Repositories\ArticleWriteRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\EloquentCategoryRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentSourceReadRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentSourceWriteRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentArticleReadRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentArticleWriteRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Source Repositories
        $this->app->bind(
            SourceReadRepositoryInterface::class,
            EloquentSourceReadRepository::class
        );
        $this->app->bind(
            SourceWriteRepositoryInterface::class,
            EloquentSourceWriteRepository::class
        );

        // Article Repositories
        $this->app->bind(
            ArticleReadRepositoryInterface::class,
            EloquentArticleReadRepository::class
        );
        $this->app->bind(
            ArticleWriteRepositoryInterface::class,
            EloquentArticleWriteRepository::class
        );

        // Category Repositories
        $this->app->bind(
            CategoryRepositoryInterface::class,
            EloquentCategoryRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
