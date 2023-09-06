<?php
declare(strict_types = 1);

namespace Yng;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Stringable;
use Yng\Event\LogWrite;
use Yng\Helper\Arr;
use Yng\Log\Channel;
use Yng\Log\ChannelSet;

/**
 * 日志管理类
 * @package Yng
 * @mixin Channel
 */
class Log extends Manager implements LoggerInterface
{
    use LoggerTrait;
    const EMERGENCY = 'emergency';//系统错误级别
    const ALERT     = 'alert';//致命警告级别
    const CRITICAL  = 'critical';//紧急情况、需要立刻进行修复、程序组件不可用
    const ERROR     = 'error';//错误级别
    const WARNING   = 'warning';//非致命警告级别
    const NOTICE    = 'notice';//轻微提示
    const INFO      = 'info';//信息级别
    const DEBUG     = 'debug';//调试级别
    const SQL       = 'sql';//sql语句

    protected $namespace = '\\Yng\\Log\\Driver\\';

    /**
     * 默认驱动
     * @return string|null
     */
    public function getDefaultDriver(): ?string
    {
        return $this->getConfig('default');
    }

    /**
     * 获取日志配置
     * @access public
     * @param null|string $name    名称
     * @param mixed       $default 默认值
     * @return mixed
     */
    public function getConfig(string $name = null, $default = null)
    {
        if (!is_null($name)) {
            return $this->app->config->get('log.' . $name, $default);
        }

        return $this->app->config->get('log');
    }

    /**
     * 获取渠道配置
     * @param string $channel
     * @param string $name
     * @param mixed  $default
     * @return array
     */
    public function getChannelConfig(string $channel, string $name = null, $default = null)
    {
        if ($config = $this->getConfig("channels.{$channel}")) {
            return Arr::get($config, $name, $default);
        }

        throw new InvalidArgumentException("Channel [$channel] not found.");
    }

    /**
     * driver()的别名
     * @param string|array $name 渠道名
     * @return Channel|ChannelSet
     */
    public function channel(string|array $name = null)
    {
        if (is_array($name)) {
            return new ChannelSet($this, $name);
        }

        return $this->driver($name);
    }

    protected function resolveType(string $name)
    {
        return $this->getChannelConfig($name, 'type', 'file');
    }

    public function createDriver(string $name)
    {
        $driver = parent::createDriver($name);

        $lazy = !$this->getChannelConfig($name, "realtime_write", false) && !$this->app->runningInConsole();
        $allow = array_merge($this->getConfig("level", []), $this->getChannelConfig($name, "level", []));

        return new Channel($name, $driver, $allow, $lazy, $this->app->event);
    }

    protected function resolveConfig(string $name)
    {
        return $this->getChannelConfig($name);
    }

    /**
     * 清空日志信息
     * @access public
     * @param string|array $channel 日志通道名
     * @return $this
     */
    public function clear(string|array $channel = '*')
    {
        if ('*' == $channel) {
            $channel = array_keys($this->drivers);
        }

        $this->channel($channel)->clear();

        return $this;
    }

    /**
     * 关闭本次请求日志写入
     * @access public
     * @param string|array $channel 日志通道名
     * @return $this
     */
    public function close(string|array $channel = '*')
    {
        if ('*' == $channel) {
            $channel = array_keys($this->drivers);
        }

        $this->channel($channel)->close();

        return $this;
    }

    /**
     * 获取日志信息
     * @access public
     * @param string $channel 日志通道名
     * @return array
     */
    public function getLog(string $channel = null): array
    {
        return $this->channel($channel)->getLog();
    }

    /**
     * 保存日志信息
     * @access public
     * @return bool
     */
    public function save(): bool
    {
        /** @var Channel $channel */
        foreach ($this->drivers as $channel) {
            $channel->save();
        }

        return true;
    }

    /**
     * 记录日志信息
     * @access public
     * @param mixed  $msg     日志信息
     * @param string $type    日志级别
     * @param array  $context 替换内容
     * @param bool   $lazy
     * @return $this
     */
    public function record($msg, string $type = 'info', array $context = [], bool $lazy = true)
    {
        $channel = $this->getConfig('type_channel.' . $type);

        $this->channel($channel)->record($msg, $type, $context, $lazy);

        return $this;
    }

    /**
     * 实时写入日志信息
     * @access public
     * @param mixed  $msg     调试信息
     * @param string $type    日志级别
     * @param array  $context 替换内容
     * @return $this
     */
    public function write($msg, string $type = 'info', array $context = [])
    {
        return $this->record($msg, $type, $context, false);
    }

    /**
     * 注册日志写入事件监听
     * @param $listener
     * @return Event
     */
    public function listen($listener)
    {
        return $this->app->event->listen(LogWrite::class, $listener);
    }

    /**
     * 记录日志信息
     * @access public
     * @param mixed $level   日志级别
     * @param string|Stringable   $message 日志信息
     * @param array  $context 替换内容
     * @return void
     */
    public function log($level, string|Stringable $message, array $context = []): void
    {
        $this->record($message, $level, $context);
    }

    /**
     * 记录sql信息
     * @access public
     * @param string|Stringable  $message 日志信息
     * @param array $context 替换内容
     * @return void
     */
    public function sql(string|Stringable $message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    public function __call($method, $parameters)
    {
        $this->log($method, ...$parameters);
    }
}
