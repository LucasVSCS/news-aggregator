<?php

namespace App\Domain\Article\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class ArticleException extends Exception
{
    public function report()
    {
        Log::error('Article exception: ' . $this->getMessage());
        return false;
    }

    public function render($request)
    {
        $statusCode = $this->getCode() ?: 500;

        return response()->json([
            'message' => $this->getMessage(),
        ], $statusCode);
    }
}
