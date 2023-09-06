<?php

namespace Yng\Exception;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;
use Throwable;

/**
 * 类找不到异常
 */
class ClassNotFoundException extends RuntimeException implements NotFoundExceptionInterface
{
    public function __construct(string $message, protected string $class = '', Throwable $previous = null)
    {
        $this->message = $message;

        parent::__construct($message, 0, $previous);
    }

    /**
     * 获取类名
     * @access public
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }
}
