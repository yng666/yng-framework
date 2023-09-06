<?php
declare(strict_types = 1);

namespace Yng\Event;

/**
 * LogRecord事件类
 */
class LogRecord
{
    /** @var string */
    public $type;

    /** @var string */
    public $message;

    public function __construct($type, $message)
    {
        $this->type    = $type;
        $this->message = $message;
    }
}
