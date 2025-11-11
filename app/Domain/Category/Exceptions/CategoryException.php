<?php

namespace App\Domain\Category\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class CategoryException extends Exception
{
    public function report()
    {
        Log::error('Category exception: ' . $this->getMessage());
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
