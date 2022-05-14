<?php
declare(strict_types=1);

namespace Yng\Framework\Facades;

use Yng\Framework\Facade;
use Yng\Routing\Route as baseRoute;
use Yng\Routing\RouteCollector;
use Yng\Routing\Router;

/**
 * @method static baseRoute get(string $uri, array|string|\Closure $action) GET方式请求的路由
 * @method static baseRoute post(string $uri, array|string|\Closure $action)
 * @method static baseRoute put(string $uri, array|string|\Closure $action)
 * @method static baseRoute patch(string $uri, array|string|\Closure $action)
 * @method static baseRoute delete(string $uri, array|string|\Closure $action)
 * @method static baseRoute request(string $uri, array|string|\Closure $action, array $type = ['get', 'post'])
 * @method static Router prefix(string $prefix)
 * @method static Router middleware(string $prefix)
 * @method static Router controller(string $controller)
 * @method static Router namespace(string $namespace)
 * @method static Router patterns(array $patterns)
 * @method static group(\Closure $group) 分组路由
 * Class Route
 *
 * @package \Yng\Framework\Facade
 */
class Route extends Facade
{
    protected static function getFacadeClass()
    {
        return Router::class;
    }

    public static function __callStatic($method, $params)
    {
        return RouteCollector::$router->{$method}(...$params);
    }
}
