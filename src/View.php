<?php

namespace Yng\Framework;

use Yng\Config\Repository;
use Yng\Framework\Http\Response;
use Yng\View\Renderer;

/**
 * @class   View
 * @author  Yng
 * @date    2022/1/3
 * @time    13:44
 * @package Yng\Framework\View
 */
class View extends Renderer
{
    /**
     * @param Repository $repository
     *
     * @return static
     */
    public static function __new(Repository $repository)
    {
        $config  = $repository->get('view');
        $engine  = $config['engine'];
        $options = $config['options'];
        $engine  = new $engine($options);
        return new static($engine);
    }

    /**
     * @param string $template
     * @param array  $arguments
     *
     * @return false|Response|string
     * @throws \Yng\Framework\Exceptions\HttpException
     * @throws \Throwable
     */
    public function render(string $template, array $arguments = [])
    {
        ob_start();
        echo parent::render($template, $arguments);
        $content = ob_get_clean();

        return Response::make($content);
    }
}
