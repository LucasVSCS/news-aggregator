<?php

namespace App\Domain\Article\Exceptions;

use App\Domain\Article\Exceptions\ArticleException;

class ArticleNotFoundException extends ArticleException
{
    /**
     * Exception constructor.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($message = 'Article not found.', $code = 404, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
