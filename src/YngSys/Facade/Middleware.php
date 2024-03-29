<?php
declare(strict_types = 1);

namespace Yng\Facade;

use Yng\Facade;

/**
 * @see \Yng\Middleware
 * @package Yng\Facade
 * @mixin \Yng\Middleware
 * @method static void import(array $middlewares = [], string $type = 'global') 导入中间件
 * @method static void add(mixed $middleware, string $type = 'global') 注册中间件
 * @method static void route(mixed $middleware) 注册路由中间件
 * @method static void controller(mixed $middleware) 注册控制器中间件
 * @method static mixed unshift(mixed $middleware, string $type = 'global') 注册中间件到开始位置
 * @method static array all(string $type = 'global') 获取注册的中间件
 * @method static Pipeline pipeline(string $type = 'global') 调度管道
 * @method static mixed end(\Yng\Response $response) 结束调度
 * @method static \Yng\Response handleException(\Yng\Request $passable, \Throwable $e) 异常处理
 */
class Middleware extends Facade
{
    /**
     * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'middleware';
    }
}
