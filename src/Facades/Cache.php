<?php

namespace Yng\Framework\Facades;

use Yng\Framework\Facade;
use Psr\SimpleCache\CacheInterface;

/**
 * @method static string get(string $key)
 * @method static bool has(string $key)
 * @method static bool set(string $key, string $value, int $ttl = null)
 * @method static bool delete(string $key)
 * @method static bool clear()
 * @method static mixed remember($key, \Closure $value, $ttl = null) 自动刷新缓存并返回缓存
 * @method static incr($key, $step = 1)
 * @method static decr($key, $step = 1)
 * @method static array getMultiple($keys, $default = null) 获取多个值
 * @method static bool setMultiple($values, $ttl = null) 设置多个
 * @method static bool deleteMultiple($keys) 删除多个
 * @method static CacheInterface store($store = 'default') 获取一个存储
 * Class Cache
 * @package Yng\Framework\Facades
 */
class Cache extends Facade
{
    protected static function getFacadeClass()
    {
        return CacheInterface::class;
    }
}
