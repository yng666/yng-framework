<?php
declare(strict_types=1);

namespace Yng\Framework;

/**
 * 门面实现基类
 * Class Facade
 *
 * @package Yng
 */
abstract class Facade
{
    /**
     * 重新实例化
     *
     * @var bool
     */
    protected static $renew = false;

    /**
     * 方法注入设置属性，true时所有Facade调用的方法都支持依赖注入
     *
     * @var bool
     */
    protected static $methodInjection = false;

    /**
     * 构造函数参数列表
     *
     * @var array
     */
    protected static $constructorArguments = [];

    /**
     * 获取当前Facade对应类名
     *
     * @access protected
     * @return string
     */
    abstract protected static function getFacadeClass();

    /**
     * 创建Facade实例
     *
     * @return mixed
     */
    final protected static function createFacade()
    {
        $params = [static::getFacadeClass(), static::$constructorArguments];
        if (static::$renew) {
            return resolve(...$params);
        }
        return make(...$params);
    }

    /**
     * 调用实际类的方法,默认支持依赖注入
     *
     * @param string $method
     * @param array  $params
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $params)
    {
        $facade = static::createFacade();
        if (static::$methodInjection) {
            return invoke([$facade, $method], $params);
        }
        return $facade->{$method}(...$params);
    }
}
