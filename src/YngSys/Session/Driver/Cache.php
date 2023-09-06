<?php
declare(strict_types = 1);

namespace Yng\Session\Driver;

use Psr\SimpleCache\CacheInterface;
use Yng\Contract\SessionHandlerInterface;
use Yng\Helper\Arr;

class Cache implements SessionHandlerInterface
{

    /** @var CacheInterface */
    protected $handler;

    /** @var integer */
    protected $expire;

    /** @var string */
    protected $prefix;

    public function __construct(\Yng\Cache $cache, array $config = [])
    {
        $this->handler = $cache->store(Arr::get($config, 'store'));
        $this->expire  = Arr::get($config, 'expire', 1440);
        $this->prefix  = Arr::get($config, 'prefix', '');
    }

    public function read(string $sessionId): string
    {
        return (string) $this->handler->get($this->prefix . $sessionId);
    }

    public function delete(string $sessionId): bool
    {
        return $this->handler->delete($this->prefix . $sessionId);
    }

    public function write(string $sessionId, string $data): bool
    {
        return $this->handler->set($this->prefix . $sessionId, $data, $this->expire);
    }
}
