<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SourceController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CategoryController;

Route::prefix('v1')->group(function () {
    // Sources
    Route::prefix('sources')->group(function () {
        Route::post('toggle-active/{id}', [SourceController::class, 'toggleActive']);
        Route::get('active', [SourceController::class, 'getActive']);
    });
    Route::apiResource('sources', SourceController::class);

    // Articles
    Route::prefix('articles')->group(function () {
        Route::get('latest', [ArticleController::class, 'latest']);
        Route::get('source/{sourceId}', [ArticleController::class, 'bySource']);
        Route::get('category/{categoryId}', [ArticleController::class, 'byCategory']);
        Route::get('authors', [ArticleController::class, 'authors']);
    });
    Route::apiResource('articles', ArticleController::class)->only(['index', 'show']);

    // Categories
    Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
});
