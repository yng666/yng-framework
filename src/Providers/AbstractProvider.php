<?php
declare(strict_types=1);

namespace Yng\Framework\Providers;

use Yng\Framework\App;
use Yng\Framework\Contracts\ServiceProviderInterface;

abstract class AbstractProvider implements ServiceProviderInterface
{
    /**
     * App
     *
     * @var App
     */
    protected App $app;

    /**
     * Service constructor.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * 服务注册方法
     *
     * @return mixed
     */
    abstract public function register();

    /**
     * 服务启动方法
     *
     * @return mixed
     */
    abstract public function boot();

}
