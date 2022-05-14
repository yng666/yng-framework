<?php
declare(strict_types=1);

namespace Yng\Framework;

/**
 * 基础控制器
 * Class Controller
 *
 * @package Yng\Framework
 */
abstract class Controller
{
    /**
     * 控制器中间件列表
     *
     * @var array
     */
    protected array $middleware = [];

    /**
     * 设置中间件
     *
     * @param string|\Closure|object $middleware
     * @param                        $action
     */
    final public function middleware($middleware, array $only = [], array $except = [])
    {
        if ($middleware instanceof \Closure || is_object($middleware)) {
            $this->middleware[] = $middleware;
        } else {
            $this->middleware[$middleware] = [
                'only'   => (empty($except)) ? $only : [],
                'except' => $except
            ];
        }

        return $this;
    }

}
