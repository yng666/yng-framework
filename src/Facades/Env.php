<?php
declare(strict_types=1);

namespace Yng\Framework\Facades;

use Yng\Framework\Facade;

/**
 * @method static set(string $env, mixed $value)
 * @method static string get(string $key = null, $default = null)
 * @method static array all()
 * Class Env
 *
 * @package Yng\Framework\Facades
 */
class Env extends Facade
{
    protected static function getFacadeClass()
    {
        return \Yng\Env\Env::class;
    }
}
