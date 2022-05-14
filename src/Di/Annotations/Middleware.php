<?php

namespace Yng\Framework\Di\Annotations;

use Yng\Di\Annotations\Annotation;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Middleware extends Annotation
{
    /**
     * @param ...$middleware
     */
    public function __construct(...$middleware)
    {
        $this->value = $middleware;
    }

    /**
     * @return array
     */
    public function handle()
    {
        return $this->value;
    }
}
