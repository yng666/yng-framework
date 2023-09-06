<?php
declare(strict_types = 1);

namespace Yng\Event;

/**
 * LogWrite事件类
 */
class LogWrite
{
    public function __construct(public string $channel, public array $log)
    {
    }
}
