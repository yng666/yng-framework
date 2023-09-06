<?php
declare (strict_types = 1);

namespace Yng\Exception;

use Exception;

/**
 * HTTP异常
 */
class HttpException extends \RuntimeException
{
    public function __construct(private int $statusCode, string $message = '', Exception $previous = null, private array $headers = [], $code = 0)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}
