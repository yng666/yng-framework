<?php

namespace Yng\Framework;

class Cache extends \Yng\Cache\Cache
{
    /**
     * 记住缓存并返回
     *
     * @param          $key
     * @param \Closure $callback
     * @param int|null $ttl
     *
     * @return mixed|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function remember($key, \Closure $callback, ?int $ttl = null)
    {
        if (!$this->has($key)) {
            $this->set($key, $callback(), $ttl);
        }
        return $this->get($key);
    }

    /**
     * 自增
     *
     * @param     $key
     * @param int $step
     *
     * @return bool
     */
    public function incr($key, int $step = 1)
    {
        return (bool)$this->set($key, (int)$this->get($key) + $step);
    }

    /**
     * 自减去
     *
     * @param     $key
     * @param int $step
     *
     * @return bool
     */
    public function decr($key, int $step = 1)
    {
        return $this->incr($key, -$step);
    }

    /**
     * 取出并删除
     *
     * @param $key
     *
     * @return mixed|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function pull($key)
    {
        $value = $this->get($key);
        $this->delete($key);
        return $value;
    }

    /**
     * @param       $method
     * @param array $args
     *
     * @return mixed
     */
    public function __call($method, array $args)
    {
        return $this->cache->{$method}(...$args);
    }
}
