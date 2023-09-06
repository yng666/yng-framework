<?php
declare(strict_types = 1);

namespace Yng\Contract;

use DateInterval;

/**
 * 缓存驱动接口
 */
interface CacheHandlerInterface
{
    /**
     * 判断缓存
     * @access public
     * @param string $key 缓存变量名
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * 读取缓存
     * @access public
     * @param string $key    缓存变量名
     * @param mixed  $default 默认值
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * 写入缓存
     * @access public
     * @param string            $key   缓存变量名
     * @param mixed             $value  存储数据
     * @param integer|DateInterval $expire 有效时间（秒）
     * @return bool
     */
    public function set(string $key, mixed $value, int|DateInterval $expire = null): bool;

    /**
     * 自增缓存（针对数值缓存）
     * @access public
     * @param string $key 缓存变量名
     * @param int    $step 步长
     * @return false|int
     */
    public function inc(string $key, int $step = 1);

    /**
     * 自减缓存（针对数值缓存）
     * @access public
     * @param string $key 缓存变量名
     * @param int    $step 步长
     * @return false|int
     */
    public function dec(string $key, int $step = 1);

    /**
     * 删除缓存
     * @access public
     * @param string $key 缓存变量名
     * @return bool
     */
    public function delete(string $key): bool;

    /**
     * 清除缓存
     * @access public
     * @return bool
     */
    public function clear(): bool;

    /**
     * 删除缓存标签
     * @access public
     * @param array $keys 缓存标识列表
     * @return void
     */
    public function clearTag(array $keys);
}
