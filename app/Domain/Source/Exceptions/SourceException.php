<?php

namespace App\Domain\Source\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class SourceException extends Exception
{
    public function report()
    {
        Log::error('Source exception: ' . $this->getMessage());
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
