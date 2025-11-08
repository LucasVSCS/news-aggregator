<?php

use App\Http\Controllers\Api\SourceController;
use Illuminate\Support\Facades\Route;

Route::prefix('sources')->group(function () {
    Route::post('toggle-active/{id}', [SourceController::class, 'toggleActive']);
    Route::get('active', [SourceController::class, 'getActive']);
});
Route::apiResource('sources', SourceController::class);
