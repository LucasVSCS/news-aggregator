<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Requests\ArticleSearchRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Application\Services\Article\GetArticleService;
use App\Domain\Article\Exceptions\ArticleNotFoundException;

class ArticleController extends Controller
{
    public function __construct(
        private readonly GetArticleService $getArticleService
    ) {
    }

    /**
     * Search/filter articles
     */
    public function index(ArticleSearchRequest $request): JsonResponse
    {
        $articles = $this->getArticleService->search(
            keyword: $request->keyword,
            sources: $request->sources,
            categories: $request->categories,
            authors: $request->authors,
            dateFrom: $request->date_from,
            dateTo: $request->date_to,
            perPage: $request->per_page ?? 20
        );

        return response()->json([
            'data' => ArticleResource::collection($articles),
            'meta' => [
                'total' => $articles->total(),
                'per_page' => $articles->perPage(),
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
            ]
        ], Response::HTTP_OK);
    }

    /**
     * Get single article
     */
    public function show(int $id): JsonResponse
    {
        $article = $this->getArticleService->findById($id);

        if (!$article) {
            throw new ArticleNotFoundException();
        }

        return response()->json(new ArticleResource($article), Response::HTTP_OK);
    }

    /**
     * Get latest articles
     */
    public function latest(): JsonResponse
    {
        $articles = $this->getArticleService->getLatest(20);

        return response()->json(ArticleResource::collection($articles), Response::HTTP_OK);
    }

    /**
     * Get articles by source
     */
    public function bySource(int $sourceId): JsonResponse
    {
        $articles = $this->getArticleService->getBySource($sourceId);

        return response()->json([
            'data' => ArticleResource::collection($articles),
            'meta' => [
                'total' => $articles->total(),
                'per_page' => $articles->perPage(),
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
            ]
        ], Response::HTTP_OK);
    }

    /**
     * Get articles by category
     */
    public function byCategory(int $categoryId): JsonResponse
    {
        $articles = $this->getArticleService->getByCategory($categoryId);

        return response()->json([
            'data' => ArticleResource::collection($articles),
            'meta' => [
                'total' => $articles->total(),
                'per_page' => $articles->perPage(),
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
            ]
        ], Response::HTTP_OK);
    }

    /**
     * Get unique authors
     */
    public function authors(): JsonResponse
    {
        $authors = $this->getArticleService->getUniqueAuthors();

        return response()->json($authors, Response::HTTP_OK);
    }
}
