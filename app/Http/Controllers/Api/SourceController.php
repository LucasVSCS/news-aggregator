<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\SourceRequest;
use App\Http\Resources\SourceResource;
use Symfony\Component\HttpFoundation\Response;
use App\Application\Services\Source\GetSourceService;
use App\Application\Services\Source\CreateSourceService;
use App\Application\Services\Source\DeleteSourceService;
use App\Application\Services\Source\UpdateSourceService;
use App\Domain\Source\Exceptions\SourceNotFoundException;

class SourceController extends Controller
{
    public function __construct(
        private GetSourceService $getSourceService,
        private CreateSourceService $createSourceService,
        private UpdateSourceService $updateSourceService,
        private DeleteSourceService $deleteSourceService
    ) {
    }

    public function index(): JsonResponse
    {
        $sources = $this->getSourceService->getAll();
        return response()->json($sources, Response::HTTP_OK);
    }

    public function getActive(): JsonResponse
    {
        $sources = $this->getSourceService->getActive();
        return response()->json($sources, Response::HTTP_OK);
    }

    public function store(SourceRequest $request): JsonResponse
    {
        $source = $this->createSourceService->execute($request->validated());
        return response()->json($source, Response::HTTP_CREATED);
    }

    public function update(int $id, SourceRequest $request): JsonResponse
    {
        $source = $this->updateSourceService->update($id, $request->validated());

        if (!$source) {
            throw new SourceNotFoundException();
        }

        return response()->json($source, Response::HTTP_OK);
    }

    public function toggleActive(int $id): JsonResponse
    {
        $source = $this->updateSourceService->toggleActive($id);

        if (!$source) {
            throw new SourceNotFoundException();
        }

        return response()->json($source, Response::HTTP_OK);
    }

    public function show(int $id): JsonResponse
    {
        $source = $this->getSourceService->findById($id);

        if (!$source) {
            throw new SourceNotFoundException();
        }

        return response()->json(new SourceResource($source), Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->deleteSourceService->execute($id);

        if (!$deleted) {
            throw new SourceNotFoundException();
        }

        return response()->json(['message' => 'Source deleted successfully'], Response::HTTP_OK);
    }
}
