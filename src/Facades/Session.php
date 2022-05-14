<?php
declare(strict_types=1);

namespace Yng\Framework\Facades;

use Yng\Framework\Facade;

/**
 * @method static get(string $name)
 * @method static set(string $name, $value)
 * @method static has(string $name)
 * @method static flash(string $name, $value)
 * @method static destroy() 销毁
 * Class Session
 *
 * @package Yng\Framework\Facades
 */
class Session extends Facade
{
    protected static function getFacadeClass()
    {
        return \Yng\Framework\Http\Session::class;
    }
}
