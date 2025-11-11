<?php

namespace App\Domain\Category\Exceptions;

use App\Domain\Category\Exceptions\CategoryException;

class CategoryNotFoundException extends CategoryException
{
    /**
     * Exception constructor.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($message = 'Category not found.', $code = 404, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
