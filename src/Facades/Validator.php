<?php

namespace Yng\Framework\Facades;

use Yng\Framework\Facade;

/**
 * @method static \Yng\Validator\Validator make(array $data, array $rules = [], array $message = [])
 */
class Validator extends Facade
{

    protected static $renew = true;

    protected static function getFacadeClass()
    {
        return \Yng\Validator\Validator::class;
    }
}
