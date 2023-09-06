<?php
declare(strict_types = 1);

namespace Yng;

use Closure;
use Yng\Event\RouteLoaded;

/**
 * 系统服务基础类
 * @method void register()
 * @method void boot()
 */
abstract class Service
{
    public function __construct(protected App $app)
    {
    }

    /**
     * 加载路由
     * @access protected
     * @param string $path 路由路径
     */
    protected function loadRoutesFrom(string $path)
    {
        $this->registerRoutes(function () use ($path) {
            include $path;
        });
    }

    /**
     * 注册路由
     * @param Closure $closure
     */
    protected function registerRoutes(Closure $closure)
    {
        $this->app->event->listen(RouteLoaded::class, $closure);
    }

    /**
     * 添加指令
     * @access protected
     * @param array|string $commands 指令
     */
    protected function commands($commands)
    {
        $commands = is_array($commands) ? $commands : func_get_args();

        Console::starting(function (Console $console) use ($commands) {
            $console->addCommands($commands);
        });
    }
}
