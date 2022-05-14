<?php

namespace Yng\Framework\Facades;

use Yng\Framework\Facade;

class Filesystem extends Facade
{
    protected static function getFacadeClass()
    {
        return \Yng\Utils\Filesystem::class;
    }
}
