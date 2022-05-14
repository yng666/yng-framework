<?php
declare(strict_types=1);

namespace Yng\Framework\Facades;

use Yng\Framework\Facade;
use Psr\Container\ContainerInterface;

/**
 * @method static get(string $className) 获取实例化后的对象
 * @method static has(string $className) 判断实例是否存在
 * @method static set(string $className, object $concrete) 设置一个实例
 * @method static bind(string $id, \Closure $closure) 绑定闭包[闭包需要返回相应实例]
 * @method static make(string $abstract, array $arguments = []) 使用容器实例化类
 * @method static resolve(string $abstract, array $arguments = []) 使用容器实例化类
 * @method static resolving(\Closure $closure, string $id) 绑定类实例化事件
 * @method static remove(string $abstract) 注销实例
 * @method static invokeMethod(array $callable, array $arguments = [], bool $renew = false, array $constructorParameters = []) 对调用的方法实现依赖注入
 * Class App
 *
 * @package Yng\Framework\Facades
 */
class App extends Facade
{
    protected static function getFacadeClass()
    {
        return ContainerInterface::class;
    }
}
