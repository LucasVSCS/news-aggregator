<?php

namespace App\Domain\Source\Exceptions;

use App\Exceptions\Source\SourceException;

class SourceNotFoundException extends SourceException
{
    /**
     * Exception constructor.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($message = 'Source not found.', $code = 404, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
