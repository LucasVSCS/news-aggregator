<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Domain & Infrastructure paths
use App\Domain\Source\Repositories\SourceReadRepositoryInterface;
use App\Domain\Source\Repositories\SourceWriteRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\EloquentSourceReadRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentSourceWriteRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind Source Repositories
        $this->app->bind(
            SourceReadRepositoryInterface::class,
            EloquentSourceReadRepository::class
        );

        $this->app->bind(
            SourceWriteRepositoryInterface::class,
            EloquentSourceWriteRepository::class
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
