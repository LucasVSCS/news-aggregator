<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Symfony\Component\HttpFoundation\Response;
use App\Application\Services\Category\GetCategoryService;
use App\Domain\Category\Exceptions\CategoryNotFoundException;

class CategoryController extends Controller
{
    public function __construct(
        private readonly GetCategoryService $getCategoryService
    ) {
    }

    public function index(): JsonResponse
    {
        $categories = $this->getCategoryService->getAll();

        return response()->json(CategoryResource::collection($categories), Response::HTTP_OK);
    }

    public function show(int $id): JsonResponse
    {
        $category = $this->getCategoryService->findById($id);

        if (!$category) {
            throw new CategoryNotFoundException();
        }

        return response()->json(new CategoryResource($category), Response::HTTP_OK);
    }
}
