<?php

namespace Yng\Framework\Facades;

use Yng\Framework\Facade;
use Yng\Framework\View\Renderer;

/**
 * @method static render(string $template, array $arguments = [])
 * Class View
 *
 * @package Yng\Framework\Facades
 */
class View extends Facade
{
    protected static function getFacadeClass()
    {
        return Renderer::class;
    }
}
