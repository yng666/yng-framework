<?php
declare (strict_types = 1);

namespace Yng\Exception;

use Yng\Response;

/**
 * HTTP响应异常
 */
class HttpResponseException extends \RuntimeException
{
    public function __construct(protected Response $response)
    {
    }

    public function getResponse()
    {
        return $this->response;
    }

}
