<?php
declare(strict_types = 1);

namespace Yng\Log;

use Yng\Log;

/**
 * 设置log驱动
 * @package Yng\Log
 * @mixin Channel
 */
class ChannelSet
{
    public function __construct(protected Log $log, protected array $channels)
    {
    }

    public function __call($method, $arguments)
    {
        foreach ($this->channels as $channel) {
            $this->log->channel($channel)->{$method}(...$arguments);
        }
    }
}
